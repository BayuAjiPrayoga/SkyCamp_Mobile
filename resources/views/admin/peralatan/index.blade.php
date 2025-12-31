<x-layouts.admin title="Manajemen Peralatan">
    @php
        /** @var \Illuminate\Pagination\LengthAwarePaginator<\App\Models\Peralatan> $peralatans */
    @endphp
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Katalog Peralatan</h2>
            <p class="text-sm text-gray-500">Kelola inventaris alat camping untuk disewa</p>
        </div>
        <x-ui.button variant="primary" href="{{ route('admin.peralatan.create') }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Peralatan
        </x-ui.button>
    </div>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form method="GET" action="{{ route('admin.peralatan.index') }}">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari peralatan..."
                            class="form-input pl-10">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <select name="kategori" class="form-select w-full sm:w-40">
                    <option value="">Semua Kategori</option>
                    <option value="tenda" {{ request('kategori') === 'tenda' ? 'selected' : '' }}>🏕️ Tenda</option>
                    <option value="masak" {{ request('kategori') === 'masak' ? 'selected' : '' }}>🍳 Masak</option>
                    <option value="tidur" {{ request('kategori') === 'tidur' ? 'selected' : '' }}>🛏️ Tidur</option>
                    <option value="lainnya" {{ request('kategori') === 'lainnya' ? 'selected' : '' }}>📦 Lainnya</option>
                </select>
                <select name="kondisi" class="form-select w-full sm:w-40">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" {{ request('kondisi') === 'baik' ? 'selected' : '' }}>Baik</option>
                    <option value="perlu_perbaikan" {{ request('kondisi') === 'perlu_perbaikan' ? 'selected' : '' }}>Perlu
                        Perbaikan</option>
                    <option value="rusak" {{ request('kondisi') === 'rusak' ? 'selected' : '' }}>Rusak</option>
                </select>
                <x-ui.button type="submit" variant="primary">Filter</x-ui.button>
                @if(request()->hasAny(['search', 'kategori', 'kondisi']))
                    <x-ui.button type="button" variant="ghost"
                        href="{{ route('admin.peralatan.index') }}">Reset</x-ui.button>
                @endif
            </div>
        </form>
    </x-ui.card>

    <!-- Success Alert -->
    @if(session('success'))
        <x-ui.alert variant="success" class="mb-6">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    <!-- Table -->
    <div class="table-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Harga Sewa</th>
                    <th>Stok</th>
                    <th>Kondisi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($peralatans ?? [] as $index => $peralatan)
                    <tr>
                        <td>{{ $peralatans->firstItem() + $index }}</td>
                        <td class="font-medium">{{ $peralatan->nama }}</td>
                        <td>
                            @if($peralatan->kategori === 'tenda')
                                🏕️ Tenda
                            @elseif($peralatan->kategori === 'masak')
                                🍳 Masak
                            @elseif($peralatan->kategori === 'tidur')
                                🛏️ Tidur
                            @else
                                📦 Lainnya
                            @endif
                        </td>
                        <td>Rp {{ number_format($peralatan->harga_sewa, 0, ',', '.') }}</td>
                        <td>
                            <span class="text-sm">
                                {{ $peralatan->stok_tersedia }}/{{ $peralatan->stok_total }}
                            </span>
                        </td>
                        <td>
                            @if($peralatan->kondisi === 'baik')
                                <x-ui.badge variant="success">✓ Baik</x-ui.badge>
                            @elseif($peralatan->kondisi === 'perlu_perbaikan')
                                <x-ui.badge variant="warning">⚠ Perlu Perbaikan</x-ui.badge>
                            @else
                                <x-ui.badge variant="error">✗ Rusak</x-ui.badge>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.peralatan.edit', $peralatan) }}" class="btn btn-ghost btn-sm"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.peralatan.destroy', $peralatan) }}" method="POST"
                                    class="inline" onsubmit="return confirm('Yakin ingin menghapus peralatan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-ghost btn-sm text-red-600 hover:bg-red-50"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-8">
                            Belum ada data peralatan. Klik "Tambah Peralatan" untuk mulai menambahkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if(isset($peralatans) && $peralatans->hasPages())
        <div class="mt-6">
            {{ $peralatans->links() }}
        </div>
    @endif
</x-layouts.admin>