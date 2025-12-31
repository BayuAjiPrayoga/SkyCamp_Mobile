<x-layouts.admin title="Verifikasi Pembayaran">
    @php
        /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Booking> $pendingBookings */
    @endphp
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Verifikasi Pembayaran</h2>
            <p class="text-sm text-gray-500">Validasi bukti transfer dari customer</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Pending:</span>
                <span class="badge badge-warning font-semibold">{{ $pendingCount ?? 5 }}</span>
            </div>
            <div class="flex items-center gap-2 text-sm">
                <span class="text-gray-500">Hari ini:</span>
                <span class="badge badge-info font-semibold">{{ $todayCount ?? 12 }}</span>
            </div>
        </div>
    </div>

    <!-- Pending Verifications -->
    <div class="space-y-4">
        @forelse($pendingBookings ?? [] as $booking)
            <!-- Verification Card -->
            <x-ui.card class="p-0">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <!-- Booking Info -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <span class="font-mono font-bold text-primary-600">{{ $booking->code }}</span>
                                    <span class="text-gray-400 mx-2">•</span>
                                    <span class="font-semibold">{{ $booking->user?->name ?? 'Guest' }}</span>
                                </div>
                                <span class="text-lg font-bold text-primary-600">Rp
                                    {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                                <div>
                                    <p class="text-gray-500">Tanggal Camp</p>
                                    <p class="font-medium">
                                        {{ $booking->tanggal_check_in?->format('d M') ?? '-' }} -
                                        {{ $booking->tanggal_check_out?->format('d M Y') ?? '-' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Kavling</p>
                                    <p class="font-medium">{{ $booking->kavling?->nama ?? '-' }}</p>
                                </div>
                            </div>

                            <!-- Payment Info -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="text-sm font-semibold text-gray-900 mb-2">Bukti Transfer</h4>
                                <div class="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500">Pengirim:</span>
                                        <span class="font-medium ml-1">{{ $booking->user?->name ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500">Nominal:</span>
                                        <span class="font-medium ml-1">Rp
                                            {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-500">Upload:</span>
                                        <span
                                            class="font-medium ml-1">{{ $booking->updated_at?->format('d M Y, H:i') ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Proof Image -->
                        <div class="lg:w-64 flex-shrink-0">
                            <div class="bg-gray-100 rounded-lg aspect-[3/4] flex items-center justify-center relative group cursor-pointer"
                                onclick="openImageModal('{{ Storage::url($booking->bukti_pembayaran) }}')">
                                <img src="{{ Storage::url($booking->bukti_pembayaran) }}"
                                    class="w-full h-full object-cover rounded-lg" alt="Bukti Transfer">
                                <div
                                    class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Footer -->
                <div class="border-t border-gray-100 px-6 py-4 bg-gray-50 flex items-center justify-end gap-3">
                    <x-ui.button variant="outline" onclick="openRejectModal('{{ $booking->id }}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak Pembayaran
                    </x-ui.button>
                    <x-ui.button variant="primary" onclick="openConfirmModal('{{ $booking->id }}')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Konfirmasi & Generate QR
                    </x-ui.button>
                </div>
            </x-ui.card>
        @empty
            <!-- Empty State -->
            <x-ui.card>
                <div class="text-center py-12">
                    <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Pembayaran Pending</h3>
                    <p class="text-gray-600 mb-4">Semua pembayaran sudah diverifikasi atau belum ada booking baru</p>
                    <p class="text-sm text-gray-500">Pembayaran yang perlu diverifikasi akan muncul di sini</p>
                </div>
            </x-ui.card>
        @endforelse
    </div>

    <!-- Empty State -->
    @if(empty($pendingBookings) && !isset($demo))
        <x-ui.card>
            <x-ui.empty-state title="Tidak ada pembayaran pending"
                description="Semua pembayaran sudah diverifikasi. Cek kembali nanti." />
        </x-ui.card>
    @endif

    <!-- Confirm Modal -->
    <x-ui.modal id="modal-confirm" title="Konfirmasi Pembayaran" size="sm">
        <form id="form-confirm" method="POST" action="">
            @csrf
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Konfirmasi Pembayaran?</h3>
                <p class="text-sm text-gray-500 mb-4">Setelah dikonfirmasi, QR Code tiket akan otomatis digenerate dan
                    dikirim ke customer.</p>
            </div>

            <x-slot name="footer">
                <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-confirm')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" form="form-confirm">Ya, Konfirmasi</x-ui.button>
            </x-slot>
        </form>
    </x-ui.modal>

    <!-- Reject Modal -->
    <x-ui.modal id="modal-reject" title="Tolak Pembayaran" size="md">
        <form id="form-reject" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div class="text-center mb-4">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Tolak Pembayaran?</h3>
                </div>

                <x-ui.textarea label="Alasan Penolakan" name="rejection_reason"
                    placeholder="Contoh: Nominal transfer tidak sesuai, bukti tidak jelas, dll." required />
            </div>

            <x-slot name="footer">
                <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-reject')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="danger" form="form-reject">Tolak Pembayaran</x-ui.button>
            </x-slot>
        </form>
    </x-ui.modal>

    <!-- Image Zoom Modal -->
    <x-ui.modal id="modal-image" title="Bukti Transfer" size="lg">
        <div class="bg-gray-100 rounded-lg aspect-video flex items-center justify-center overflow-hidden">
            <img id="zoom-image" src="" class="w-full h-full object-contain" alt="Bukti Transfer Full">
        </div>
        <x-slot name="footer">
            <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-image')">Tutup</x-ui.button>
        </x-slot>
    </x-ui.modal>

    <script>
        function openConfirmModal(id) {
            document.getElementById('form-confirm').action = `/admin/verifikasi/${id}/confirm`;
            openModal('modal-confirm');
        }

        function openRejectModal(id) {
            document.getElementById('form-reject').action = `/admin/verifikasi/${id}/reject`;
            openModal('modal-reject');
        }

        function openImageModal(url) {
            document.getElementById('zoom-image').src = url;
            openModal('modal-image');
        }
    </script>
</x-layouts.admin>