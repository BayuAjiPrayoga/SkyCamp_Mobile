class Peralatan {
  final int id;
  final String nama;
  final String kategori;
  final int hargaSewa;
  final int stokTotal;
  final String kondisi;
  final String? gambar;
  final String? deskripsi;

  Peralatan({
    required this.id,
    required this.nama,
    required this.kategori,
    required this.hargaSewa,
    required this.stokTotal,
    required this.kondisi,
    this.gambar,
    this.deskripsi,
  });

  factory Peralatan.fromJson(Map<String, dynamic> json) {
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

    return Peralatan(
      id: safeParseInt(json['id']),
      nama: json['nama']?.toString() ?? '',
      kategori: json['kategori']?.toString() ?? '',
      hargaSewa: safeParseInt(json['harga_sewa']),
      stokTotal: safeParseInt(json['stok_total']),
      kondisi: json['kondisi']?.toString() ?? 'baik',
      gambar: json['gambar']?.toString(),
      deskripsi: json['deskripsi']?.toString(),
    );
  }

  bool get isAvailable => stokTotal > 0 && kondisi == 'baik';

  String get formattedPrice {
    return 'Rp ${hargaSewa.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]}.',
    )}';
  }

  String get kondisiLabel {
    switch (kondisi) {
      case 'baik':
        return 'Tersedia';
      case 'perlu_perbaikan':
        return 'Maintenance';
      case 'rusak':
        return 'Tidak Tersedia';
      default:
        return kondisi;
    }
  }
}
