<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePengumumanRequest;
use App\Http\Requests\Admin\UpdatePengumumanRequest;
use App\Exports\PengumumanExport;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PengumumanController extends Controller
{
    /**
     * Display a listing of announcements
     */
    public function index(Request $request)
    {
        $announcements = Announcement::query()
            ->when($request->search, fn($q, $v) => $q->where('title', 'like', "%{$v}%")
                ->orWhere('content', 'like', "%{$v}%"))
            ->when($request->type, fn($q, $v) => $q->where('type', $v))
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->is_active))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengumuman.index', compact('announcements'));
    }

    /**
     * Store a newly created announcement
     */
    public function store(StorePengumumanRequest $request)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        Announcement::create($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    /**
     * Update the specified announcement
     */
    public function update(UpdatePengumumanRequest $request, Announcement $pengumuman)
    {
        $data = $request->validated();
        $data['is_active'] = $request->has('is_active');

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Remove the specified announcement
     */
    public function destroy(Announcement $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }

    /**
     * Export filtered announcements to Excel
     */
    public function export(Request $request)
    {
        $filename = 'pengumuman-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new PengumumanExport($request->all()),
            $filename
        );
    }
}
