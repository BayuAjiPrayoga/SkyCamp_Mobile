<x-layouts.admin title="Laporan & Export">
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900">Laporan & Export</h2>
        <p class="text-sm text-gray-500">Generate laporan pendapatan dan inventaris</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Report (PDF) -->
        <x-ui.card>
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Laporan Pendapatan</h3>
                    <p class="text-sm text-gray-500">Export laporan pendapatan dalam format PDF</p>
                </div>
            </div>

            <form action="{{ route('admin.laporan.pdf') }}" method="GET">
                <!-- Period Selector -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <x-ui.select label="Bulan" name="month" :value="$month ?? now()->month" :options="[
        '1' => 'Januari',
        '2' => 'Februari',
        '3' => 'Maret',
        '4' => 'April',
        '5' => 'Mei',
        '6' => 'Juni',
        '7' => 'Juli',
        '8' => 'Agustus',
        '9' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember',
    ]" />
                    <x-ui.select label="Tahun" name="year" :value="$year ?? now()->year" :options="[
        '2025' => '2025',
        '2024' => '2024',
        '2023' => '2023',
    ]" />
                </div>

                <!-- Preview -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    @php
                        $monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $currentMonthName = $monthNames[($month ?? now()->month) - 1] ?? 'Desember';
                        $currentYear = $year ?? now()->year;
                        $totalRevenue = isset($weeklyData) ? collect($weeklyData)->sum('revenue') : 0;
                        $totalBookings = isset($weeklyData) ? collect($weeklyData)->sum('bookings') : 0;
                    @endphp

                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-semibold text-gray-900">Preview Pendapatan</h4>
                        <span class="text-xs bg-primary-100 text-primary-700 px-2 py-1 rounded-full font-medium">
                            {{ $currentMonthName }} {{ $currentYear }}
                        </span>
                    </div>

                    @if(isset($weeklyData) && count($weeklyData) > 0)
                        <div class="space-y-2 text-sm">
                            @foreach($weeklyData as $week)
                                <div
                                    class="flex justify-between items-center py-1 {{ $week['revenue'] > 0 ? 'bg-green-50 -mx-2 px-2 rounded' : '' }}">
                                    <div class="flex items-center gap-2">
                                        @if($week['revenue'] > 0)
                                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        @else
                                            <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                        @endif
                                        <span class="text-gray-600">Minggu {{ $week['week'] }} <span
                                                class="text-gray-400">({{ $week['start'] }} - {{ $week['end'] }})</span></span>
                                    </div>
                                    <div class="text-right">
                                        @if($week['revenue'] > 0)
                                            <span class="font-semibold text-green-600">Rp
                                                {{ number_format($week['revenue'], 0, ',', '.') }}</span>
                                            <span class="text-gray-400 text-xs ml-1">({{ $week['bookings'] }} booking)</span>
                                        @else
                                            <span class="text-gray-400">Rp 0</span>
                                            <span class="text-gray-300 text-xs ml-1">(0 booking)</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach

                            <div class="border-t-2 border-gray-200 pt-3 mt-3">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-bold text-gray-900">TOTAL PENDAPATAN</span>
                                        <p class="text-xs text-gray-500">{{ $totalBookings }} booking di
                                            {{ $currentMonthName }}
                                        </p>
                                    </div>
                                    <span class="text-xl font-bold text-primary-600">Rp
                                        {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-gray-500 text-sm">Belum ada data booking untuk {{ $currentMonthName }}
                                {{ $currentYear }}
                            </p>
                            <p class="text-gray-400 text-xs mt-1">Data akan muncul setelah ada booking yang dikonfirmasi</p>
                        </div>
                    @endif
                </div>

                <x-ui.button type="submit" variant="primary" class="w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Download PDF
                </x-ui.button>
            </form>
        </x-ui.card>

        <!-- Inventory Report (Excel) -->
        <x-ui.card>
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Rekapitulasi Inventaris</h3>
                    <p class="text-sm text-gray-500">Export data inventaris dalam format Excel</p>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-gray-900">{{ $totalItems ?? 60 }}</p>
                    <p class="text-sm text-gray-500">Total Item</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-green-600">{{ $goodCondition ?? 52 }}</p>
                    <p class="text-sm text-gray-500">Kondisi Baik</p>
                </div>
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-yellow-600">{{ $needRepair ?? 5 }}</p>
                    <p class="text-sm text-gray-500">Perlu Perbaikan</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $damaged ?? 3 }}</p>
                    <p class="text-sm text-gray-500">Rusak</p>
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h4 class="font-semibold text-gray-900 mb-3">Per Kategori</h4>
                @php
                    $categories = \App\Models\Peralatan::selectRaw("kategori, COUNT(*) as total, SUM(CASE WHEN kondisi = 'baik' THEN 1 ELSE 0 END) as available")
                        ->groupBy('kategori')
                        ->get();
                @endphp
                @if($categories->count() > 0)
                    <div class="space-y-3">
                        @foreach($categories as $cat)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-600">{{ ucfirst($cat->kategori) }}</span>
                                <div class="flex items-center gap-2">
                                    <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-primary-500 rounded-full"
                                            style="width: {{ $cat->total > 0 ? ($cat->available / $cat->total) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                    <span
                                        class="font-medium text-gray-900 w-16 text-right">{{ $cat->available }}/{{ $cat->total }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500 text-center py-2">Belum ada data peralatan</p>
                @endif
            </div>

            <x-ui.button href="{{ route('admin.laporan.excel') }}" variant="primary"
                class="w-full bg-green-600 hover:bg-green-500">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download Excel
            </x-ui.button>
        </x-ui.card>
    </div>

    {{--
    <!-- Additional Reports - Coming Soon (setelah mobile app selesai) -->
    <div class="mt-6">
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Laporan Lainnya</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                ... Data Pengunjung, Statistik Bulanan, Log Aktivitas ...
            </div>
        </x-ui.card>
    </div>
    --}}
</x-layouts.admin>