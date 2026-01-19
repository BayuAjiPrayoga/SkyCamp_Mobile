// User Model - Representasi data pengguna

class User {
  final int id;
  final String name;
  final String email;
  final String? phone;
  final String? avatar;
  final DateTime? createdAt;
  final String? authProvider;

  User({
    required this.id,
    required this.name,
    required this.email,
    this.phone,
    this.avatar,
    this.createdAt,
    this.authProvider,
  });

  // Empty user untuk default/placeholder
  factory User.empty() => User(id: 0, name: '', email: '');

  // Parse dari JSON API
  factory User.fromJson(Map<String, dynamic> json) {
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

    return User(
      id: safeParseInt(json['id']),
      name: json['name']?.toString() ?? '',
      email: json['email']?.toString() ?? '',
      phone: json['phone']?.toString(),
      avatar: json['avatar']?.toString(),
      createdAt: json['created_at'] != null 
          ? DateTime.tryParse(json['created_at'].toString()) 
          : null,
      authProvider: json['auth_provider']?.toString(),
    );
  }

  // Serialize ke JSON
  Map<String, dynamic> toJson() => {
    'id': id,
    'name': name,
    'email': email,
    'phone': phone,
    'avatar': avatar,
  };
}
