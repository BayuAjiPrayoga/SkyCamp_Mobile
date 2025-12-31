<x-layouts.admin title="Tambah Peralatan">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.peralatan.index') }}" class="hover:text-primary-600">Peralatan</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Tambah Baru</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Tambah Peralatan Baru</h2>
        </div>

        <x-ui.card>
            <form method="POST" action="{{ route('admin.peralatan.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="space-y-6">
                    <!-- Nama Alat -->
                    <x-ui.input label="Nama Alat" name="nama" placeholder="Contoh: Tenda Dome 4 Orang" required
                        :error="$errors->first('nama')" value="{{ old('nama') }}" />

                    <!-- Kategori & Harga -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.select label="Kategori" name="kategori" :options="['tenda' => 'Tenda', 'masak' => 'Masak', 'tidur' => 'Tidur', 'lainnya' => 'Lainnya']" required :error="$errors->first('kategori')" />
                        <x-ui.input label="Harga Sewa/Malam (Rp)" name="harga_sewa" type="number" placeholder="75000"
                            required :error="$errors->first('harga_sewa')" value="{{ old('harga_sewa') }}" />
                    </div>

                    <!-- Stok & Kondisi -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input label="Stok Total" name="stok_total" type="number" placeholder="15" required
                            :error="$errors->first('stok_total')" value="{{ old('stok_total') }}"
                            helper="Stok tersedia akan sama dengan stok total saat pertama kali dibuat" />
                        <x-ui.select label="Kondisi" name="kondisi" :options="['baik' => 'Baik', 'perlu_perbaikan' => 'Perlu Perbaikan', 'rusak' => 'Rusak']" :error="$errors->first('kondisi')" />
                    </div>

                    <!-- Deskripsi -->
                    <x-ui.textarea label="Deskripsi" name="deskripsi" placeholder="Deskripsi detail peralatan..."
                        :error="$errors->first('deskripsi')">{{ old('deskripsi') }}</x-ui.textarea>

                    <!-- Gambar Upload -->
                    <div class="space-y-2">
                        <label class="form-label">Gambar Peralatan</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition cursor-pointer"
                            id="upload-area">
                            <input type="file" name="gambar" id="gambar-input" class="hidden" accept="image/*">
                            <div id="upload-placeholder">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-600 mb-1">Klik untuk upload gambar</p>
                                <p class="text-xs text-gray-500">PNG, JPG hingga 2MB</p>
                            </div>
                            <img id="preview-image" class="hidden max-w-full h-48 mx-auto rounded-lg" alt="Preview">
                        </div>
                        @error('gambar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t">
                    <x-ui.button type="button" variant="ghost" href="{{ route('admin.peralatan.index') }}">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Peralatan
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
        <script>
            const uploadArea = document.getElementById('upload-area');
            const fileInput = document.getElementById('gambar-input');
            const placeholder = document.getElementById('upload-placeholder');
            const preview = document.getElementById('preview-image');

            uploadArea.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        preview.src = e.target.result;
                        placeholder.classList.add('hidden');
                        preview.classList.remove('hidden');
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endpush
</x-layouts.admin>