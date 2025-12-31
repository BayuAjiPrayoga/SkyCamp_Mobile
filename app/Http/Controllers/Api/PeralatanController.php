<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Peralatan;
use Illuminate\Http\Request;

class PeralatanController extends Controller
{
    /**
     * List all available equipment
     */
    public function index(Request $request)
    {
        $peralatan = Peralatan::where('kondisi', '!=', 'rusak')
            ->where('stok_total', '>', 0)
            ->when($request->kategori, fn($q, $v) => $q->where('kategori', $v))
            ->when($request->search, fn($q, $v) => $q->where('nama', 'like', "%{$v}%"))
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $peralatan,
        ]);
    }

    /**
     * Get equipment detail
     */
    public function show(Peralatan $peralatan)
    {
        return response()->json([
            'success' => true,
            'data' => $peralatan,
        ]);
    }
}
