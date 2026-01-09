# 📱 SkyCamp Mobile App - Project Context

> File ini berisi konteks proyek untuk membantu AI Agent memahami codebase dengan cepat.

---

## 📋 Ringkasan Proyek

**SkyCamp Mobile** adalah aplikasi mobile untuk sistem reservasi camping ground LuhurCamp. Dibangun dengan Flutter dan terintegrasi dengan backend Laravel. Fitur utama:

- Booking kavling (camping spot)
- Sewa peralatan camping
- Push notification real-time
- QR Code tiket digital
- Gallery dan pengumuman

---

## 🛠️ Technology Stack

| Layer              | Teknologi                       |
| :----------------- | :------------------------------ |
| Framework          | Flutter 3.10.4+                 |
| Language           | Dart 3.x                        |
| State Management   | Riverpod 2.5.1                  |
| Navigation         | GoRouter 13.2.0                 |
| HTTP Client        | Dio 5.4.1                       |
| Auth               | Firebase Auth + Laravel Sanctum |
| Push Notification  | Firebase Cloud Messaging        |
| Local Notification | Flutter Local Notifications     |
| Secure Storage     | Flutter Secure Storage          |

---

## 📁 Struktur Folder

```
lib/
├── main.dart                      # Entry point
├── core/                          # ⭐ Core utilities & config
│   ├── config/
│   │   └── api_config.dart        # Base URL API
│   ├── network/
│   │   └── api_client.dart        # Dio HTTP client (singleton)
│   ├── router/
│   │   └── app_router.dart        # GoRouter configuration
│   ├── services/
│   │   └── notification_service.dart  # FCM handling
│   ├── storage/
│   │   └── secure_storage.dart    # Token storage
│   └── theme/
│       └── app_theme.dart         # Colors, TextStyles
│
├── data/                          # ⭐ Data layer
│   ├── models/                    # Data models (fromJson, toJson)
│   │   ├── user_model.dart
│   │   ├── kavling_model.dart
│   │   ├── booking_model.dart
│   │   ├── peralatan_model.dart
│   │   ├── gallery_model.dart
│   │   └── announcement_model.dart
│   └── repositories/              # API calls
│       ├── auth_repository.dart
│       ├── kavling_repository.dart
│       ├── booking_repository.dart
│       ├── peralatan_repository.dart
│       ├── gallery_repository.dart
│       └── announcement_repository.dart
│
└── presentation/                  # ⭐ UI layer
    ├── providers/                 # Riverpod state providers
    │   ├── auth_provider.dart
    │   ├── kavling_provider.dart
    │   ├── booking_provider.dart
    │   └── ...
    ├── screens/                   # App screens
    │   ├── splash_screen.dart
    │   ├── auth/
    │   │   ├── login_screen.dart
    │   │   └── register_screen.dart
    │   ├── home/
    │   │   └── home_screen.dart
    │   ├── kavling/
    │   │   ├── kavling_list_screen.dart
    │   │   └── kavling_detail_screen.dart
    │   ├── booking/
    │   │   ├── booking_flow_screen.dart
    │   │   ├── my_bookings_screen.dart
    │   │   └── booking_detail_screen.dart
    │   ├── profile/
    │   │   └── profile_screen.dart
    │   ├── gallery/
    │   │   └── gallery_screen.dart
    │   ├── peralatan/
    │   │   └── peralatan_list_screen.dart
    │   └── announcement/
    │       └── announcement_list_screen.dart
    └── widgets/                   # Reusable widgets
        ├── custom_text_field.dart
        ├── loading_button.dart
        └── main_shell.dart        # Bottom navigation shell
```

---

## 🏗️ Architecture Pattern

Aplikasi menggunakan **Clean Architecture** dengan 3 layer:

```
┌─────────────────────────────────────────┐
│         PRESENTATION LAYER              │
│  (Screens, Widgets, Providers)          │
│  - UI rendering                         │
│  - User interaction                     │
│  - State management (Riverpod)          │
├─────────────────────────────────────────┤
│            DATA LAYER                   │
│  (Repositories, Models)                 │
│  - API calls                            │
│  - Data transformation                  │
│  - JSON serialization                   │
├─────────────────────────────────────────┤
│            CORE LAYER                   │
│  (Network, Config, Services)            │
│  - HTTP client (Dio)                    │
│  - Routing (GoRouter)                   │
│  - Push notifications (FCM)             │
└─────────────────────────────────────────┘
```

### Data Flow

```
User Action → Screen → Provider → Repository → ApiClient → Backend API
                         ↓
                   Update State
                         ↓
                  Screen Re-renders
```

---

## 🔄 State Management (Riverpod)

### Pattern yang Digunakan

```dart
// 1. State class
class AuthState {
  final AuthStatus status;
  final UserModel? user;
  final String? error;
}

// 2. StateNotifier
class AuthNotifier extends StateNotifier<AuthState> {
  AuthNotifier() : super(AuthState.initial());

  Future<void> login(String email, String password) async { ... }
  Future<void> logout() async { ... }
}

// 3. Provider
final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
```

### Penggunaan di Widget

```dart
class MyScreen extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // Watch state (rebuild saat berubah)
    final authState = ref.watch(authProvider);

    // Trigger action
    ref.read(authProvider.notifier).login(email, password);
  }
}
```

---

## 🌐 API Integration

### Base URL Configuration (`api_config.dart`)

```dart
class ApiConfig {
  // Production
  static const String baseUrl = 'https://skycampmobile-production.up.railway.app/api/v1';

  // Development (local)
  // static const String baseUrl = 'http://192.168.1.x:8000/api/v1';
}
```

### API Client (`api_client.dart`)

```dart
// Singleton Dio instance dengan interceptors
final apiClient = ApiClient();

// GET request
final response = await apiClient.get('/kavlings');

// POST request
final response = await apiClient.post('/bookings', data: {
  'kavling_id': 1,
  'tanggal_checkin': '2026-01-15',
  'tanggal_checkout': '2026-01-17',
});

// Dengan auth token (otomatis dari interceptor)
// Header: Authorization: Bearer {token}
```

### Response Handling

```dart
// Repository pattern
class KavlingRepository {
  Future<List<KavlingModel>> getKavlings() async {
    final response = await apiClient.get('/kavlings');
    final List data = response.data['data'];
    return data.map((e) => KavlingModel.fromJson(e)).toList();
  }
}
```

---

## 🔐 Authentication Flow

1. **Login Screen** → User input email & password
2. **AuthRepository** → POST `/auth/login`
3. **Backend** → Return `{ token: "xxx", user: {...} }`
4. **SecureStorage** → Simpan token
5. **ApiClient Interceptor** → Auto-attach token ke semua request
6. **AuthProvider** → Update state ke `authenticated`
7. **GoRouter** → Redirect ke Home

### Google Sign-In Flow

1. **GoogleSignIn** → Get ID Token dari Firebase
2. **AuthRepository** → POST `/auth/firebase-login` dengan ID Token
3. Backend validasi token via Firebase Admin SDK
4. Return Sanctum token + user data
5. Lanjut seperti flow normal

---

## 📬 Push Notifications

### Initialization (`notification_service.dart`)

```dart
class NotificationService {
  Future<void> initialize() async {
    // 1. Request permission
    await _requestPermission();

    // 2. Get FCM token
    final token = await _messaging.getToken();

    // 3. Send token ke backend
    await sendTokenToBackend(token);

    // 4. Subscribe to announcements topic
    await _messaging.subscribeToTopic('announcements');

    // 5. Setup foreground handler
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
  }
}
```

### Notification Types

| Type              | Trigger                  | Handling                      |
| :---------------- | :----------------------- | :---------------------------- |
| Booking Confirmed | Admin konfirmasi booking | Navigate ke Booking Detail    |
| Booking Rejected  | Admin tolak booking      | Navigate ke Booking Detail    |
| Announcement      | Admin buat pengumuman    | Navigate ke Announcement List |

---

## 🗺️ Navigation (GoRouter)

### Route Structure

```
/splash          → SplashScreen (auto-redirect)
/login           → LoginScreen
/register        → RegisterScreen
/home            → HomeScreen (dengan BottomNav)
  └── /announcement → AnnouncementListScreen
  └── /peralatan    → PeralatanListScreen
/kavlings        → KavlingListScreen
  └── /:id          → KavlingDetailScreen
/bookings        → MyBookingsScreen
  └── /:id          → BookingDetailScreen
/booking/new     → BookingFlowScreen
/gallery         → GalleryScreen
/profile         → ProfileScreen
```

### Navigation Guard

```dart
redirect: (context, state) {
  final isLoggedIn = authState.status == AuthStatus.authenticated;
  final isAuthRoute = ['/login', '/register'].contains(state.matchedLocation);

  if (!isLoggedIn && !isAuthRoute) {
    return '/login';  // Redirect ke login jika belum auth
  }

  if (isLoggedIn && isAuthRoute) {
    return '/home';   // Redirect ke home jika sudah auth
  }

  return null;  // Tidak redirect
}
```

---

## 🎨 Theme & Styling

### Colors (`app_theme.dart`)

```dart
class AppColors {
  static const primary = Color(0xFF2E7D32);      // Green
  static const secondary = Color(0xFF81C784);
  static const background = Color(0xFFF5F5F5);
  static const error = Color(0xFFD32F2F);
}
```

### Text Styles

```dart
class AppTextStyles {
  static const heading1 = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.bold,
  );
  // ...
}
```

---

## 📦 Dependencies Penting (`pubspec.yaml`)

```yaml
dependencies:
  flutter_riverpod: ^2.5.1 # State management
  go_router: ^13.2.0 # Navigation
  dio: ^5.4.1 # HTTP client
  firebase_core: ^4.3.0 # Firebase
  firebase_auth: ^6.1.3 # Firebase Auth
  firebase_messaging: ^16.1.0 # Push notifications
  google_sign_in: ^6.2.1 # Google OAuth
  flutter_secure_storage: ^9.0.0 # Secure token storage
  cached_network_image: ^3.3.1 # Image caching
  qr_flutter: ^4.1.0 # QR Code display
  image_picker: ^1.0.7 # Image picker
  intl: ^0.19.0 # Date formatting
```

---

## 🧪 Testing

```bash
# Run all tests
flutter test

# Run specific test
flutter test test/unit/auth_test.dart

# Run with coverage
flutter test --coverage
```

---

## 📱 Build Commands

```bash
# Debug
flutter run

# Release APK
flutter build apk --release

# Release App Bundle (untuk Play Store)
flutter build appbundle --release

# iOS
flutter build ios --release
```

### Output Locations

- APK: `build/app/outputs/flutter-apk/app-release.apk`
- AAB: `build/app/outputs/bundle/release/app-release.aab`

---

## ⚙️ Firebase Configuration

### Required Files

- Android: `android/app/google-services.json`
- iOS: `ios/Runner/GoogleService-Info.plist`

### Get SHA-1 (untuk Google Sign-In)

```bash
cd android
./gradlew signingReport
```

---

## 📝 Konvensi Kode

1. **File naming**: `snake_case.dart`
2. **Class naming**: `PascalCase`
3. **Variable/function**: `camelCase`
4. **Folder structure**: Feature-based di `screens/`
5. **State**: Selalu gunakan `StateNotifier` + `StateNotifierProvider`
6. **API calls**: Selalu di Repository, bukan di Provider/Screen

---

## 🔗 Repository Terkait

| Repository                                                               | Deskripsi                   |
| :----------------------------------------------------------------------- | :-------------------------- |
| [LuhurCamp-Web-App](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App) | Backend Laravel + Web Admin |
| [SkyCamp_Mobile](https://github.com/BayuAjiPrayoga/SkyCamp_Mobile)       | Mobile App ini              |

---

## 📚 Dokumentasi Teknis (di Backend Repo)

- [SRS](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SRS.md) - Software Requirement Specification
- [SDD](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SDD.md) - System Design Document
- [Technical Spec](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Technical_Spec.md) - Technical Specification
- [Business Logic](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Business_Logic.md) - Business Logic & Flowchart

---

_Last updated: January 2026_
