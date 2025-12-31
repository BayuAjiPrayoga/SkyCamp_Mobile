<x-layouts.admin title="Detail Booking">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('admin.booking.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar Booking
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Header -->
            <x-ui.card>
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Kode Booking</p>
                        <p class="text-2xl font-bold font-mono text-primary-600">{{ $booking->code }}</p>
                    </div>
                    @switch($booking->status)
                        @case('pending')
                            <x-ui.badge variant="warning">🟡 Pending</x-ui.badge>
                            @break
                        @case('confirmed')
                            <x-ui.badge variant="success">🟢 Dikonfirmasi</x-ui.badge>
                            @break
                        @case('rejected')
                            <x-ui.badge variant="error">🔴 Ditolak</x-ui.badge>
                            @break
                        @case('completed')
                            <x-ui.badge variant="neutral">✅ Selesai</x-ui.badge>
                            @break
                        @case('cancelled')
                            <x-ui.badge variant="error">❌ Dibatalkan</x-ui.badge>
                            @break
                        @default
                            <x-ui.badge variant="neutral">{{ ucfirst($booking->status) }}</x-ui.badge>
                    @endswitch
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Tanggal Check-in</p>
                        <p class="font-semibold">{{ $booking->tanggal_check_in->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Tanggal Check-out</p>
                        <p class="font-semibold">{{ $booking->tanggal_check_out->translatedFormat('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Durasi</p>
                        <p class="font-semibold">{{ $booking->tanggal_check_in->diffInDays($booking->tanggal_check_out) }} Malam</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Dibuat</p>
                        <p class="font-semibold">{{ $booking->created_at->translatedFormat('d M Y H:i') }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Customer Info -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Customer</h3>
                <div class="flex items-start gap-4">
                    <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-bold text-xl">
                        {{ strtoupper(substr($booking->user->name ?? 'G', 0, 1)) }}
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nama Lengkap</p>
                            <p class="font-semibold">{{ $booking->user->name ?? 'Guest' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-semibold">{{ $booking->user->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telepon</p>
                            <p class="font-semibold">{{ $booking->user->phone ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <!-- Kavling Info -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Kavling</h3>
                <div class="flex items-start gap-4">
                    @if($booking->kavling->gambar)
                        <img src="{{ asset('storage/' . $booking->kavling->gambar) }}" 
                             alt="{{ $booking->kavling->nama }}" 
                             class="w-24 h-24 object-cover rounded-lg">
                    @else
                        <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="font-semibold text-lg">{{ $booking->kavling->nama }}</h4>
                        <p class="text-sm text-gray-500 mb-2">Kapasitas: {{ $booking->kavling->kapasitas }} Orang</p>
                        <p class="text-sm text-gray-600">{{ $booking->kavling->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Rented Equipment -->
            @if($booking->items->count() > 0)
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Peralatan yang Disewa</h3>
                <div class="space-y-3">
                    @foreach($booking->items as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-3">
                            @if($item->peralatan->gambar)
                                <img src="{{ asset('storage/' . $item->peralatan->gambar) }}" 
                                     alt="{{ $item->peralatan->nama }}" 
                                     class="w-12 h-12 object-cover rounded">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                <p class="font-medium">{{ $item->peralatan->nama }}</p>
                                <p class="text-sm text-gray-500">{{ $item->jumlah }}x @ Rp {{ number_format($item->harga_sewa, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <p class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Rejection Reason -->
            @if($booking->status === 'rejected' && $booking->rejection_reason)
            <x-ui.card class="border-red-200 bg-red-50">
                <h3 class="text-lg font-semibold text-red-800 mb-2">Alasan Penolakan</h3>
                <p class="text-red-700">{{ $booking->rejection_reason }}</p>
            </x-ui.card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pembayaran</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sewa Kavling ({{ $booking->tanggal_check_in->diffInDays($booking->tanggal_check_out) }} malam)</span>
                        <span>Rp {{ number_format($booking->kavling->harga_per_malam * $booking->tanggal_check_in->diffInDays($booking->tanggal_check_out), 0, ',', '.') }}</span>
                    </div>
                    @if($booking->items->count() > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Sewa Peralatan</span>
                        <span>Rp {{ number_format($booking->items->sum('subtotal'), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <hr>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary-600">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Payment Proof -->
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Bukti Pembayaran</h3>
                @if($booking->bukti_pembayaran)
                    <a href="{{ asset('storage/' . $booking->bukti_pembayaran) }}" target="_blank" class="block">
                        <img src="{{ asset('storage/' . $booking->bukti_pembayaran) }}" 
                             alt="Bukti Pembayaran" 
                             class="w-full rounded-lg hover:opacity-75 transition cursor-zoom-in">
                    </a>
                    <p class="text-xs text-gray-500 text-center mt-2">Klik untuk memperbesar</p>
                @else
                    <div class="bg-gray-100 rounded-lg p-8 text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-500">Belum ada bukti pembayaran</p>
                    </div>
                @endif
            </x-ui.card>

            <!-- QR Code -->
            @if($booking->qr_code)
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">QR Code Tiket</h3>
                <div class="text-center">
                    <img src="{{ asset('storage/' . $booking->qr_code) }}" 
                         alt="QR Code" 
                         class="w-48 h-48 mx-auto">
                    <p class="text-xs text-gray-500 mt-2">Scan untuk check-in</p>
                </div>
            </x-ui.card>
            @endif

            <!-- Actions -->
            @if($booking->status === 'pending' && $booking->bukti_pembayaran)
            <x-ui.card>
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Verifikasi Pembayaran</h3>
                <div class="space-y-3">
                    <form action="{{ route('admin.verifikasi.confirm', $booking) }}" method="POST">
                        @csrf
                        <x-ui.button type="submit" variant="primary" class="w-full">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Konfirmasi & Generate QR
                        </x-ui.button>
                    </form>
                    
                    <x-ui.button type="button" variant="outline" class="w-full text-red-600 border-red-300 hover:bg-red-50" onclick="openModal('modal-reject')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tolak Pembayaran
                    </x-ui.button>
                </div>
            </x-ui.card>
            @endif
        </div>
    </div>

    <!-- Reject Modal -->
    <x-ui.modal id="modal-reject" title="Tolak Pembayaran">
        <form action="{{ route('admin.verifikasi.reject', $booking) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <p class="text-sm text-gray-600">
                    Masukkan alasan penolakan untuk booking <strong>{{ $booking->code }}</strong>:
                </p>
                <x-ui.textarea 
                    name="rejection_reason" 
                    placeholder="Contoh: Nominal tidak sesuai, bukti transfer tidak valid, dll..."
                    rows="4"
                    required
                />
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-reject')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="error">Tolak Pembayaran</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</x-layouts.admin>
