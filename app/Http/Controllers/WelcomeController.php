<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): View
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-t'));

        // Scrolling text: Last Updated
        $lastUpdate = Transaction::latest('updated_at')->first()?->updated_at?->format('d M Y H:i') ?? '-';

        // 1. Existing functionality: Bank Accounts List & Tabs
        $bankAccounts = BankAccount::withSum(['transactions as total_reception' => function ($query) use ($startDate, $endDate) {
            $query->where('type', 'credit')
                ->whereBetween('date', [$startDate, $endDate]);
        }], 'amount')->get();

        $bankDetails = BankAccount::with(['transactions' => function ($query) use ($startDate, $endDate) {
            $query->with(['items.receiptType', 'paymentChannel'])
                ->where('type', 'credit')
                ->whereBetween('date', [$startDate, $endDate])
                ->latest('date');
        }])->get();

        // 2. Report Functionality (Laporan)
        $reports = [];
        foreach ($bankAccounts as $bank) {
            // Aggregate by Receipt Type (from items)
            $receiptTypesReport = TransactionItem::whereHas('transaction', function ($q) use ($bank, $startDate, $endDate) {
                $q->where('bank_account_id', $bank->id)
                    ->where('type', 'credit')
                    ->whereBetween('date', [$startDate, $endDate]);
            })
                ->join('receipt_types', 'transaction_items.receipt_type_id', '=', 'receipt_types.id')
                ->select('receipt_types.name', DB::raw('SUM(transaction_items.amount) as total'))
                ->groupBy('receipt_types.name')
                ->get();

            // Aggregate by Payment Channel (from transactions)
            $paymentChannelsReport = Transaction::where('bank_account_id', $bank->id)
                ->where('type', 'credit')
                ->whereBetween('date', [$startDate, $endDate])
                ->join('payment_channels', 'transactions.payment_channel_id', '=', 'payment_channels.id')
                ->select('payment_channels.name', DB::raw('SUM(transactions.amount) as total'))
                ->groupBy('payment_channels.name')
                ->get();

            $reports[] = [
                'bank' => $bank,
                'receipt_types' => $receiptTypesReport,
                'payment_channels' => $paymentChannelsReport,
            ];
        }

        return view('welcome', [
            'bankAccounts' => $bankAccounts,
            'bankDetails' => $bankDetails,
            'reports' => $reports,
            'lastUpdate' => $lastUpdate,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * Download CSV for a specific bank and date range.
     */
    public function downloadCsv(Request $request, BankAccount $bank): StreamedResponse
    {
        $startDate = $request->query('start_date', date('Y-m-01'));
        $endDate = $request->query('end_date', date('Y-m-t'));

        $transactions = Transaction::with(['items.receiptType', 'paymentChannel'])
            ->where('bank_account_id', $bank->id)
            ->where('type', 'credit')
            ->whereBetween('date', [$startDate, $endDate])
            ->latest('date')
            ->get();

        $filename = "Laporan_Penerimaan_{$bank->name}_{$startDate}_sd_{$endDate}.csv";

        return new StreamedResponse(function () use ($transactions) {
            $handle = fopen('php://output', 'w');

            // Add BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($handle, ['Tanggal', 'Keterangan', 'Jenis Penerimaan', 'Kanal Pembayaran', 'Nominal']);

            foreach ($transactions as $tx) {
                $types = $tx->items->map(fn($i) => $i->receiptType?->name)->filter()->unique()->join('; ');
                fputcsv($handle, [
                    $tx->date->format('d/m/Y'),
                    $tx->description,
                    $types ?: 'Lainnya',
                    $tx->paymentChannel?->name ?? 'Cash',
                    $tx->amount
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
