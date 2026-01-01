<x-layouts.admin title="Dashboard">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-ui.stat-card value="{{ $todayBookings ?? 12 }}" label="Booking Hari Ini" trend="+15% dari kemarin"
            :trendUp="true"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' />

        <x-ui.stat-card value="Rp {{ number_format($monthlyRevenue ?? 5200000, 0, ',', '.') }}"
            label="Pendapatan Bulan Ini" trend="+20% dari bulan lalu" :trendUp="true"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />

        <x-ui.stat-card value="{{ $availableKavling ?? 8 }}/{{ $totalKavling ?? 15 }}" label="Kavling Tersedia"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>' />

        <x-ui.stat-card value="{{ $availableGear ?? 45 }}/{{ $totalGear ?? 60 }}" label="Peralatan Tersedia"
            icon='<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' />
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Chart Section -->
        <div class="lg:col-span-2">
            <x-ui.card>
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Grafik Booking Mingguan</h3>
                    <select class="form-select text-sm w-auto">
                        <option>7 Hari Terakhir</option>
                        <option>30 Hari Terakhir</option>
                        <option>Bulan Ini</option>
                    </select>
                </div>
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <canvas id="bookingChart"></canvas>
                </div>
            </x-ui.card>
        </div>

        <!-- Pending Verification -->
        <div>
            <x-ui.card>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Pending Verifikasi</h3>
                    <span class="badge badge-warning">{{ $pendingCount ?? 5 }}</span>
                </div>

                <div class="space-y-3">
                    @forelse($pendingBookings ?? [] as $booking)
                        @if($booking)
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                            <div
                                class="w-10 h-10 bg-accent-100 rounded-full flex items-center justify-center text-accent-600 font-semibold text-sm">
                                {{ substr(data_get($booking, 'user.name', 'U'), 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">{{ data_get($booking, 'code', 'BK-001') }}</p>
                                <p class="text-xs text-gray-500">Rp
                                    {{ number_format(data_get($booking, 'total_harga', 350000), 0, ',', '.') }}</p>
                            </div>
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        @endif
                    @empty
                        <div class="text-center py-6">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-sm text-gray-500">Tidak ada pembayaran pending</p>
                            <p class="text-xs text-gray-400 mt-1">Semua sudah terverifikasi</p>
                        </div>
                    @endforelse
                </div>

                <a href="{{ route('admin.verifikasi.index') }}"
                    class="btn btn-ghost w-full mt-4 text-primary-600 hover:text-primary-700">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </a>
            </x-ui.card>
        </div>
    </div>

    <!-- Weather Widget -->
    <div class="mt-6">
        <x-ui.card>
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">🌤️ Cuaca Gunung Luhur Hari Ini</h3>
                    <p class="text-gray-600">
                        <span class="text-2xl font-bold text-gray-900">{{ $weather['temp'] ?? 21 }}°C</span>
                        | {{ $weather['description'] ?? 'Cerah Berawan' }}
                        | Kelembaban: {{ $weather['humidity'] ?? 65 }}%
                        | Angin: {{ $weather['wind'] ?? 12 }} km/h
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('bookingChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                    datasets: [{
                        label: 'Booking',
                        data: [12, 19, 15, 8, 22, 30, 25],
                        backgroundColor: 'rgba(74, 124, 35, 0.8)',
                        borderRadius: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
        </script>
    @endpush
</x-layouts.admin>