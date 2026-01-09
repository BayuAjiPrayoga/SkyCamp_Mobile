# 📱 LuhurCamp Mobile App Documentation

Dokumentasi lengkap untuk aplikasi mobile LuhurCamp - Camping Ground Booking App.

## 📋 Table of Contents

1. [Overview](#overview)
2. [Screenshots](#-screenshots)
3. [Tech Stack](#tech-stack)
4. [Project Structure](#project-structure)
5. [Features](#features)
6. [Setup & Installation](#setup--installation)
7. [Architecture](#architecture)
8. [API Integration](#api-integration)
9. [Firebase Configuration](#firebase-configuration)
10. [Push Notifications (FCM)](#push-notifications-fcm)
11. [State Management](#state-management)
12. [Screens & Navigation](#screens--navigation)
13. [Build & Release](#build--release)

---

## 🎯 Overview

LuhurCamp adalah aplikasi mobile untuk reservasi camping ground yang terintegrasi dengan backend Laravel. Aplikasi ini memungkinkan pengguna untuk:

-   Melihat daftar kavling (camping spot) yang tersedia
-   Melakukan booking kavling
-   Melihat status booking
-   Menerima notifikasi push
-   Melihat galeri dan pengumuman

---

## 📸 Screenshots

Berikut adalah tampilan aplikasi LuhurCamp Mobile:

### Login & Register

|                            Login Screen                            |                            Register Screen                            |
| :----------------------------------------------------------------: | :-------------------------------------------------------------------: |
| ![Login](docs/img%20asset/Screenshot%202026-01-09%20221832.png) | ![Register](docs/img%20asset/Screenshot%202026-01-09%20221917.png) |

### Home & Dashboard

|                            Home Screen                            |                             Dashboard Menu                             |
| :---------------------------------------------------------------: | :--------------------------------------------------------------------: |
| ![Home](docs/img%20asset/Screenshot%202026-01-09%20221953.png) | ![Dashboard](docs/img%20asset/Screenshot%202026-01-09%20222043.png) |

### Kavling & Booking

|                               Kavling List                                |                               Kavling Detail                                |
| :-----------------------------------------------------------------------: | :-------------------------------------------------------------------------: |
| ![Kavling List](docs/img%20asset/Screenshot%202026-01-09%20222112.png) | ![Kavling Detail](docs/img%20asset/Screenshot%202026-01-09%20222254.png) |

### Booking Process

|                               Booking Form                                |                               My Bookings                                |
| :-----------------------------------------------------------------------: | :----------------------------------------------------------------------: |
| ![Booking Form](docs/img%20asset/Screenshot%202026-01-09%20222534.png) | ![My Bookings](docs/img%20asset/Screenshot%202026-01-09%20222558.png) |

### Gallery & Pengumuman

|                               Gallery                                |                               Pengumuman                                |
| :------------------------------------------------------------------: | :---------------------------------------------------------------------: |
| ![Gallery](docs/img%20asset/Screenshot%202026-01-09%20222617.png) | ![Pengumuman](docs/img%20asset/Screenshot%202026-01-09%20222636.png) |

---

## 🛠 Tech Stack

| Technology         | Version | Purpose                  |
| ------------------ | ------- | ------------------------ |
| Flutter            | ^3.10.4 | Cross-platform framework |
| Dart               | ^3.10.4 | Programming language     |
| Riverpod           | ^2.5.1  | State management         |
| Dio                | ^5.4.1  | HTTP client              |
| GoRouter           | ^13.2.0 | Navigation & routing     |
| Firebase Core      | ^4.3.0  | Firebase integration     |
| Firebase Auth      | ^6.1.3  | Authentication           |
| Firebase Messaging | ^16.1.0 | Push notifications       |
| Google Sign-In     | ^6.2.1  | OAuth authentication     |

---

## 📁 Project Structure

```
lib/
├── main.dart                    # Entry point
├── core/                        # Core utilities
│   ├── config/                  # App configuration
│   │   └── api_config.dart      # API endpoints
│   ├── network/                 # Networking
│   │   └── api_client.dart      # Dio HTTP client
│   ├── router/                  # Navigation
│   │   └── app_router.dart      # GoRouter configuration
│   ├── services/                # Services
│   │   └── notification_service.dart  # FCM & local notifications
│   ├── storage/                 # Local storage
│   │   └── secure_storage.dart  # Secure token storage
│   └── theme/                   # UI Theme
│       └── app_theme.dart       # Colors, styles
├── data/                        # Data layer
│   ├── models/                  # Data models
│   │   ├── user_model.dart
│   │   ├── kavling_model.dart
│   │   ├── booking_model.dart
│   │   ├── peralatan_model.dart
│   │   ├── gallery_model.dart
│   │   └── announcement_model.dart
│   └── repositories/            # API repositories
│       ├── auth_repository.dart
│       ├── kavling_repository.dart
│       ├── booking_repository.dart
│       ├── peralatan_repository.dart
│       ├── gallery_repository.dart
│       └── announcement_repository.dart
└── presentation/                # UI layer
    ├── providers/               # Riverpod providers
    │   ├── auth_provider.dart
    │   ├── kavling_provider.dart
    │   ├── booking_provider.dart
    │   └── ...
    ├── screens/                 # App screens
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
    │   ├── announcement/
    │   │   └── announcement_list_screen.dart
    │   └── splash_screen.dart
    └── widgets/                 # Reusable widgets
        ├── custom_text_field.dart
        ├── loading_button.dart
        └── main_shell.dart
```

---

## ✨ Features

### 1. Authentication

-   **Email/Password Login** - Traditional login
-   **Google Sign-In** - OAuth via Firebase Auth
-   **Auto Login** - Token persistence with secure storage
-   **Profile Management** - Update name, phone, avatar

### 2. Kavling Management

-   Browse available camping spots
-   View kavling details (capacity, facilities, price)
-   Real-time availability status

### 3. Booking System

-   Multi-step booking flow
-   Date selection
-   Equipment rental (optional)
-   QR Code for check-in
-   Booking status tracking

### 4. Push Notifications

-   **Booking Updates** - Confirmed, rejected, cancelled
-   **Announcements** - Broadcast via FCM topics
-   **Local Notifications** - Foreground message display

### 5. Additional Features

-   Photo gallery with upload capability
-   Announcements/news feed
-   Equipment rental catalog

---

## 🚀 Setup & Installation

### Prerequisites

-   Flutter SDK ^3.10.4
-   Android Studio / VS Code
-   Firebase project configured
-   Backend API running

### Installation Steps

```bash
# 1. Clone repository
git clone https://github.com/BayuAjiPrayoga/SkyCamp_Mobile.git
cd arkanta_skycamp

# 2. Install dependencies
flutter pub get

# 3. Configure Firebase
# - Download google-services.json from Firebase Console
# - Place in android/app/google-services.json

# 4. Run the app
flutter run
```

### Environment Configuration

Edit `lib/core/config/api_config.dart`:

```dart
class ApiConfig {
  // Production
  static const String baseUrl = 'https://skycampmobile-production.up.railway.app/api/v1';

  // Development (local)
  // static const String baseUrl = 'http://192.168.1.x:8000/api/v1';
}
```

---

## 🏗 Architecture

Aplikasi menggunakan **Clean Architecture** dengan pemisahan layer:

```
┌─────────────────────────────────────────┐
│           PRESENTATION LAYER            │
│  (Screens, Widgets, Providers)          │
├─────────────────────────────────────────┤
│              DATA LAYER                 │
│  (Repositories, Models)                 │
├─────────────────────────────────────────┤
│              CORE LAYER                 │
│  (Network, Config, Services)            │
└─────────────────────────────────────────┘
```

### Data Flow

```
Screen → Provider → Repository → API Client → Backend
                ↓
            Update State
                ↓
        Screen Re-renders
```

---

## 🔌 API Integration

### API Client (`api_client.dart`)

```dart
// Singleton instance
final apiClient = ApiClient();

// Usage
final response = await apiClient.get('/kavlings');
final response = await apiClient.post('/bookings', data: {...});
```

### Available Endpoints

| Method | Endpoint               | Description      |
| ------ | ---------------------- | ---------------- |
| POST   | `/auth/login`          | Email login      |
| POST   | `/auth/register`       | Registration     |
| POST   | `/auth/firebase-login` | Google Sign-In   |
| GET    | `/user`                | Get current user |
| PUT    | `/user/fcm-token`      | Update FCM token |
| GET    | `/kavlings`            | List kavlings    |
| GET    | `/bookings`            | User bookings    |
| POST   | `/bookings`            | Create booking   |
| GET    | `/pengumuman`          | Announcements    |
| GET    | `/galeri`              | Gallery items    |

---

## 🔥 Firebase Configuration

### Required Files

1. **android/app/google-services.json** - Firebase Android config
2. **ios/Runner/GoogleService-Info.plist** - Firebase iOS config

### Firebase Console Setup

1. Create Firebase project
2. Enable Authentication (Email/Password + Google)
3. Enable Cloud Messaging
4. Add SHA-1 fingerprint for Android

### Get SHA-1 Fingerprint

```bash
cd android
./gradlew signingReport
```

---

## 📬 Push Notifications (FCM)

### Architecture

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Backend   │────▶│   Firebase  │────▶│  Mobile App │
│   Laravel   │     │     FCM     │     │   Flutter   │
└─────────────┘     └─────────────┘     └─────────────┘
```

### Implementation

#### 1. Notification Service (`notification_service.dart`)

```dart
class NotificationService {
  // Initialize FCM
  Future<void> initialize() async {
    await _requestPermission();
    await _initializeLocalNotifications();
    await _getFCMToken();
    await sendTokenToBackend(_fcmToken);
  }

  // Subscribe to topic
  Future<void> subscribeToTopic(String topic) async {
    await _messaging.subscribeToTopic(topic);
  }

  // Send token to backend
  Future<bool> sendTokenToBackend(String? token) async {
    await apiClient.put('/user/fcm-token', data: {'fcm_token': token});
  }
}
```

#### 2. Notification Types

| Type              | Trigger                    | Target                 |
| ----------------- | -------------------------- | ---------------------- |
| Booking Confirmed | Admin confirms booking     | User device            |
| Booking Rejected  | Admin rejects booking      | User device            |
| Booking Cancelled | User cancels booking       | User device            |
| Announcement      | Admin creates announcement | Topic: `announcements` |

#### 3. Handling Notifications

```dart
// Foreground - show local notification
FirebaseMessaging.onMessage.listen(_handleForegroundMessage);

// Background - handle tap to navigate
FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);

// App opened from notification
final initialMessage = await _messaging.getInitialMessage();
```

---

## 🔄 State Management

### Riverpod Providers

```dart
// Auth state
final authProvider = StateNotifierProvider<AuthNotifier, AuthState>(...);

// Kavling state
final kavlingProvider = StateNotifierProvider<KavlingNotifier, KavlingState>(...);

// Booking state
final bookingProvider = StateNotifierProvider<BookingNotifier, BookingState>(...);
```

### Usage in Widgets

```dart
class MyScreen extends ConsumerWidget {
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // Watch state
    final authState = ref.watch(authProvider);

    // Trigger action
    ref.read(authProvider.notifier).login(email, password);
  }
}
```

---

## 🗺 Screens & Navigation

### Route Structure

```
/splash          → SplashScreen (auto-redirect)
/login           → LoginScreen
/register        → RegisterScreen
/home            → HomeScreen (dashboard)
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

### Navigation Guards

```dart
redirect: (context, state) {
  final isLoggedIn = authState.status == AuthStatus.authenticated;

  if (!isLoggedIn && !isAuthRoute) {
    return '/login';  // Redirect to login
  }

  if (isLoggedIn && isAuthRoute) {
    return '/home';   // Redirect to home
  }

  return null;
}
```

---

## 📦 Build & Release

### Debug Build

```bash
flutter run
```

### Release Build (Android)

```bash
# APK
flutter build apk --release

# App Bundle
flutter build appbundle --release
```

### Release Build (iOS)

```bash
flutter build ios --release
```

### Build Output Locations

-   APK: `build/app/outputs/flutter-apk/app-release.apk`
-   AAB: `build/app/outputs/bundle/release/app-release.aab`
-   iOS: `build/ios/iphoneos/Runner.app`

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

## 📝 Changelog

### v1.0.0 (Current)

-   ✅ Authentication (Email + Google Sign-In)
-   ✅ Kavling browsing & booking
-   ✅ Push notifications (FCM)
-   ✅ Booking management
-   ✅ Profile management
-   ✅ Gallery & Announcements

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📄 License

This project is proprietary software for LuhurCamp.

---

## 📞 Support

For technical support, contact:

-   Email: support@luhurcamp.com
-   Developer: Bayu Aji Prayoga
