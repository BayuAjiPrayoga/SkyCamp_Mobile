# System Design Document (SDD) - LuhurCamp

## 1. Arsitektur Sistem
LuhurCamp menggunakan arsitektur **Client-Server** dengan pola REST API.

```mermaid
graph TD
    Client[Mobile App (Flutter)] -->|REST API (JSON)| Server[Web Server (Laravel)]
    Admin[Web Browser] -->|HTTP Request| Server
    Server -->|Read/Write| DB[(Database MySQL)]
    Server -->|Store Files| Storage[File Storage (Local/S3)]
```

### 1.1 Komponen Utama
1.  **Mobile Client (Frontend Mobile)**:
    *   Dibangun dengan **Flutter**.
    *   State Management: **Riverpod**.
    *   Routing: **GoRouter**.
    *   HTTP Client: **Dio**.
2.  **Web Admin (Backend + Frontend Web)**:
    *   Dibangun dengan **Laravel 10** (PHP).
    *   Template Engine: **Blade** + **Tailwind CSS**.
    *   API Authentication: **Laravel Sanctum**.
3.  **Database**:
    *   **MySQL** (Relational Database).

## 2. Desain Database (ERD Skematik)

### Tabel Utama
-   `users`: Menyimpan data pengguna (pelanggan & admin).
    -   `id`, `name`, `email`, `password`, `role` (admin/customer), `avatar`.
-   `kavlings`: Data master kavling.
    -   `id`, `nama`, `kapasitas`, `harga_per_malam`, `deskripsi`, `gambar`, `status`.
-   `peralatans`: Data master peralatan sewa.
    -   `id`, `nama`, `stok` (total), `stok_tersedia` (kalkulasi), `harga`, `gambar`.
-   `bookings`: Transaksi utama.
    -   `id`, `user_id`, `kavling_id`, `tanggal_check_in`, `tanggal_check_out`, `total_harga`, `status` (pending/confirmed/cancelled/checked_in/completed), `bukti_bayar`.
-   `booking_peralatan` (Pivot): Detail sewa alat per booking.
    -   `booking_id`, `peralatan_id`, `jumlah`, `harga_satuan`.
-   `galleries`: Foto-foto lokasi.
    -   `id`, `user_id` (uploader), `image`, `caption`, `status` (pending/approved).
-   `announcements`: Pengumuman.
    -   `id`, `judul`, `konten`, `is_active`.

## 3. Desain API (Interface)
Semua respon API mengikuti standar format JSON berikut:

```json
{
  "success": true,
  "message": "Optional message",
  "data": { ... }
}
```

### Endpoint Penting
-   `POST /api/v1/login`: Autentikasi & Token.
-   `GET /api/v1/kavlings`: List kavling dengan filter tanggal (ketersediaan).
-   `POST /api/v1/bookings`: Submit booking baru.
-   `GET /api/v1/bookings`: Riwayat booking user.
-   `POST /api/v1/user/avatar`: Upload foto profil (Multipart).

## 4. Alur Proses Utama

### 4.1 Booking Flow
1.  User memilih Kavling & Tanggal -> Sistem cek ketersediaan.
2.  User memilih Peralatan (Opsional) -> Sistem cek stok.
3.  User konfirmasi & checkout -> Booking status `pending`.
4.  User transfer & upload bukti -> Booking status `waiting_confirmation`.
5.  Admin verifikasi -> Booking status `confirmed`.
6.  User datang -> Admin scan QR -> Booking status `checked_in`.
7.  Selesai -> Booking status `completed`.

### 4.2 Check-in Logic
Menggunakan **QR Code**. Setiap booking yang `confirmed` memiliki QR Code unik (berisi Booking ID). Admin men-scan QR ini via Web Webcam untuk mengubah status menjadi `checked_in`.
