<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>POSH - Dashboard Keuangan</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        [x-cloak] {
            display: none !important;
        }

        .marquee {
            overflow: hidden;
            white-space: nowrap;
            position: relative;
        }

        .marquee-content {
            display: inline-block;
            animation: marquee 60s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Custom form styling for date inputs
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5);
            cursor: pointer;
        } */
    </style>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="antialiased" x-data="{ mainPage: 'monitoring', activeTab: {{ $bankAccounts->first()?->id ?? 0 }} }">
    <div class="min-h-screen">
        <!-- Marquee Last Update -->
        <div class="bg-orange-700 text-white py-2 marquee">
            <div class="marquee-content font-semibold text-sm">
                Informasi: Data transaksi terakhir diperbarui pada {{ $lastUpdate }}. Gunakan Advance Filter untuk
                rentang waktu spesifik. &nbsp; &nbsp; • &nbsp; &nbsp;
                Sistem Monitoring POSH (Penerimaan Terpusat) &nbsp; &nbsp; • &nbsp; &nbsp; Data terupdate:
                {{ $lastUpdate }}
            </div>
        </div>

        <!-- Navigation -->
        <nav class="glass sticky top-0 z-50 px-6 py-4 flex justify-between items-center shadow-sm">
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-orange-700 rounded-xl flex items-center justify-center shadow-lg shadow-orange-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-slate-900 tracking-tight cursor-pointer"
                        @click="mainPage = 'monitoring'">POSH</span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <button @click="mainPage = 'monitoring'"
                        :class="mainPage === 'monitoring' ? 'text-orange-700 font-bold' : 'text-slate-500 hover:text-slate-900'"
                        class="text-sm transition-all">Monitoring</button>
                    <button @click="mainPage = 'laporan'"
                        :class="mainPage === 'laporan' ? 'text-orange-700 font-bold' : 'text-slate-500 hover:text-slate-900'"
                        class="text-sm transition-all">Laporan</button>
                </div>
            </div>
            <a href="{{ url('/admin/login') }}"
                class="px-5 py-2.5 bg-slate-900 text-white font-semibold rounded-xl hover:bg-slate-800 transition-all active:scale-95 shadow-lg shadow-slate-200">
                Admin Portal
            </a>
        </nav>

        <main class="max-w-7xl mx-auto px-6 py-12">
            <!-- Advance Filter Section -->
            <div class="mb-12 bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-orange-50 text-orange-700 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Advance Filter</h2>
                        <p class="text-slate-500 text-sm">Tentukan rentang tanggal laporan secara fleksibel.</p>
                    </div>
                </div>

                <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row items-end gap-4">
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Dari
                            Tanggal</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-orange-500 transition-all h-12 px-4">
                    </div>
                    <div class="flex-1 w-full">
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 ml-1">Sampai
                            Tanggal</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="w-full bg-slate-50 border-none rounded-xl text-sm font-semibold text-slate-700 focus:ring-2 focus:ring-orange-500 transition-all h-12 px-4">
                    </div>
                    <button type="submit"
                        class="w-full md:w-auto h-12 bg-orange-700 text-white px-8 rounded-xl text-sm font-bold shadow-lg shadow-orange-200 hover:bg-orange-800 hover:-translate-y-0.5 transition-all">
                        Tampilkan Data
                    </button>
                    <a href="{{ route('home') }}"
                        class="w-full md:w-auto h-12 flex items-center justify-center bg-slate-100 text-slate-600 px-6 rounded-xl text-sm font-bold hover:bg-slate-200 transition-all">
                        Reset
                    </a>
                </form>
            </div>

            <!-- Page 1: Monitoring -->
            <div x-show="mainPage === 'monitoring'" x-cloak x-transition>
                <div class="mb-12">
                    <h1 class="text-4xl font-bold text-slate-900 mb-2">Monitoring Penerimaan</h1>
                    <p class="text-slate-500 text-lg">Periode: <span
                            class="font-bold text-slate-900">{{ date('d M Y', strtotime($startDate)) }}</span> s/d <span
                            class="font-bold text-slate-900">{{ date('d M Y', strtotime($endDate)) }}</span></p>
                </div>

                <!-- Grid Lists -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
                    @foreach ($bankAccounts as $bank)
                        <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100 hover:shadow-xl transition-all duration-300 group cursor-pointer"
                            @click="activeTab = {{ $bank->id }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="p-3 bg-slate-50 rounded-2xl group-hover:bg-orange-50 transition-colors">
                                    <svg class="w-6 h-6 text-slate-400 group-hover:text-orange-600" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-xs font-bold text-slate-400 uppercase tracking-widest">{{ $bank->bank_name }}</span>
                            </div>
                            <h3 class="text-lg font-bold text-slate-900 mb-1">{{ $bank->name }}</h3>
                            <p class="text-sm text-slate-400 mb-4">{{ $bank->account_number }}</p>
                            <div class="pt-4 border-t border-slate-50">
                                <p class="text-xs font-semibold text-slate-400 uppercase mb-1">Total Penerimaan</p>
                                <p class="text-2xl font-bold text-orange-700">Rp
                                    {{ number_format($bank->total_reception ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Tabs: Detailed Table -->
                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 pr-6">
                        <div class="flex overflow-x-auto px-4">
                            @foreach ($bankAccounts as $bank)
                                <button @click="activeTab = {{ $bank->id }}"
                                    :class="activeTab === {{ $bank->id }} ? 'text-orange-700 border-orange-700 bg-white' :
                                        'text-slate-500 border-transparent hover:text-slate-700'"
                                    class="px-6 py-4 text-sm font-bold border-b-2 transition-all whitespace-nowrap outline-none">
                                    {{ $bank->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    <div class="p-6">
                        @foreach ($bankDetails as $bank)
                            <div x-show="activeTab === {{ $bank->id }}" x-cloak
                                x-data="{ page: 1, perPage: 10, total: {{ $bank->transactions->count() }} }"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-4">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-separate border-spacing-0">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                                    Tanggal</th>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                                    Jenis Penerimaan</th>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 border-r-0">
                                                    Kanal Pembayaran</th>
                                                <th
                                                    class="px-4 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100 text-right">
                                                    Jumlah (Rp)</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @forelse($bank->transactions as $tx)
                                                <tr class="hover:bg-slate-50/50 transition-colors"
                                                    x-show="{{ $loop->index }} >= (page - 1) * perPage && {{ $loop->index }} < page * perPage">
                                                    <td class="px-4 py-4 text-sm text-slate-600 font-medium">
                                                        {{ $tx->date->format('d/m/Y') }}</td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        @foreach ($tx->items->map(fn($i) => $i->receiptType?->name)->filter()->unique() as $type)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100">{{ $type }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">{{ $tx->paymentChannel?->name ?? 'Cash' }}</span>
                                                    </td>
                                                    <td class="px-4 py-4 text-sm font-bold text-slate-900 text-right">
                                                         {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5"
                                                        class="px-4 py-12 text-center text-slate-400 italic">Tidak ada
                                                        data untuk periode ini.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination Controls -->
                                <template x-if="total > perPage">
                                    <div class="mt-8 flex items-center justify-between px-4 py-3 bg-slate-50 rounded-2xl border border-slate-100">
                                        <div class="flex flex-1 justify-between sm:hidden">
                                            <button @click="page--" :disabled="page <= 1"
                                                class="relative inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-slate-700 border border-slate-200 disabled:opacity-50">Previous</button>
                                            <button @click="page++" :disabled="page * perPage >= total"
                                                class="relative ml-3 inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-slate-700 border border-slate-200 disabled:opacity-50">Next</button>
                                        </div>
                                        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                            <div>
                                                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                                                    Menampilkan <span class="font-bold text-slate-900" x-text="(page - 1) * perPage + 1"></span> sampai <span class="font-bold text-slate-900" x-text="Math.min(page * perPage, total)"></span> dari <span class="font-bold text-slate-900" x-text="total"></span> hasil
                                                </p>
                                            </div>
                                            <div>
                                                <nav class="isolate inline-flex -space-x-px gap-2" aria-label="Pagination">
                                                    <button @click="page--" :disabled="page <= 1"
                                                        class="relative inline-flex items-center rounded-xl bg-white p-2 text-slate-400 hover:bg-slate-50 focus:z-20 disabled:opacity-50 transition-all border border-slate-200">
                                                        <span class="sr-only">Previous</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 01-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <div class="flex items-center gap-1 px-4">
                                                        <span class="text-sm font-bold text-slate-900" x-text="page"></span>
                                                        <span class="text-sm font-bold text-slate-400">/</span>
                                                        <span class="text-sm font-bold text-slate-400" x-text="Math.ceil(total / perPage)"></span>
                                                    </div>

                                                    <button @click="page++" :disabled="page * perPage >= total"
                                                        class="relative inline-flex items-center rounded-xl bg-white p-2 text-slate-400 hover:bg-slate-50 focus:z-20 disabled:opacity-50 transition-all border border-slate-200">
                                                        <span class="sr-only">Next</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Page 2: Laporan -->
            <div x-show="mainPage === 'laporan'" x-cloak x-transition>
                <div class="mb-12">
                    <h1 class="text-4xl font-bold text-slate-900 mb-2">Laporan Penerimaan Kas</h1>
                    <p class="text-slate-500 text-lg">Rekapitulasi total berdasarkan periode yang dipilih.</p>
                </div>

                <div class="grid grid-cols-1 gap-12">
                    @foreach ($reports as $report)
                        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-slate-900 uppercase tracking-tight">
                                            {{ $report['bank']->name }}</h2>
                                        <p class="text-slate-400 font-semibold">{{ $report['bank']->account_number }}
                                            ({{ $report['bank']->bank_name }})
                                        </p>
                                    </div>
                                </div>

                                <!-- CSV Download Button -->
                                <a href="{{ route('report.download', ['bank' => $report['bank']->id, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100 active:scale-95">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Download CSV
                                </a>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <!-- List 1: Receipt Types -->
                                <div>
                                    <h3
                                        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 px-4 border-l-4 border-blue-500">
                                        Berdasarkan Jenis Penerimaan</h3>
                                    <dl
                                        class="divide-y divide-slate-100 border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                                        @forelse($report['receipt_types'] as $rt)
                                            <div
                                                class="px-6 py-5 flex justify-between items-center bg-white hover:bg-slate-50 transition-colors">
                                                <dt class="text-sm font-semibold text-slate-600">{{ $rt->name }}
                                                </dt>
                                                <dd class="text-sm font-bold text-slate-900">Rp
                                                    {{ number_format($rt->total, 0, ',', '.') }}</dd>
                                            </div>
                                        @empty
                                            <div
                                                class="px-6 py-12 text-center text-slate-400 italic text-sm bg-slate-50/30">
                                                Data tidak tersedia</div>
                                        @endforelse
                                    </dl>
                                </div>

                                <!-- List 2: Payment Channels -->
                                <div>
                                    <h3
                                        class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-6 px-4 border-l-4 border-amber-500">
                                        Berdasarkan Kanal Pembayaran</h3>
                                    <dl
                                        class="divide-y divide-slate-100 border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                                        @forelse($report['payment_channels'] as $pc)
                                            <div
                                                class="px-6 py-5 flex justify-between items-center bg-white hover:bg-slate-50 transition-colors">
                                                <dt class="text-sm font-semibold text-slate-600">{{ $pc->name }}
                                                </dt>
                                                <dd class="text-sm font-bold text-slate-900">Rp
                                                    {{ number_format($pc->total, 0, ',', '.') }}</dd>
                                            </div>
                                        @empty
                                            <div
                                                class="px-6 py-12 text-center text-slate-400 italic text-sm bg-slate-50/30">
                                                Data tidak tersedia</div>
                                        @endforelse
                                    </dl>
                                </div>
                            </div>

                            <div class="mt-8 pt-8 border-t border-slate-50 flex justify-end">
                                <div class="text-right">
                                    <p class="text-xs font-bold text-slate-400 uppercase">Subtotal Penerimaan</p>
                                    <p class="text-3xl font-black text-orange-700">Rp
                                        {{ number_format($report['bank']->total_reception ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>

        <footer class="mt-24 border-t border-slate-100 py-12 px-6">
            <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-6">
                <p class="text-slate-400 text-sm">© {{ date('Y') }} POSH. Transparansi Keuangan Terotomasi.</p>
                <div class="flex gap-6">
                    <button @click="mainPage = 'monitoring'"
                        class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-orange-700">Monitoring</button>
                    <button @click="mainPage = 'laporan'"
                        class="text-xs font-bold text-slate-400 uppercase tracking-widest hover:text-orange-700">Laporan</button>
                </div>
            </div>
        </footer>
    </div>
</body>

</html>
