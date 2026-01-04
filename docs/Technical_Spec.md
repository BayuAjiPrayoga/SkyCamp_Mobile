# Technical Specification - LuhurCamp

## 1. Technology Stack

### Backend (Server Side)
-   **Framework**: Laravel 10 (PHP 8.1+)
-   **Database**: MySQL 8.0
-   **Web Server**: Nginx / Apache
-   **API Auth**: Laravel Sanctum (Bearer Token)
-   **Frontend (Admin)**: Blade Templates + Tailwind CSS (via Vite)
-   **PDF Generation**: DomPDF / Barryvdh Laravel-dompdf
-   **QR Code**: SimpleSoftwareIO/simple-qrcode

### Mobile App (Client Side)
-   **Framework**: Flutter (Dart 3.x)
-   **State Management**: Riverpod (v2.x) - Menggunakan `ConsumerStatefulWidget` dan `StateNotifierProvider`.
-   **Networking**: Dio (v5.x) dengan Interceptor untuk header Auth dan Error Handling.
-   **Routing**: GoRouter (v13.x) dengan `StatefulShellRoute` untuk Bottom Navigation bar persisten.
-   **UI Toolkit**: Material Design 3, Google Fonts (Plus Jakarta Sans).
-   **Local Storage**: Flutter Secure Storage (untuk Token), SharedPreferences.
-   **Features**:
    -   `image_picker`: Akses galeri/kamera.
    -   `qr_flutter`: Render QR Code.
    -   `url_launcher`: Membuka link eksternal (WhatsApp).
    -   `flutter_animate`: Animasi UI.

## 2. Development Environment
-   **OS**: Windows
-   **Local Server**: Laragon (PHP, MySQL, Nginx)
-   **IDE**: VS Code
-   **Version Control**: Git

## 3. Konvensi Kode (Code Convention)

### Laravel
-   **PSR-12** standard compliance.
-   Clean Architecture: Controller hanya menghandle HTTP request/response. Logika bisnis kompleks (jika ada) dipisah ke Service/Repository (saat ini masih terpusat di Controller untuk kecepatan pengembangan).
-   Response API seragam (`success`, `message`, `data`).

### Flutter
-   **Feature-First Architecture**: Folder dikelompokkan berdasarkan fitur (`bookings`, `kavlings`, `auth`) di dalam `presentation/screens`.
-   **Repository Pattern**: Data layer (`repositories`) memisahkan logika API dari UI.
-   **MVVM-ish**: `StateNotifier` bertindak sebagai ViewModel yang memegang state UI.
-   **Strict Linting**: Menggunakan `flutter_lints`.

## 4. Keamanan (Security)
-   **Password**: Hashed menggunakan Bcrypt.
-   **Data Transit**: Semua komunikasi API wajib melalui HTTPS (di Production).
-   **Input Validation**: Validasi ketat di sisi server (Laravel Request Validation) sebelum data diproses DB.
-   **Asset Access**: File privat disimpan di `storage/app`, file publik (gambar) di-symlink ke `public/storage`.
