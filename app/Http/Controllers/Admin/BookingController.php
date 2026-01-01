<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Exports\BookingExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $bookings = Booking::with(['user', 'kavling'])
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when(
                $request->search,
                fn($q, $search) =>
                $q->where(function ($query) use ($search) {
                    $query->where('code', 'like', "%{$search}%")
                        ->orWhereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"));
                })
            )
            ->when($request->date_from, fn($q, $date) => $q->whereDate('tanggal_check_in', '>=', $date))
            ->when($request->date_to, fn($q, $date) => $q->whereDate('tanggal_check_out', '<=', $date))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.booking.index', compact('bookings'));
    }

    /**
     * Display the specified booking
     */
    public function show($id)
    {
        $booking = Booking::with(['user', 'kavling', 'items.peralatan'])->findOrFail($id);

        return view('admin.booking.show', compact('booking'));
    }

    /**
     * Export filtered bookings to Excel
     */
    public function export(Request $request)
    {
        $filename = 'booking-' . date('Y-m-d-His') . '.xlsx';

        return Excel::download(
            new BookingExport($request->all()),
            $filename
        );
    }

    /**
     * Mark booking as checked in
     */
    public function checkIn(Booking $booking)
    {
        if (!in_array($booking->status, ['confirmed', 'paid'])) {
            return back()->with('error', 'Booking tidak dalam status yang valid untuk Check-in.');
        }

        $booking->update([
            'status' => 'checked_in',
        ]);

        return back()->with('success', 'Berhasil Check-in tamu.');
    }

    public function scanPage()
    {
        return view('admin.booking.scan');
    }

    public function scanCheckIn(Request $request)
    {
        $request->validate([
            'code' => 'required|string|exists:bookings,code',
        ]);

        $booking = Booking::where('code', $request->code)->first();

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking tidak ditemukan.',
            ], 404);
        }

        if ($booking->status === 'checked_in') {
            return response()->json([
                'status' => 'error',
                'message' => 'Tamu ini SUDAH Check-in sebelumnya.',
                'booking' => $booking->load('user', 'kavling')
            ], 400);
        }

        if (!in_array($booking->status, ['confirmed', 'paid'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Status booking tidak valid (' . ucfirst($booking->status) . ').',
                'booking' => $booking->load('user', 'kavling')
            ], 400);
        }

        $booking->update(['status' => 'checked_in']);

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in BERHASIL!',
            'booking' => $booking->load('user', 'kavling')
        ]);
    }
}
