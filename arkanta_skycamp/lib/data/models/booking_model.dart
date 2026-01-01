import 'kavling_model.dart';
import 'peralatan_model.dart';

class Booking {
  final int id;
  final String code;
  final int userId;
  final int? kavlingId;
  final DateTime tanggalCheckIn;
  final DateTime tanggalCheckOut;
  final int totalHarga;
  final String status;
  final String? buktiPembayaran;
  final DateTime? createdAt;
  final Kavling? kavling;
  final List<BookingItem> items;

  Booking({
    required this.id,
    required this.code,
    required this.userId,
    this.kavlingId,
    required this.tanggalCheckIn,
    required this.tanggalCheckOut,
    required this.totalHarga,
    required this.status,
    this.buktiPembayaran,
    this.createdAt,
    this.kavling,
    this.items = const [],
  });

  factory Booking.fromJson(Map<String, dynamic> json) {
    // Safe int parsing - handles int, String, double, and decimal strings
    int safeParseInt(dynamic value) {
      if (value == null) return 0;
      if (value is int) return value;
      if (value is double) return value.toInt();
      if (value is String) {
        final doubleVal = double.tryParse(value);
        if (doubleVal != null) return doubleVal.toInt();
        return int.tryParse(value) ?? 0;
      }
      return 0;
    }

    return Booking(
      id: safeParseInt(json['id']),
      code: json['code']?.toString() ?? '',
      userId: safeParseInt(json['user_id']),
      kavlingId: json['kavling_id'] != null ? safeParseInt(json['kavling_id']) : null,
      tanggalCheckIn: DateTime.tryParse(json['tanggal_check_in']?.toString() ?? '') ?? DateTime.now(),
      tanggalCheckOut: DateTime.tryParse(json['tanggal_check_out']?.toString() ?? '') ?? DateTime.now(),
      totalHarga: safeParseInt(json['total_harga']),
      status: json['status']?.toString() ?? 'pending',
      buktiPembayaran: json['bukti_pembayaran']?.toString(),
      createdAt: json['created_at'] != null 
          ? DateTime.tryParse(json['created_at'].toString()) 
          : null,
      kavling: json['kavling'] != null 
          ? Kavling.fromJson(json['kavling']) 
          : null,
      items: json['items'] != null
          ? (json['items'] as List).map((e) => BookingItem.fromJson(e)).toList()
          : [],
    );
  }

  String get formattedTotal {
    return 'Rp ${totalHarga.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]}.',
    )}';
  }

  String get statusLabel {
    // Handle legacy data where payment was uploaded but status not updated
    if (status == 'pending' && buktiPembayaran != null) {
      return 'Menunggu Verifikasi';
    }
    
    switch (status) {
      case 'pending':
        return 'Menunggu Pembayaran';
      case 'waiting_confirmation':
        return 'Menunggu Verifikasi';
      case 'confirmed':
        return 'Dikonfirmasi';
      case 'checked_in':
        return 'Checked In';
      case 'cancelled':
        return 'Dibatalkan';
      case 'rejected':
        return 'Ditolak';
      case 'completed':
        return 'Selesai';
      default:
        return status;
    }
  }

  int get totalNights {
    return tanggalCheckOut.difference(tanggalCheckIn).inDays;
  }

  bool get canUploadPayment => status == 'pending' && buktiPembayaran == null;
  bool get canCancel => status == 'pending';
}

class BookingItem {
  final int id;
  final int bookingId;
  final int peralatanId;
  final int jumlah;
  final int harga;
  final Peralatan? peralatan;

  BookingItem({
    required this.id,
    required this.bookingId,
    required this.peralatanId,
    required this.jumlah,
    required this.harga,
    this.peralatan,
  });

  factory BookingItem.fromJson(Map<String, dynamic> json) {
    int safeParseInt(dynamic value) {
      if (value == null) return 0;
      if (value is int) return value;
      if (value is double) return value.toInt();
      if (value is String) {
        final doubleVal = double.tryParse(value);
        if (doubleVal != null) return doubleVal.toInt();
        return int.tryParse(value) ?? 0;
      }
      return 0;
    }

    return BookingItem(
      id: safeParseInt(json['id']),
      bookingId: safeParseInt(json['booking_id']),
      peralatanId: safeParseInt(json['peralatan_id']),
      jumlah: safeParseInt(json['jumlah']),
      harga: safeParseInt(json['harga']),
      peralatan: json['peralatan'] != null 
          ? Peralatan.fromJson(json['peralatan']) 
          : null,
    );
  }

  int get subtotal => jumlah * harga;
}
