<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CreateBookingRequest;
use App\Http\Requests\Api\UploadPaymentRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Kavling;
use App\Repositories\Contracts\BookingRepositoryInterface;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingRepositoryInterface $bookingRepository,
        protected BookingService $bookingService
    ) {
    }

    /**
     * List user's bookings
     */
    public function index(Request $request)
    {
        $bookings = $this->bookingRepository->findByUser($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => BookingResource::collection($bookings),
        ]);
    }

    /**
     * Create new booking
     */
    public function store(CreateBookingRequest $request)
    {
        $data = $request->validated();

        // Check kavling availability
        $kavling = Kavling::findOrFail($data['kavling_id']);
        $conflicting = $kavling->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($data) {
                $q->whereBetween('tanggal_check_in', [$data['tanggal_check_in'], $data['tanggal_check_out']])
                    ->orWhereBetween('tanggal_check_out', [$data['tanggal_check_in'], $data['tanggal_check_out']])
                    ->orWhere(function ($q) use ($data) {
                        $q->where('tanggal_check_in', '<=', $data['tanggal_check_in'])
                            ->where('tanggal_check_out', '>=', $data['tanggal_check_out']);
                    });
            })
            ->exists();

        if ($conflicting) {
            return response()->json([
                'success' => false,
                'message' => 'Kavling tidak tersedia pada tanggal tersebut',
            ], 422);
        }

        try {
            $booking = $this->bookingService->createBooking($data, $request->user()->id);

            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat',
                'data' => new BookingResource($booking),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine(),
            ], 500);
        }
    }

    /**
     * Get booking detail
     */
    public function show(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id && $request->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $booking->load(['kavling', 'items.peralatan', 'user']);

        return response()->json([
            'success' => true,
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Upload payment proof
     */
    public function uploadPayment(UploadPaymentRequest $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dalam status pending',
            ], 422);
        }

        $booking = $this->bookingService->uploadPaymentProof(
            $booking->id,
            $request->file('bukti_pembayaran')
        );

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
            'data' => new BookingResource($booking),
        ]);
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak dapat dibatalkan',
            ], 422);
        }

        $booking = $this->bookingService->cancelBooking($booking->id);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibatalkan',
            'data' => new BookingResource($booking),
        ]);
    }
}
