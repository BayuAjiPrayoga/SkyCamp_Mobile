<x-layouts.admin title="Edit Kavling">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.kavling.index') }}" class="hover:text-primary-600">Kavling</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>Edit</span>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Edit Kavling: {{ $kavling->nama }}</h2>
        </div>

        <x-ui.card>
            <form method="POST" action="{{ route('admin.kavling.update', $kavling) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <!-- Nama Kavling -->
                    <x-ui.input label="Nama Kavling" name="nama" placeholder="Contoh: Kavling A1" required
                        :error="$errors->first('nama')" value="{{ old('nama', $kavling->nama) }}" />

                    <!-- Kapasitas & Harga -->
                    <div class="grid grid-cols-2 gap-4">
                        <x-ui.input label="Kapasitas (Orang)" name="kapasitas" type="number" placeholder="4" required
                            :error="$errors->first('kapasitas')" value="{{ old('kapasitas', $kavling->kapasitas) }}" />
                        <x-ui.input label="Harga per Malam (Rp)" name="harga_per_malam" type="number"
                            placeholder="150000" required :error="$errors->first('harga_per_malam')"
                            value="{{ old('harga_per_malam', $kavling->harga_per_malam) }}" />
                    </div>

                    <!-- Deskripsi -->
                    <x-ui.textarea label="Deskripsi" name="deskripsi"
                        placeholder="Deskripsi detail tentang kavling ini..."
                        :error="$errors->first('deskripsi')">{{ old('deskripsi', $kavling->deskripsi) }}</x-ui.textarea>

                    <!-- Gambar Upload -->
                    <div class="space-y-2">
                        <label class="form-label">Gambar Kavling</label>

                        @if($kavling->gambar)
                            <div class="mb-3">
                                <p class="text-sm text-gray-600 mb-2">Gambar saat ini:</p>
                                <img src="{{ Storage::url($kavling->gambar) }}"
                                    class="w-48 h-32 object-cover rounded-lg border" alt="{{ $kavling->nama }}">
                            </div>
                        @endif

                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary-500 transition cursor-pointer"
                            id="upload-area">
                            <input type="file" name="gambar" id="gambar-input" class="hidden" accept="image/*">
                            <div id="upload-placeholder">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-sm text-gray-600 mb-1">Klik untuk
                                    {{ $kavling->gambar ? 'mengganti' : 'upload' }} gambar</p>
                                <p class="text-xs text-gray-500">PNG, JPG hingga 2MB</p>
                            </div>
                            <img id="preview-image" class="hidden max-w-full h-48 mx-auto rounded-lg" alt="Preview">
                        </div>
                        @error('gambar')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <x-ui.select label="Status" name="status" :options="['aktif' => 'Aktif', 'penuh' => 'Penuh', 'maintenance' => 'Maintenance']" :error="$errors->first('status')" id="status" />
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t">
                    <x-ui.button type="button" variant="ghost" href="{{ route('admin.kavling.index') }}">
                        Batal
                    </x-ui.button>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    @push('scripts')
        <script>
            // Set current status value
            document.getElementById('status').value = '{{ old('status', $kavling->status) }}';

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