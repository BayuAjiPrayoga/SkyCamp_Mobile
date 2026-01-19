// Kavling Model - Data slot/area camping

class Kavling {
  final int id;
  final String nama;
  final int kapasitas;
  final int hargaPerMalam;
  final List<String> fasilitas;
  final String status;
  final String? gambar;
  final String? deskripsi;
  final bool isAvailable;

  Kavling({
    required this.id,
    required this.nama,
    required this.kapasitas,
    required this.hargaPerMalam,
    required this.fasilitas,
    required this.status,
    this.gambar,
    this.deskripsi,
    this.isAvailable = true,
  });

  factory Kavling.fromJson(Map<String, dynamic> json) {
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

    return Kavling(
      id: safeParseInt(json['id']),
      nama: json['nama']?.toString() ?? '',
      kapasitas: safeParseInt(json['kapasitas']),
      hargaPerMalam: safeParseInt(json['harga_per_malam']),
      fasilitas: parseFasilitas(json['fasilitas']),
      status: json['status']?.toString() ?? 'tersedia',
      gambar: json['gambar_url']?.toString() ?? json['gambar']?.toString(),
      deskripsi: json['deskripsi']?.toString(),
      isAvailable: json['is_available'] ?? (json['status'] == 'aktif'),
    );
  }

  String get formattedPrice => 'Rp ${hargaPerMalam.toString().replaceAllMapped(
    RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'), (m) => '${m[1]}.',
  )}';
}
