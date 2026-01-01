<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePeralatanRequest;
use App\Http\Requests\Admin\UpdatePeralatanRequest;
use App\Repositories\Contracts\PeralatanRepositoryInterface;
use App\Models\Peralatan;
use Illuminate\Http\Request;

class PeralatanController extends Controller
{
    public function __construct(
        protected PeralatanRepositoryInterface $peralatanRepository
    ) {
    }

    /**
     * Display a listing of peralatan
     */
    public function index(Request $request)
    {
        $peralatans = Peralatan::query()
            ->when($request->search, fn($q, $v) => $q->where('nama', 'like', "%{$v}%"))
            ->when($request->kategori, fn($q, $v) => $q->where('kategori', $v))
            ->when($request->kondisi, fn($q, $v) => $q->where('kondisi', $v))
            ->withSum([
                'bookingItems' => function ($q) {
                    $q->whereHas('booking', function ($b) {
                        $b->whereIn('status', ['pending', 'waiting_confirmation', 'confirmed'])
                            ->whereDate('tanggal_check_in', '<=', now())
                            ->whereDate('tanggal_check_out', '>=', now());
                    });
                }
            ], 'jumlah')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->paginate(10);

        return view('admin.peralatan.index', compact('peralatans'));
    }

    /**
     * Show the form for creating new peralatan
     */
    public function create()
    {
        return view('admin.peralatan.create');
    }

    /**
     * Store a newly created peralatan
     */
    public function store(StorePeralatanRequest $request)
    {
        $data = $request->validated();
        $data['stok_tersedia'] = $data['stok_total'];
        $data['kondisi'] = $data['kondisi'] ?? 'baik';

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('peralatan', 'public');
        }

        $this->peralatanRepository->create($data);

        return redirect()->route('admin.peralatan.index')
            ->with('success', 'Peralatan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing peralatan
     */
    public function edit(Peralatan $peralatan)
    {
        return view('admin.peralatan.edit', compact('peralatan'));
    }

    /**
     * Update the specified peralatan
     */
    public function update(UpdatePeralatanRequest $request, Peralatan $peralatan)
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            // Delete old image if exists
            if ($peralatan->gambar) {
                \Storage::disk('public')->delete($peralatan->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('peralatan', 'public');
        }

        $this->peralatanRepository->update($peralatan->id, $data);

        return redirect()->route('admin.peralatan.index')
            ->with('success', 'Peralatan berhasil diperbarui.');
    }

    /**
     * Remove the specified peralatan
     */
    public function destroy(Peralatan $peralatan)
    {
        // Delete associated image if exists
        if ($peralatan->gambar) {
            \Storage::disk('public')->delete($peralatan->gambar);
        }

        $this->peralatanRepository->delete($peralatan->id);

        return redirect()->route('admin.peralatan.index')
            ->with('success', 'Peralatan berhasil dihapus.');
    }
}
