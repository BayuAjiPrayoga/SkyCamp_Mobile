class GalleryPhoto {
  final int id;
  final int? userId;
  final String imageUrl;
  final String? caption;
  final String status;
  final DateTime? createdAt;
  final String? userName;

  GalleryPhoto({
    required this.id,
    this.userId,
    required this.imageUrl,
    this.caption,
    required this.status,
    this.createdAt,
    this.userName,
  });

  factory GalleryPhoto.fromJson(Map<String, dynamic> json) {
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

    return GalleryPhoto(
      id: safeParseInt(json['id']),
      userId: json['user_id'] != null ? safeParseInt(json['user_id']) : null,
      imageUrl: json['image_url']?.toString() ?? json['gambar']?.toString() ?? '',
      caption: json['caption']?.toString() ?? json['keterangan']?.toString(),
      status: json['status']?.toString() ?? 'pending',
      createdAt: json['created_at'] != null 
          ? DateTime.tryParse(json['created_at'].toString()) 
          : null,
      userName: json['user']?['name']?.toString(),
    );
  }

  bool get isApproved => status == 'approved';
}
