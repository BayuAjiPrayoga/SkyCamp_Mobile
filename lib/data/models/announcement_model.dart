
class Announcement {
  final int id;
  final String title;
  final String content;
  final String type;
  final bool isActive;
  final DateTime createdAt;

  Announcement({
    required this.id,
    required this.title,
    required this.content,
    required this.type,
    required this.isActive,
    required this.createdAt,
  });

  factory Announcement.fromJson(Map<String, dynamic> json) {
    return Announcement(
      id: json['id'],
      title: json['title'],
      content: json['content'],
      type: json['type'] ?? 'info',
      isActive: json['is_active'] == 1 || json['is_active'] == true,
      createdAt: DateTime.tryParse(json['created_at']?.toString() ?? '') ?? 
          DateTime.now(),
    );
  }
}
