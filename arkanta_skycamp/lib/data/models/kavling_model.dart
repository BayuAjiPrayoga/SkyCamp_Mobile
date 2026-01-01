class Kavling {
  final int id;
  final String nama;
  final int kapasitas;
  final int hargaPerMalam;
  final List<String> fasilitas;
  final String status;
  final String? gambar;
  final String? deskripsi;

  Kavling({
    required this.id,
    required this.nama,
    required this.kapasitas,
    required this.hargaPerMalam,
    required this.fasilitas,
    required this.status,
    this.gambar,
    this.deskripsi,
  });

  factory Kavling.fromJson(Map<String, dynamic> json) {
    // Safe int parsing - handles int, String, double, and decimal strings like "150000.00"
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

    List<String> parseFasilitas(dynamic data) {
      if (data == null) return [];
      if (data is List) return data.map((e) => e.toString()).toList();
      if (data is String) {
        try {
          return data.split(',').map((e) => e.trim()).toList();
        } catch (_) {
          return [data];
        }
      }
      return [];
    }

    final harga = safeParseInt(json['harga_per_malam']);
    
    return Kavling(
      id: safeParseInt(json['id']),
      nama: json['nama']?.toString() ?? '',
      kapasitas: safeParseInt(json['kapasitas']),
      hargaPerMalam: harga,
      fasilitas: parseFasilitas(json['fasilitas']),
      status: json['status']?.toString() ?? 'tersedia',
      gambar: json['gambar_url']?.toString() ?? json['gambar']?.toString(),
      deskripsi: json['deskripsi']?.toString(),
    );
  }

  // API uses 'aktif' for available kavlings
  bool get isAvailable => status == 'aktif';

  String get formattedPrice {
    return 'Rp ${hargaPerMalam.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]}.',
    )}';
  }
}
