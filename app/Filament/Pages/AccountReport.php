<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use App\Models\BankAccount;
use App\Models\Transaction;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;

class AccountReport extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.account-report';
    protected static string | \UnitEnum | null $navigationGroup = 'Reports';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?int $navigationSort = 3;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'year' => date('Y'),
            'month' => date('n'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('bank_account_id')
                            ->label('Bank Account')
                            ->options(BankAccount::pluck('name', 'id'))
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('month')
                            ->options([
                                1 => 'January',
                                2 => 'February',
                                3 => 'March',
                                4 => 'April',
                                5 => 'May',
                                6 => 'June',
                                7 => 'July',
                                8 => 'August',
                                9 => 'September',
                                10 => 'October',
                                11 => 'November',
                                12 => 'December',
                            ])
                            ->required(),
                        Select::make('year')
                            ->options(array_combine(range(date('Y'), date('Y') - 5), range(date('Y'), date('Y') - 5)))
                            ->required(),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function filter(): void
    {
        $this->form->getState();
    }

    public function getViewData(): array
    {
        $data = $this->data;
        $transactions = collect();
        $receiptTypeStats = collect();
        $paymentChannelStats = collect();

        if (!empty($data['bank_account_id'])) {
            $query = Transaction::query()
                ->where('bank_account_id', $data['bank_account_id'])
                ->whereYear('date', $data['year'])
                ->whereMonth('date', $data['month']);

            $transactions = $query->with(['items.receiptType', 'paymentChannel'])->get();

            // Clone query for stats to avoid issues if $query is modified (though ->get() doesn't modify)
            $receiptTypeStats = Transaction::query()
                ->where('bank_account_id', $data['bank_account_id'])
                ->whereYear('date', $data['year'])
                ->whereMonth('date', $data['month'])
                ->join('transaction_items', 'transactions.id', '=', 'transaction_items.transaction_id')
                ->whereNotNull('transaction_items.receipt_type_id')
                ->join('receipt_types', 'transaction_items.receipt_type_id', '=', 'receipt_types.id')
                ->selectRaw('receipt_types.name as name, sum(transaction_items.amount) as total')
                ->groupBy('receipt_types.name')
                ->get();

            $paymentChannelStats = Transaction::query()
                ->where('bank_account_id', $data['bank_account_id'])
                ->whereYear('date', $data['year'])
                ->whereMonth('date', $data['month'])
                ->whereNotNull('payment_channel_id')
                ->join('payment_channels', 'transactions.payment_channel_id', '=', 'payment_channels.id')
                ->selectRaw('payment_channels.name as name, sum(amount) as total')
                ->groupBy('payment_channels.name')
                ->get();
        }

        return [
            'transactions' => $transactions,
            'receiptTypeStats' => $receiptTypeStats,
            'paymentChannelStats' => $paymentChannelStats,
        ];
    }

    public function print()
    {
        // Simple print using browser print via JS in view, or we open a specific print route.
        // For simplicity, we dispatch an event to the browser window.
        $this->dispatch('print-report');
    }
}
