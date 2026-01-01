<x-layouts.admin title="Moderasi Galeri">
    @php
        /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Gallery> $photos */
    @endphp
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Moderasi Galeri</h2>
            <p class="text-sm text-gray-500">Kelola foto yang diupload pengunjung</p>
        </div>
        <div class="flex items-center gap-4 text-sm">
            <span class="text-gray-500">Pending: <span
                    class="badge badge-warning font-semibold">{{ $pendingCount ?? 8 }}</span></span>
            <span class="text-gray-500">Total: <span
                    class="badge badge-info font-semibold">{{ $totalCount ?? 156 }}</span></span>
        </div>
    </div>

    <!-- Tabs -->
    <div class="flex items-center gap-1 mb-6 bg-gray-100 p-1 rounded-lg w-fit">
        <a href="{{ route('admin.galeri.index', ['status' => 'pending']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition {{ request('status', 'pending') == 'pending' ? 'bg-white shadow text-primary-600' : 'text-gray-600 hover:text-gray-900' }}">
            📋 Pending
        </a>
        <a href="{{ route('admin.galeri.index', ['status' => 'approved']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition {{ request('status') == 'approved' ? 'bg-white shadow text-primary-600' : 'text-gray-600 hover:text-gray-900' }}">
            ✅ Approved
        </a>
        <a href="{{ route('admin.galeri.index', ['status' => 'rejected']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition {{ request('status') == 'rejected' ? 'bg-white shadow text-primary-600' : 'text-gray-600 hover:text-gray-900' }}">
            ❌ Rejected
        </a>
    </div>

    <!-- Bulk Actions -->
    <div class="flex items-center justify-between mb-4">
        <label class="flex items-center gap-2 text-sm cursor-pointer">
            <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            <span class="text-gray-600">Pilih Semua</span>
        </label>
        <div class="flex items-center gap-2">
            <x-ui.button variant="outline" size="sm" disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve Selected
            </x-ui.button>
            <x-ui.button variant="ghost" size="sm" class="text-red-600" disabled>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject Selected
            </x-ui.button>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
        @forelse($photos ?? [] as $photo)
            <div class="group relative bg-white rounded-xl shadow overflow-hidden">
                <div class="aspect-square bg-gray-100 cursor-pointer" onclick='previewPhoto(@json($photo))'>
                    <img src="{{ Storage::url($photo->image_path) }}"
                        class="w-full h-full object-cover group-hover:scale-105 transition duration-300" alt="">
                </div>
                <div class="p-3">
                    <p class="text-sm font-medium text-gray-900 truncate">@ {{ $photo->user?->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500">{{ $photo->created_at?->format('d M Y') ?? '-' }}</p>
                </div>
                <!-- Actions -->
                <div class="absolute top-2 right-2 flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
                    <form action="{{ route('admin.galeri.approve', $photo->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="w-8 h-8 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center justify-center shadow"
                            title="Approve">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </form>
                    <form action="{{ route('admin.galeri.reject', $photo->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                            class="w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-lg flex items-center justify-center shadow"
                            title="Reject">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                <!-- Checkbox -->
                <div class="absolute top-2 left-2">
                    <input type="checkbox"
                        class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 bg-white/80 backdrop-blur">
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada foto</h3>
                <p class="text-sm text-gray-500">Foto yang diupload user akan muncul di sini untuk moderasi</p>
            </div>
        @endforelse

    </div>

    <!-- Pagination (already added earlier) -->
    @if(isset($photos) && $photos->hasPages())
        <div class="mt-6">
            {{ $photos->links() }}
        </div>
    @endif

    <!-- Preview Modal -->
    <x-ui.modal id="modal-preview" title="Preview Foto" size="lg">
        <div class="space-y-4">
            <div class="bg-gray-100 rounded-lg overflow-hidden aspect-video flex items-center justify-center relative">
                <img id="preview-image" src="" class="w-full h-full object-contain absolute inset-0" alt="Preview">
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-900" id="preview-user">@username</p>
                    <p class="text-sm text-gray-500" id="preview-date">Diupload pada ...</p>
                </div>
                <div class="flex items-center gap-2">
                    <span id="preview-status" class="hidden px-2 py-1 text-xs font-semibold rounded-full"></span>
                </div>
            </div>
        </div>

        <x-slot name="footer">
            <form id="form-approve" method="POST" class="hidden">@csrf</form>
            <form id="form-reject" method="POST" class="hidden">@csrf</form>

            <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-preview')">Tutup</x-ui.button>

            <x-ui.button type="button" variant="danger" onclick="submitGalleryAction('reject')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Reject
            </x-ui.button>
            <x-ui.button type="button" variant="primary" onclick="submitGalleryAction('approve')">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Approve
            </x-ui.button>
        </x-slot>
    </x-ui.modal>

    <script>
        let currentPhotoId = null;

        function previewPhoto(photo) {
            currentPhotoId = photo.id;

            // Populate Modal
            document.getElementById('preview-image').src = '/storage/' + photo.image_path;
            document.getElementById('preview-user').textContent = '@' + (photo.user ? photo.user.name : 'Unknown');

            // Format date (simple approach or use library if available, here just raw string or simple JS format)
            const date = new Date(photo.created_at);
            document.getElementById('preview-date').textContent = 'Diupload pada ' + date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

            openModal('modal-preview');
        }

        function submitGalleryAction(action) {
            if (!currentPhotoId) return;

            // Create a temporary form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/galeri/${currentPhotoId}/${action}`;

            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;

            form.appendChild(csrfInput);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</x-layouts.admin>