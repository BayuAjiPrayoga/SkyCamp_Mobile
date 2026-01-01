<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function __construct(
        protected GalleryService $galleryService
    ) {
    }

    /**
     * Display gallery for moderation
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $photos = match ($status) {
            'approved' => $this->galleryService->getApprovedPhotos(),
            'rejected' => $this->galleryService->getRejectedPhotos(),
            default => $this->galleryService->getPendingPhotos(),
        };

        return view('admin.galeri.index', [
            'photos' => $photos,
            'pendingCount' => $this->galleryService->getPendingCount(),
            'totalCount' => $this->galleryService->getTotalCount(),
        ]);
    }

    /**
     * Approve a gallery photo
     */
    public function approve(int $id)
    {
        $this->galleryService->approvePhoto($id);
        return back()->with('success', 'Foto berhasil disetujui.');
    }

    /**
     * Reject a gallery photo
     */
    public function reject(int $id)
    {
        $this->galleryService->rejectPhoto($id);
        return back()->with('success', 'Foto berhasil ditolak.');
    }

    /**
     * Bulk approve photos
     */
    public function bulkApprove(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $count = $this->galleryService->bulkApprove($request->ids);
        return back()->with('success', "{$count} foto berhasil disetujui.");
    }

    /**
     * Bulk reject photos
     */
    public function bulkReject(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $count = $this->galleryService->bulkReject($request->ids);
        return back()->with('success', "{$count} foto berhasil ditolak.");
    }
    /**
     * Delete a gallery photo permanently
     */
    public function destroy(int $id)
    {
        $this->galleryService->deletePhoto($id);
        return back()->with('success', 'Foto berhasil dihapus permanen.');
    }

    /**
     * Bulk delete photos permanently
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate(['ids' => 'required|array']);
        $count = $this->galleryService->bulkDelete($request->ids);
        return back()->with('success', "{$count} foto berhasil dihapus permanen.");
    }
}
