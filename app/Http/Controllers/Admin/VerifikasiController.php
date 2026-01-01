<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectBookingRequest;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Services\BookingService;

class VerifikasiController extends Controller
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected BookingService $bookingService
    ) {
    }

    /**
     * Display pending verifications
     */
    public function index()
    {
        // Directly query with pagination instead of using repository
        $pendingBookings = \App\Models\Booking::with(['user', 'kavling'])
            ->whereIn('status', ['pending', 'waiting_confirmation'])
            ->whereNotNull('bukti_pembayaran')
            ->latest()
            ->paginate(10);

        $pendingCount = $pendingBookings->total();
        $todayCount = \App\Models\Booking::whereDate('created_at', today())->count();

        return view('admin.verifikasi.index', compact(
            'pendingBookings',
            'pendingCount',
            'todayCount'
        ));
    }

    /**
     * Confirm a booking payment
     */
    public function confirm(int $id)
    {
        $this->bookingService->confirmBooking($id);

        return redirect()->route('admin.verifikasi.index')
            ->with('success', 'Pembayaran berhasil dikonfirmasi. QR Code telah dibuat.');
    }

    /**
     * Reject a booking payment
     */
    public function reject(RejectBookingRequest $request, int $id)
    {
        $this->bookingService->rejectBooking($id, $request->validated('rejection_reason'));

        return redirect()->route('admin.verifikasi.index')
            ->with('success', 'Pembayaran berhasil ditolak.');
    }
}
