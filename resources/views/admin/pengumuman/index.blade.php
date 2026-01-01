<x-layouts.admin title="Pengumuman">
    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.pengumuman.index') }}">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi..." class="form-input pl-10">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
                <select name="type" class="form-select w-full sm:w-40">
                    <option value="">Semua Tipe</option>
                    <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>ℹ️ Info</option>
                    <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>⚠️ Warning</option>
                    <option value="success" {{ request('type') === 'success' ? 'selected' : '' }}>✓ Success</option>
                </select>
                <select name="is_active" class="form-select w-full sm:w-40">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Non-aktif</option>
                </select>
                <x-ui.button type="submit" variant="primary">Filter</x-ui.button>
                @if(request()->hasAny(['search', 'type', 'is_active']))
                    <x-ui.button type="button" variant="ghost" href="{{ route('admin.pengumuman.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Pengumuman</h2>
            <p class="text-sm text-gray-500">Kelola pengumuman yang tampil di aplikasi mobile</p>
        </div>
        <x-ui.button variant="primary" onclick="openModal('modal-create')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengumuman
        </x-ui.button>
    </div>

    <!-- Announcements List -->
    <div class="space-y-4">
        @forelse($announcements ?? [] as $announcement)
            <!-- Announcement Card -->
            <x-ui.card :padding="false">
                <div class="p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-4 flex-1">
                            <!-- Type Icon -->
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                @if($announcement->type === 'info') bg-blue-100 text-blue-600
                                @elseif($announcement->type === 'warning') bg-yellow-100 text-yellow-600
                                @else bg-green-100 text-green-600
                                @endif">
                                @if($announcement->type === 'info')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                @elseif($announcement->type === 'warning')
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                    </svg>
                                @endif
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <h3 class="font-semibold text-gray-900">{{ $announcement->title }}</h3>
                                    @if($announcement->is_active)
                                        <x-ui.badge variant="success">Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="neutral">Tidak Aktif</x-ui.badge>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mb-2">{{ $announcement->content }}</p>
                                <p class="text-xs text-gray-400">Dibuat: {{ $announcement->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <button class="btn btn-ghost btn-sm" onclick='editPengumuman(@json($announcement))' title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </button>
                            <form action="{{ route('admin.pengumuman.destroy', $announcement) }}" method="POST" class="inline" onsubmit="return confirm('Hapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        @empty
            <!-- Demo Data -->
            @foreach([
                [
                    'title' => 'Libur Tahun Baru 2025',
                    'content' => 'Selamat merayakan tahun baru 2025! Area camping tetap buka dengan kapasitas penuh. Pastikan booking lebih awal untuk mendapatkan spot terbaik.',
                    'type' => 'info',
                    'active' => true,
                    'date' => '30 Des 2024',
                ],
                [
                    'title' => 'Cuaca Ekstrem - Harap Waspada',
                    'content' => 'Perkiraan cuaca menunjukkan potensi hujan lebat dan angin kencang pada tanggal 2-4 Januari 2025. Pengunjung diharapkan membawa peralatan tambahan.',
                    'type' => 'warning',
                    'active' => true,
                    'date' => '29 Des 2024',
                ],
                [
                    'title' => 'Promo Awal Tahun',
                    'content' => 'Diskon 20% untuk sewa peralatan camping selama bulan Januari 2025. Gunakan kode NEWYEAR2025 saat checkout.',
                    'type' => 'success',
                    'active' => false,
                    'date' => '28 Des 2024',
                ],
            ] as $demo)
                <x-ui.card :padding="false">
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4 flex-1">
                                <!-- Type Icon -->
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0
                                    @if($demo['type'] === 'info') bg-blue-100 text-blue-600
                                    @elseif($demo['type'] === 'warning') bg-yellow-100 text-yellow-600
                                    @else bg-green-100 text-green-600
                                    @endif">
                                    @if($demo['type'] === 'info')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($demo['type'] === 'warning')
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                        </svg>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="font-semibold text-gray-900">{{ $demo['title'] }}</h3>
                                        @if($demo['active'])
                                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="neutral">Tidak Aktif</x-ui.badge>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">{{ $demo['content'] }}</p>
                                    <p class="text-xs text-gray-400">Dibuat: {{ $demo['date'] }}</p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <button class="btn btn-ghost btn-sm" onclick='editPengumuman(@json($announcement))' title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                <button class="btn btn-ghost btn-sm text-red-600 hover:bg-red-50" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </x-ui.card>
            @endforeach
        @endforelse
    </div>

    <!-- Create Modal -->
    <x-ui.modal id="modal-create" title="Tambah Pengumuman Baru" size="lg">
        <form id="form-create" method="POST" action="{{ route('admin.pengumuman.store') }}">
            @csrf
            <div class="space-y-4">
                <x-ui.input label="Judul Pengumuman" name="title" placeholder="Contoh: Promo Akhir Tahun" required />
                
                <x-ui.textarea label="Isi Pengumuman" name="content" rows="4" placeholder="Tulis isi pengumuman di sini..." required />

                <x-ui.select label="Tipe" name="type" :options="[
                    'info' => 'ℹ️ Informasi',
                    'warning' => '⚠️ Peringatan',
                    'success' => '📢 Promosi',
                ]" required />

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" checked>
                    <label for="is_active" class="text-sm text-gray-700">Aktifkan pengumuman ini</label>
                </div>
            </div>

            <x-slot name="footer">
                <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-create')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" form="form-create">Simpan</x-ui.button>
            </x-slot>
        </form>
    </x-ui.modal>

    <!-- Edit Modal -->
    <x-ui.modal id="modal-edit" title="Edit Pengumuman">
        <form id="form-edit" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <x-ui.input id="edit-title" label="Judul Pengumuman" name="title" required />
                
                <x-ui.textarea id="edit-content" label="Isi Pengumuman" name="content" rows="4" required />

                <x-ui.select id="edit-type" label="Tipe" name="type" :options="[
                    'info' => 'ℹ️ Informasi',
                    'warning' => '⚠️ Peringatan',
                    'success' => '📢 Promosi',
                ]" required />

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" id="edit-is_active" value="1" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <label for="edit-is_active" class="text-sm text-gray-700">Aktifkan pengumuman ini</label>
                </div>
            </div>

            <x-slot name="footer">
                <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-edit')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary" form="form-edit">Simpan Perubahan</x-ui.button>
            </x-slot>
        </form>
    </x-ui.modal>

    <!-- Delete Confirmation Modal -->
    <x-ui.modal id="modal-delete" title="Hapus Pengumuman" size="sm">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Hapus Pengumuman?</h3>
            <p class="text-sm text-gray-500 mb-6">Data yang dihapus tidak dapat dikembalikan.</p>
        </div>

        <x-slot name="footer">
            <x-ui.button type="button" variant="ghost" onclick="closeModal('modal-delete')">Batal</x-ui.button>
            <x-ui.button type="button" variant="danger">Ya, Hapus</x-ui.button>
        </x-slot>
    </x-ui.modal>

    <script>
        function editPengumuman(announcement) {
            // Set form action
            const form = document.getElementById('form-edit');
            form.action = `/admin/pengumuman/${announcement.id}`;

            // Set input values
            document.getElementById('edit-title').value = announcement.title;
            
            const contentEl = document.getElementById('edit-content');
            if (contentEl) contentEl.value = announcement.content;

            document.getElementById('edit-type').value = announcement.type;
            document.getElementById('edit-is_active').checked = announcement.is_active;

            // Open modal
            openModal('modal-edit');
        }
    </script>
</x-layouts.admin>
