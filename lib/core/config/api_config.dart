// LuhurCamp API Configuration

class ApiConfig {
  // Smart Base URL Selection
  static String get baseUrl {
    // 🔴 SALAH (Hanya untuk antar-service di dalam Railway)
    // return 'https://luhurcamp-web-app.railway.internal/api/v1';

    // ✅ BENAR (Gunakan Public Domain dari Railway agar bisa diakses HP)
    // Update: Menggunakan domain production yang benar dengan suffix -production
    // ✅ BENAR (Gunakan Public Domain dari Railway agar bisa diakses HP)
    // Update: Menggunakan domain production yang benar dengan suffix -production
    return 'https://luhurcamp-web-app-production.up.railway.app/api/v1';

    // ➤ Production URL (Cloudflare Tunnel - Lama)
    // return 'https://api.luhur.my.id/api/v1';

    // ➤ Android Emulator
    // return 'http://10.0.2.2:8000/api/v1';

    // ➤ Local Development (Device Fisik) - Updated for Smart Scan Testing
    // return 'http://192.168.1.117:8000/api/v1';
  }

  // API Endpoints
  static const String login = '/login';
  static const String register = '/register';
  static const String logout = '/logout';
  static const String user = '/user';
  static const String updateProfile = '/user';

  static const String kavlings = '/kavlings';
  static const String peralatan = '/peralatan';
  static const String bookings = '/bookings';
  static const String galleries = '/galleries';
  static const String announcements = '/announcements';
  static const String weather = '/weather';

  // Timeouts
  static const int connectTimeout = 30000;
  static const int receiveTimeout = 30000;
}
