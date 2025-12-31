# LuhurCamp Admin Panel Documentation

## 📋 Deskripsi

**LuhurCamp** adalah sistem manajemen camping ground berbasis web untuk mengelola booking, kavling, peralatan, galeri foto, dan laporan keuangan di Bumi Perkemahan Gunung Luhur, Lebak, Banten.

**Tech Stack:**
- Laravel 11 (PHP 8.3)
- PostgreSQL
- Blade + Tailwind CSS
- Alpine.js
- Vite

---

## 🚀 Instalasi

```bash
# Clone & install
git clone [repository]
cd LuhurCamp
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Storage
php artisan storage:link

# Run
php artisan serve
npm run dev
```

**URL:** http://127.0.0.1:8000/admin

---

## 📁 Struktur Modul

### 1. Dashboard
- **Route:** `/admin/dashboard`
- **Controller:** `DashboardController`
- **Service:** `DashboardService`
- **Fitur:**
  - Statistik booking hari ini (real-time)
  - Pendapatan bulan ini (dari confirmed bookings)
  - Kavling tersedia
  - Stok peralatan tersedia
  - Grafik booking mingguan
  - Widget cuaca (OpenWeatherMap API)
  - Pending verifikasi pembayaran

### 2. Master Data

#### 2.1 Kavling
- **Route:** `/admin/kavling`
- **Controller:** `KavlingController`
- **Model:** `Kavling`
- **Fitur:** CRUD kavling camping

#### 2.2 Peralatan
- **Route:** `/admin/peralatan`
- **Controller:** `PeralatanController`
- **Model:** `Peralatan`
- **Fitur:** CRUD peralatan camping, manajemen stok

#### 2.3 Pengumuman
- **Route:** `/admin/pengumuman`
- **Controller:** `PengumumanController`
- **Model:** `Pengumuman`
- **Fitur:** CRUD pengumuman untuk customer

### 3. Transaksi

#### 3.1 Booking
- **Route:** `/admin/booking`
- **Controller:** `BookingController`
- **Model:** `Booking`
- **Fitur:**
  - Daftar semua booking
  - Filter: status, search, date range
  - Export Excel

#### 3.2 Verifikasi Pembayaran
- **Route:** `/admin/verifikasi`
- **Controller:** `VerifikasiController`
- **Fitur:**
  - Verifikasi bukti transfer
  - Konfirmasi pembayaran
  - Generate QR Code
  - Tolak pembayaran

### 4. Galeri
- **Route:** `/admin/galeri`
- **Controller:** `GaleriController`
- **Service:** `GalleryService`
- **Fitur:**
  - Moderasi foto dari customer
  - Approve/Reject foto
  - Bulk action
  - Filter: pending, approved, rejected

### 5. Laporan
- **Route:** `/admin/laporan`
- **Controller:** `LaporanController`
- **Fitur:**
  - Laporan pendapatan per minggu
  - Export PDF (DomPDF)
  - Rekapitulasi inventaris
  - Export Excel (Maatwebsite)
  - Filter bulan/tahun

---

## 🗄️ Database Schema

### Tables

| Table | Deskripsi |
|-------|-----------|
| `users` | Data user (admin & customer) |
| `kavlings` | Data kavling camping |
| `peralatans` | Data peralatan rental |
| `bookings` | Data booking |
| `booking_items` | Detail peralatan per booking |
| `gallery_photos` | Foto dari customer |
| `pengumumen` | Pengumuman |

### Status Booking
- `pending` - Menunggu pembayaran
- `confirmed` - Pembayaran dikonfirmasi
- `cancelled` - Dibatalkan
- `completed` - Selesai

### Kondisi Peralatan
- `baik` - Tersedia
- `perlu_perbaikan` - Butuh maintenance
- `rusak` - Tidak tersedia

---

## 🔌 API & Services

### WeatherService
```php
// Config: config/services.php
'openweather' => [
    'key' => env('OPENWEATHER_API_KEY'),
],

// Lokasi: Gunung Luhur (-6.7318, 106.4572)
// Cache: 30 menit
```

### Export Services
- `BookingExport` - Export booking ke Excel
- `PeralatanExport` - Export inventaris ke Excel
- PDF menggunakan `barryvdh/laravel-dompdf`

---

## 🎨 UI Components

Komponen UI tersedia di `resources/views/components/ui/`:

| Component | Penggunaan |
|-----------|------------|
| `card` | Container dengan shadow |
| `button` | Tombol dengan variants |
| `badge` | Label status |
| `modal` | Dialog popup |
| `stat-card` | Card statistik dashboard |
| `select` | Dropdown select |
| `input` | Form input |
| `table` | Tabel data |

---

## 🔐 Environment Variables

```env
# Database
DB_CONNECTION=pgsql
DB_DATABASE=luhurcamp
DB_USERNAME=postgres
DB_PASSWORD=xxx

# Weather API (optional)
OPENWEATHER_API_KEY=your_api_key

# App
APP_URL=http://localhost
```

---

## 📊 Arsitektur

```
app/
├── Http/Controllers/Admin/    # Admin controllers
├── Models/                    # Eloquent models
├── Repositories/              # Repository pattern
│   ├── Contracts/             # Interfaces
│   └── Eloquent/              # Implementations
├── Services/                  # Business logic
└── Exports/                   # Excel exports

resources/views/
├── admin/                     # Admin views
├── components/ui/             # Reusable components
└── layouts/                   # Layout templates
```

---

## ✅ Status Production Ready

| Module | Status | Notes |
|--------|--------|-------|
| Dashboard | ✅ | Real data, weather API |
| Kavling | ✅ | CRUD complete |
| Peralatan | ✅ | CRUD + stock management |
| Pengumuman | ✅ | CRUD complete |
| Booking | ✅ | List, filter, export |
| Verifikasi | ✅ | Confirm/reject payments |
| Galeri | ✅ | Moderation system |
| Laporan | ✅ | PDF & Excel export |

---

## 🗺️ Lokasi

**Bumi Perkemahan Gunung Luhur**
- **Maps:** https://maps.app.goo.gl/kxvHhMJSeciYF26y9
- **Koordinat:** -6.7318, 106.4572
- **Wilayah:** Lebak, Banten, Indonesia

---

## 📝 Changelog

### v1.0.0 (31 Desember 2025)
- ✅ Initial release
- ✅ Demo data removed
- ✅ All modules production ready
- ✅ Real database integration
- ✅ Weather API integration
- ✅ Export functionality (PDF/Excel)

---

## 🔮 Roadmap

### Coming Soon
- [ ] Mobile App (Flutter)
- [ ] Data Pengunjung export
- [ ] Statistik Bulanan
- [ ] Log Aktivitas
- [ ] Push Notifications

---

*Last updated: 31 Desember 2025*
