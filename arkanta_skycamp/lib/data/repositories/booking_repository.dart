import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/booking_model.dart';

class BookingRepository {
  final ApiClient _apiClient = apiClient;

  Future<List<Booking>> getMyBookings() async {
    try {
      final response = await _apiClient.get(ApiConfig.bookings);
      
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data is List 
            ? response.data 
            : response.data['data'] ?? [];
        return data.map((json) => Booking.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch bookings');
    }
  }

  Future<Booking?> getById(int id) async {
    try {
      final response = await _apiClient.get('${ApiConfig.bookings}/$id');
      
      if (response.statusCode == 200) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return Booking.fromJson(data);
      }
      return null;
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch booking');
    }
  }

  Future<BookingResult> createBooking({
    required int kavlingId,
    required DateTime checkIn,
    required DateTime checkOut,
    List<Map<String, dynamic>>? items,
  }) async {
    try {
      final payload = {
        'kavling_id': kavlingId,
        'tanggal_check_in': checkIn.toIso8601String().split('T')[0],
        'tanggal_check_out': checkOut.toIso8601String().split('T')[0],
        'items': items ?? [],
      };

      final response = await _apiClient.post(
        ApiConfig.bookings,
        data: payload,
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return BookingResult.success(booking: Booking.fromJson(data));
      }
      
      return BookingResult.error(message: 'Failed to create booking');
    } on DioException catch (e) {
      final message = e.response?.data['message'] ?? e.message ?? 'Failed to create booking';
      return BookingResult.error(message: message);
    } catch (e) {
      return BookingResult.error(message: e.toString());
    }
  }

  Future<BookingResult> uploadPayment(int bookingId, String imagePath) async {
    try {
      final formData = FormData.fromMap({
        'bukti_pembayaran': await MultipartFile.fromFile(imagePath),
      });

      final response = await _apiClient.postFormData(
        '${ApiConfig.bookings}/$bookingId/upload-payment',
        formData,
      );

      if (response.statusCode == 200) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return BookingResult.success(booking: Booking.fromJson(data));
      }
      
      return BookingResult.error(message: 'Upload failed');
    } on DioException catch (e) {
      return BookingResult.error(
        message: e.response?.data['message'] ?? 'Upload failed',
      );
    }
  }

  Future<BookingResult> cancelBooking(int bookingId) async {
    try {
      final response = await _apiClient.post(
        '${ApiConfig.bookings}/$bookingId/cancel',
      );

      if (response.statusCode == 200) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return BookingResult.success(booking: Booking.fromJson(data));
      }
      
      return BookingResult.error(message: 'Cancel failed');
    } on DioException catch (e) {
      return BookingResult.error(
        message: e.response?.data['message'] ?? 'Cancel failed',
      );
    }
  }
}

class BookingResult {
  final bool isSuccess;
  final Booking? booking;
  final String? message;

  BookingResult._({required this.isSuccess, this.booking, this.message});

  factory BookingResult.success({required Booking booking}) {
    return BookingResult._(isSuccess: true, booking: booking);
  }

  factory BookingResult.error({required String message}) {
    return BookingResult._(isSuccess: false, message: message);
  }
}

final bookingRepository = BookingRepository();
