# Software Requirement Specification (SRS) - LuhurCamp

## 1. Pendahuluan
### 1.1 Tujuan
Dokumen ini mendeskripsikan kebutuhan perangkat lunak untuk sistem **LuhurCamp**, sebuah,platform reservasi *camping ground* yang terdiri dari aplikasi mobile (untuk pelanggan) dan panel admin berbasis web (untuk pengelola).

### 1.2 Cakupan Produk
Sistem ini bertujuan untuk mendigitalkan proses pemesanan kavling, penyewaan peralatan, dan manajemen operasional di LuhurCamp. Sistem memfasilitasi pengguna untuk melihat ketersediaan, melakukan booking, dan pembayaran, serta membantu admin dalam verifikasi, manajemen stok, dan pelaporan.

## 2. Deskripsi Umum
### 2.1 Perspektif Produk
Sistem terdiri dari dua komponen utama:
1.  **Mobile App (Flutter)**: Antarmuka pelanggan.
2.  **Web Admin (Laravel)**: Antarmuka pengelola (back-office) dan penyedia API.

### 2.2 Karakteristik Pengguna
-   **Pelanggan (Customer)**: Pengguna umum yang ingin memesan tempat camping. Menggunakan aplikasi mobile.
-   **Admin/Pengelola**: Staf LuhurCamp yang mengelola data, booking, dan laporan. Menggunakan web panel.
-   **Owner**: Pemilik bisnis yang memantau laporan pendapatan.

## 3. Kebutuhan Fungsional

### 3.1 Modul Autentikasi
-   **REQ-AUTH-01**: Pengguna dapat mendaftar (Register) dengan nama, email, no HP, dan password.
-   **REQ-AUTH-02**: Pengguna dapat masuk (Login) ke aplikasi.
-   **REQ-AUTH-03**: Pengguna dapat memperbarui profil (avatar, nama, no HP).
-   **REQ-AUTH-04**: Pengguna dapat mengganti password.
-   **REQ-AUTH-05**: Pengguna dapat keluar (Logout).

### 3.2 Modul Booking (Sewa Kavling & Alat)
-   **REQ-BOOK-01**: Pengguna dapat melihat daftar Kavling beserta, harga, kapasitas, dan fasilitas.
-   **REQ-BOOK-02**: Pengguna dapat melihat ketersediaan Kavling berdasarkan tanggal Check-in dan Check-out.
-   **REQ-BOOK-03**: Pengguna dapat melihat daftar Peralatan (tenda, matras, dll) beserta stok *real-time*.
-   **REQ-BOOK-04**: Pengguna dapat membuat pesanan yang terdiri dari Kavling dan (opsional) Peralatan.
-   **REQ-BOOK-05**: Sistem harus mencegah pemesanan ganda (double booking) pada kavling dan tanggal yang sama.

### 3.3 Modul Pembayaran & Riwayat
-   **REQ-PAY-01**: Pengguna dapat melihat riwayat pesanan (Pending, Konfirmasi, Selesai, Batal).
-   **REQ-PAY-02**: Pengguna dapat mengunggah bukti pembayaran (transfer manual).
-   **REQ-PAY-03**: Pengguna dapat membatalkan pesanan yang belum diproses.

### 3.4 Modul Galeri & Informasi
-   **REQ-INFO-01**: Pengguna dapat melihat galeri foto lokasi.
-   **REQ-INFO-02**: Pengguna dapat mengunggah foto mereka sendiri ke galeri publik (dengan moderasi admin).
-   **REQ-INFO-03**: Pengguna dapat melihat pengumuman/informasi terbaru dari pengelola.

### 3.5 Modul Admin (Web)
-   **REQ-ADM-01**: Admin dapat mengelola Data Master (Kavling, Peralatan).
-   **REQ-ADM-02**: Admin dapat memverifikasi bukti pembayaran (Terima/Tolak).
-   **REQ-ADM-03**: Admin dapat melakukan Check-in tamu (scan QR Code atau manual).
-   **REQ-ADM-04**: Admin dapat mengelola Galeri (Setujui/Hapus foto pengguna).
-   **REQ-ADM-05**: Admin dapat membuat dan mengedit Pengumuman.
-   **REQ-ADM-06**: Admin dapat mencetak laporan pendapatan dan okupansi (Harian/Bulanan) ke PDF.

## 4. Kebutuhan Non-Fungsional
-   **NFR-01 (Performance)**: Aplikasi mobile harus memiliki waktu respon < 2 detik untuk transisi antar layar.
-   **NFR-02 (Reliability)**: Sistem harus menjamin akurasi stok dan ketersediaan 100% untuk mencegah *overbooking*.
-   **NFR-03 (Usability)**: Antarmuka mobile harus intuitif dengan desain modern (tema alam).
-   **NFR-04 (Security)**: Password harus di-hash (Bcrypt) dan komunikasi API menggunakan Token (Sanctum).
-   **NFR-05 (Availability)**: Server harus dapat diakses 24/7.
