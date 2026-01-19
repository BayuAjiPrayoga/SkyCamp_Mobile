// Konfigurasi API - Base URL dan Endpoints
// Lihat Penjelasan.md untuk dokumentasi lengkap

class ApiConfig {
  // Base URL API (Production)
  static String get baseUrl {
    // Uncomment sesuai environment:
    // return 'http://192.168.100.19:8000/api/v1';   // Local development
    // return 'http://10.0.2.2:8000/api/v1';         // Android Emulator
    return 'https://krevix.my.id/api/v1';            // Production
  }

  // Auth endpoints
  static const String login = '/login';
  static const String register = '/register';
  static const String logout = '/logout';
  static const String user = '/user';
  static const String updateProfile = '/user';
  
  // Feature endpoints
  static const String kavlings = '/kavlings';
  static const String peralatan = '/peralatan';
  static const String bookings = '/bookings';
  static const String galleries = '/galleries';
  static const String announcements = '/announcements';
  static const String fcmToken = '/fcm-token';

  // Timeout configuration (milliseconds)
  static const int connectTimeout = 30000;
  static const int receiveTimeout = 30000;
}
