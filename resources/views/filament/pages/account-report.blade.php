<x-filament-panels::page>
    <form wire:submit="filter">
        {{ $this->form }}

        <div class="flex justify-end mt-4 gap-2">
            <x-filament::button type="submit" wire:target="filter">
                Filter Data
            </x-filament::button>

            <x-filament::button color="gray" wire:click="print" icon="heroicon-o-printer">
                Print Report
            </x-filament::button>
        </div>
    </form>

    @php
        $viewData = $this->getViewData();
    @endphp

    @if ($this->data['bank_account_id'] ?? null)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <x-filament::section>
                <x-slot name="heading">Receipt Types Summary</x-slot>

                <div class="space-y-2">
                    @forelse($viewData['receiptTypeStats'] as $stat)
                        <div wire:key="receipt-type-{{ str($stat->name)->slug() }}"
                            class="flex justify-between p-2 border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span>{{ $stat->name }}</span>
                            <span class="font-bold text-success-600">Rp {{ number_format($stat->total, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No data available</div>
                    @endforelse
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Payment Channels Summary</x-slot>

                <div class="space-y-2">
                    @forelse($viewData['paymentChannelStats'] as $stat)
                        <div wire:key="payment-channel-{{ str($stat->name)->slug() }}"
                            class="flex justify-between p-2 border-b last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <span>{{ $stat->name }}</span>
                            <span class="font-bold text-warning-600">Rp {{ number_format($stat->total, 2) }}</span>
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">No data available</div>
                    @endforelse
                </div>
            </x-filament::section>
        </div>

        <x-filament::section class="mt-6">
            <x-slot name="heading">Transaction Details</x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3">Date</th>
                            <th class="px-6 py-3">Description</th>
                            <th class="px-6 py-3">Type</th>
                            <th class="px-6 py-3 text-right">Amount</th>
                            <th class="px-6 py-3">Category</th>
                            <th class="px-6 py-3">Channel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($viewData['transactions'] as $transaction)
                            @foreach ($transaction->items as $index => $item)
                                <tr wire:key="transaction-{{ $transaction->id }}-item-{{ $item->id }}"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    @if ($index === 0)
                                        <td class="px-6 py-4" rowspan="{{ $transaction->items->count() ?: 1 }}">
                                            {{ $transaction->date->format('d M Y') }}</td>
                                    @endif
                                    <td class="px-6 py-4">{{ $item->description }}</td>
                                    @if ($index === 0)
                                        <td class="px-6 py-4" rowspan="{{ $transaction->items->count() ?: 1 }}">
                                            <x-filament::badge
                                                color="{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                                {{ ucfirst($transaction->type) }}
                                            </x-filament::badge>
                                        </td>
                                    @endif
                                    <td class="px-6 py-4 text-right">Rp {{ number_format($item->amount, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">{{ $item->receiptType?->name ?? '-' }}</td>
                                    @if ($index === 0)
                                        <td class="px-6 py-4" rowspan="{{ $transaction->items->count() ?: 1 }}">
                                            {{ $transaction->paymentChannel?->name ?? '-' }}</td>
                                    @endif
                                </tr>
                            @endforeach
                            @if ($transaction->items->isEmpty())
                                <tr wire:key="transaction-{{ $transaction->id }}-no-items"
                                    class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                    <td class="px-6 py-4">{{ $transaction->date->format('d M Y') }}</td>
                                    <td class="px-6 py-4">{{ $transaction->description }} (No Details)</td>
                                    <td class="px-6 py-4">
                                        <x-filament::badge
                                            color="{{ $transaction->type === 'credit' ? 'success' : 'danger' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-6 py-4 text-right">Rp
                                        {{ number_format($transaction->amount, 2, ',', '.') }}</td>
                                    <td class="px-6 py-4">-</td>
                                    <td class="px-6 py-4">{{ $transaction->paymentChannel?->name ?? '-' }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center">No transactions found for this period.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <script>
            window.addEventListener('print-report', event => {
                window.print();
            });
        </script>

        <style media="print">
            @page {
                size: landscape;
                margin: 1cm;
            }

            body * {
                visibility: hidden;
            }

            .fi-main-ctn,
            .fi-main-ctn * {
                visibility: visible;
            }

            .fi-main-ctn {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .fi-topbar,
            .fi-sidebar {
                display: none !important;
            }

            button {
                display: none !important;
            }

            input,
            select {
                border: none !important;
                appearance: none !important;
            }
        </style>
    @else
        <div class="mt-6 text-center text-gray-500">
            Please select a bank account and period to view the report.
        </div>
    @endif
</x-filament-panels::page>
