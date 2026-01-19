# Dokumentasi Arsitektur Aplikasi LuhurCamp Mobile

## Daftar Isi
1. [Pendahuluan](#pendahuluan)
2. [Arsitektur Aplikasi](#arsitektur-aplikasi)
3. [Struktur Folder](#struktur-folder)
4. [Penjelasan Layer](#penjelasan-layer)
5. [Pola Desain (Design Patterns)](#pola-desain)
6. [Alur Data (Data Flow)](#alur-data)
7. [Referensi File](#referensi-file)

---

## Pendahuluan

**LuhurCamp Mobile** adalah aplikasi mobile berbasis Flutter untuk sistem pemesanan camping. Aplikasi ini mengimplementasikan arsitektur **Clean Architecture** yang dimodifikasi dengan pendekatan **Feature-First Organization** untuk memastikan maintainability, scalability, dan readability kode.

### Teknologi Utama
| Teknologi | Fungsi |
|-----------|--------|
| Flutter/Dart | Framework UI cross-platform |
| Riverpod | State management |
| GoRouter | Navigasi deklaratif |
| Dio | HTTP client |
| Firebase | Authentication & Push Notifications |

---

## Arsitektur Aplikasi

Aplikasi ini menggunakan **Layered Architecture** dengan 3 layer utama:

```
┌─────────────────────────────────────────────────┐
│              PRESENTATION LAYER                  │
│    (Screens, Widgets, Providers/Controllers)     │
├─────────────────────────────────────────────────┤
│                 DATA LAYER                       │
│         (Models, Repositories)                   │
├─────────────────────────────────────────────────┤
│                 CORE LAYER                       │
│   (Config, Network, Router, Services, Theme)     │
└─────────────────────────────────────────────────┘
```

### Prinsip Arsitektur

1. **Separation of Concerns**: Setiap layer memiliki tanggung jawab spesifik
2. **Dependency Rule**: Layer atas bergantung pada layer bawah, tidak sebaliknya
3. **Single Source of Truth**: Data dikelola terpusat melalui Providers
4. **Immutability**: State bersifat immutable untuk predictability

---

## Struktur Folder

```
lib/
├── main.dart                         # Entry point aplikasi, inisialisasi Firebase & Riverpod
│
├── core/                             # Layer fondasi (infrastruktur teknis)
│   ├── config/
│   │   └── api_config.dart           # Base URL & daftar endpoint API
│   ├── network/
│   │   └── api_client.dart           # HTTP client Dio + auto-attach token
│   ├── router/
│   │   └── app_router.dart           # Navigasi GoRouter + redirect auth
│   ├── services/
│   │   └── notification_service.dart # Push notification (FCM)
│   ├── storage/
│   │   └── secure_storage.dart       # Simpan token aman (encrypted)
│   └── theme/
│       └── app_theme.dart            # Warna, font, styling UI
│
├── data/                             # Layer data (models & repositories)
│   ├── models/
│   │   ├── user_model.dart           # Data user (id, nama, email, avatar)
│   │   ├── booking_model.dart        # Data pemesanan (tanggal, kavling, items, status)
│   │   ├── kavling_model.dart        # Data slot camping (nama, kapasitas, harga, fasilitas)
│   │   ├── peralatan_model.dart      # Data peralatan sewa (nama, kategori, harga, stok)
│   │   ├── gallery_model.dart        # Data foto galeri (url, caption, status approval)
│   │   └── announcement_model.dart   # Data pengumuman (judul, isi, tipe)
│   │
│   └── repositories/                 # Akses data ke API backend
│       ├── auth_repository.dart      # Login, register, Google Sign-In, profile
│       ├── booking_repository.dart   # CRUD booking, upload pembayaran
│       ├── kavling_repository.dart   # Ambil daftar & detail kavling
│       ├── peralatan_repository.dart # Ambil daftar & detail peralatan
│       ├── gallery_repository.dart   # Ambil & upload foto galeri
│       └── announcement_repository.dart # Ambil pengumuman
│
└── presentation/                     # Layer UI (tampilan & interaksi)
    ├── providers/                    # State management (Riverpod)
    │   ├── auth_provider.dart        # State login/logout, data user
    │   ├── booking_provider.dart     # State booking, cart peralatan
    │   ├── kavling_provider.dart     # State daftar kavling (cache 5 menit)
    │   ├── peralatan_provider.dart   # State peralatan + filter kategori
    │   ├── gallery_provider.dart     # State galeri foto
    │   └── announcement_provider.dart # State pengumuman (cache 10 menit)
    │
    ├── screens/                      # Halaman-halaman UI
    │   ├── auth/                     # Login, Register, Forgot Password
    │   ├── home/                     # Dashboard utama
    │   ├── booking/                  # List booking, detail, flow wizard
    │   ├── kavling/                  # List & detail kavling
    │   ├── peralatan/                # List peralatan
    │   ├── gallery/                  # Galeri foto
    │   ├── announcement/             # Daftar pengumuman
    │   └── profile/                  # Profil & edit user
    │
    └── widgets/                      # Komponen UI reusable
        ├── custom_button.dart        # Tombol dengan styling konsisten
        ├── custom_text_field.dart    # Input field dengan validasi
        ├── loading_widget.dart       # Indicator loading
        └── ...                       # Widget lainnya
```

---

## Penjelasan Layer

### 1. Core Layer (`lib/core/`)

Layer fondasi yang menyediakan infrastruktur teknis untuk seluruh aplikasi.

| Folder | File | Fungsi |
|--------|------|--------|
| `config/` | `api_config.dart` | Mendefinisikan base URL API dan daftar endpoint |
| `network/` | `api_client.dart` | Wrapper Dio dengan interceptor untuk auth token |
| `router/` | `app_router.dart` | Konfigurasi navigasi GoRouter dengan redirect logic |
| `services/` | `notification_service.dart` | Integrasi Firebase Cloud Messaging (FCM) |
| `storage/` | `secure_storage.dart` | Penyimpanan aman untuk token (FlutterSecureStorage) |
| `theme/` | `app_theme.dart` | Design system: warna, font, dan styling |

### 2. Data Layer (`lib/data/`)

Layer yang bertanggung jawab atas representasi dan akses data.

#### Models (`lib/data/models/`)

Model adalah representasi objek data dalam aplikasi. Setiap model memiliki:
- **Properties**: Atribut data
- **fromJson()**: Factory untuk parsing JSON dari API
- **toJson()**: Method untuk serialisasi ke JSON

| Model | Deskripsi |
|-------|-----------|
| `User` | Data pengguna (id, name, email, phone, avatar) |
| `Booking` | Data pemesanan camping (kavling, tanggal, status, items) |
| `Kavling` | Unit slot camping (nama, kapasitas, harga, fasilitas) |
| `Peralatan` | Item peralatan rental (nama, kategori, harga, stok) |
| `GalleryPhoto` | Foto galeri (imageUrl, caption, status approval) |
| `Announcement` | Pengumuman admin (title, content, type) |

#### Repositories (`lib/data/repositories/`)

Repository adalah abstraksi untuk akses data. Menerapkan **Repository Pattern**:
- Menyembunyikan detail implementasi API
- Menyediakan interface bersih untuk layer atas
- Menangani parsing response dan error handling

| Repository | Operasi Utama |
|------------|---------------|
| `AuthRepository` | login, register, logout, updateProfile, googleSignIn |
| `BookingRepository` | getMyBookings, createBooking, uploadPayment, cancel |
| `KavlingRepository` | getAll, getById, getAvailable |
| `PeralatanRepository` | getAll, getById, getByCategory |
| `GalleryRepository` | getAll, uploadPhoto |
| `AnnouncementRepository` | getAnnouncements |

### 3. Presentation Layer (`lib/presentation/`)

Layer yang menangani UI dan interaksi pengguna.

#### Providers (`lib/presentation/providers/`)

Provider mengelola state aplikasi menggunakan **Riverpod StateNotifier Pattern**:

```dart
// Struktur Provider
class ExampleState {           // Immutable state
  final List<Data> items;
  final bool isLoading;
  final String? error;
}

class ExampleNotifier extends StateNotifier<ExampleState> {
  // Business logic
  Future<void> loadData() async { ... }
}

final exampleProvider = StateNotifierProvider<ExampleNotifier, ExampleState>(...);
```

| Provider | Fungsi |
|----------|--------|
| `authProvider` | Status login, user data, auth operations |
| `bookingProvider` | Daftar booking, booking flow wizard, cart |
| `kavlingProvider` | Daftar kavling dengan caching |
| `peralatanProvider` | Daftar peralatan dengan filter kategori |
| `galleryProvider` | Foto galeri, upload foto |
| `announcementProvider` | Pengumuman dengan caching |

#### Screens (`lib/presentation/screens/`)

Setiap screen merepresentasikan satu halaman dalam aplikasi. Screens:
- Menggunakan `ConsumerWidget` atau `ConsumerStatefulWidget` untuk akses Riverpod
- Memanggil provider untuk data dan aksi
- Menampilkan UI berdasarkan state

#### Widgets (`lib/presentation/widgets/`)

Komponen UI reusable yang digunakan di berbagai screens:
- `CustomButton` - Tombol dengan styling konsisten
- `LoadingWidget` - Indicator loading
- `ErrorWidget` - Tampilan error
- Dan lainnya

---

## Pola Desain

### 1. Repository Pattern

```
UI → Provider → Repository → API
         ↓           ↓
       State       Model
```

**Keuntungan:**
- Abstraksi akses data
- Mudah di-mock untuk testing
- Single point of change untuk API

### 2. StateNotifier Pattern (Riverpod)

```dart
// State immutable
state = state.copyWith(isLoading: true);

// Update otomatis trigger rebuild UI
```

**Keuntungan:**
- State predictable dan traceable
- Rebuilds efisien hanya pada widget yang subscribe
- Mudah debug dengan Riverpod DevTools

### 3. Factory Pattern (Models)

```dart
User.fromJson(json)   // Parsing dari API
User.empty()          // Default instance
```

### 4. Singleton Pattern (Services)

```dart
final apiClient = ApiClient();        // Global instance
final notificationService = NotificationService();
```

### 5. Result Pattern (Error Handling)

```dart
class AuthResult {
  final bool isSuccess;
  final User? user;
  final String? message;
}
```

---

## Alur Data

### Alur Login

```
LoginScreen
    │
    ▼
authProvider.notifier.login(email, password)
    │
    ▼
AuthNotifier.login()
    │
    ▼
AuthRepository.login()
    │
    ▼
ApiClient.post('/login')
    │
    ▼
Backend Laravel API
    │
    ▼
Response + Token
    │
    ▼
SecureStorage.saveToken()
    │
    ▼
state = state.copyWith(user: user, status: authenticated)
    │
    ▼
GoRouter redirect → HomeScreen
```

### Alur Booking

```
1. User pilih kavling → BookingNotifier.selectKavling()
2. User pilih tanggal → BookingNotifier.setDates()
3. User pilih peralatan → BookingNotifier.addToCart()
4. User submit → BookingNotifier.submitBooking()
                        │
                        ▼
              BookingRepository.createBooking()
                        │
                        ▼
                 Backend API
                        │
                        ▼
              Booking created + Notification
```

---

## Referensi File

### Entry Point & Inisialisasi

| File | Deskripsi |
|------|-----------|
| `main.dart` | Entry point. Inisialisasi Firebase, NotificationService, dan ProviderScope |

### Core Layer

| File | Deskripsi |
|------|-----------|
| `api_config.dart` | Base URL API (`https://krevix.my.id/api/v1`) dan semua endpoint |
| `api_client.dart` | Dio wrapper dengan interceptor untuk auto-attach Bearer token |
| `app_router.dart` | GoRouter dengan redirect logic berdasarkan auth state |
| `notification_service.dart` | FCM: request permission, token sync, handle foreground/background messages |
| `secure_storage.dart` | FlutterSecureStorage (mobile) / SharedPreferences (web) |
| `app_theme.dart` | AppColors dan ThemeData dengan Google Fonts Poppins |

### Data Layer - Models

| File | Properties Utama |
|------|------------------|
| `user_model.dart` | id, name, email, phone, avatar, authProvider |
| `booking_model.dart` | code, tanggalCheckIn/Out, totalHarga, status, kavling, items |
| `kavling_model.dart` | nama, kapasitas, hargaPerMalam, fasilitas, isAvailable |
| `peralatan_model.dart` | nama, kategori, hargaSewa, stokTotal, kondisi |
| `gallery_model.dart` | imageUrl, caption, status, userName |
| `announcement_model.dart` | title, content, type, isActive |

### Data Layer - Repositories

| File | Methods |
|------|---------|
| `auth_repository.dart` | login, register, logout, getUser, updateProfile, loginWithGoogle, sendPasswordResetEmail |
| `booking_repository.dart` | getMyBookings, getById, createBooking, uploadPayment, cancelBooking |
| `kavling_repository.dart` | getAll, getById, getAvailable |
| `peralatan_repository.dart` | getAll, getById, getByCategory, getAvailable |
| `gallery_repository.dart` | getAll, uploadPhoto |
| `announcement_repository.dart` | getAnnouncements |

### Presentation Layer - Providers

| File | State Properties | Key Methods |
|------|------------------|-------------|
| `auth_provider.dart` | status, user, errorMessage | login, register, logout, checkAuthStatus |
| `booking_provider.dart` | bookings, selectedKavling, cart | loadBookings, submitBooking, addToCart |
| `kavling_provider.dart` | kavlings, selectedKavling | loadKavlings (cached) |
| `peralatan_provider.dart` | peralatanList, selectedCategory | loadPeralatan, setCategory |
| `gallery_provider.dart` | photos | loadPhotos, uploadPhoto |
| `announcement_provider.dart` | announcements | loadAnnouncements (cached) |

---

## Hubungan dengan Kualitas Kode

### Maintainability

1. **Separation of Concerns**: Setiap file memiliki satu tanggung jawab
2. **Modular Structure**: Mudah menemukan dan mengubah kode
3. **Consistent Patterns**: Pola yang sama di seluruh aplikasi

### Scalability

1. **Layered Architecture**: Mudah menambah fitur baru
2. **Repository Pattern**: Bisa mengganti data source tanpa ubah UI
3. **Provider Pattern**: State management yang scalable

### Readability

1. **Clear Naming**: Nama file dan class deskriptif
2. **Organized Folders**: Struktur intuitif
3. **Documentation**: File ini sebagai referensi

---

## Catatan Pengembangan

### Dependency Injection

```dart
// Pattern 1: Singleton (kebanyakan repository)
final authRepository = AuthRepository();

// Pattern 2: Riverpod Provider (AnnouncementRepository)
final announcementRepositoryProvider = Provider<AnnouncementRepository>((ref) {
  return AnnouncementRepository(apiClient);
});
```

### Caching Strategy

- **KavlingProvider**: Cache 5 menit
- **AnnouncementProvider**: Cache 10 menit
- **BookingProvider**: No cache (data dinamis)

### Error Handling

Semua repository menggunakan Result Pattern:
```dart
final result = await repository.doSomething();
if (result.isSuccess) {
  // Handle success
} else {
  // Show result.message
}
```

---

*Dokumentasi ini dibuat untuk membantu pemahaman arsitektur aplikasi LuhurCamp Mobile. Untuk pertanyaan lebih lanjut, silakan hubungi tim pengembang.*
