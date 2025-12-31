# API Reference - LuhurCamp Admin

## Routes Overview

### Admin Routes (Prefix: `/admin`)

| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/dashboard` | DashboardController@index | Dashboard |
| GET | `/kavling` | KavlingController@index | List kavling |
| POST | `/kavling` | KavlingController@store | Create kavling |
| PUT | `/kavling/{id}` | KavlingController@update | Update kavling |
| DELETE | `/kavling/{id}` | KavlingController@destroy | Delete kavling |
| GET | `/peralatan` | PeralatanController@index | List peralatan |
| POST | `/peralatan` | PeralatanController@store | Create peralatan |
| PUT | `/peralatan/{id}` | PeralatanController@update | Update peralatan |
| DELETE | `/peralatan/{id}` | PeralatanController@destroy | Delete peralatan |
| GET | `/booking` | BookingController@index | List booking |
| GET | `/booking/export` | BookingController@export | Export Excel |
| GET | `/verifikasi` | VerifikasiController@index | Pending payments |
| POST | `/verifikasi/{id}/confirm` | VerifikasiController@confirm | Confirm payment |
| POST | `/verifikasi/{id}/reject` | VerifikasiController@reject | Reject |
| GET | `/galeri` | GaleriController@index | List photos |
| POST | `/galeri/{id}/approve` | GaleriController@approve | Approve photo |
| POST | `/galeri/{id}/reject` | GaleriController@reject | Reject photo |
| GET | `/laporan` | LaporanController@index | Reports page |
| GET | `/laporan/pdf` | LaporanController@exportPdf | Export PDF |
| GET | `/laporan/excel` | LaporanController@exportExcel | Export Excel |

---

## Query Parameters

### Booking List
```
GET /admin/booking?status=pending&search=john&date_from=2025-01-01&date_to=2025-01-31
```

| Param | Type | Description |
|-------|------|-------------|
| status | string | Filter: pending, confirmed, cancelled |
| search | string | Search by code or user name |
| date_from | date | Start date filter |
| date_to | date | End date filter |

### Laporan
```
GET /admin/laporan?month=12&year=2025
GET /admin/laporan/pdf?month=12&year=2025
```

| Param | Type | Description |
|-------|------|-------------|
| month | int | 1-12 |
| year | int | 2020-2030 |

### Galeri
```
GET /admin/galeri?status=pending
```

| Param | Type | Description |
|-------|------|-------------|
| status | string | pending, approved, rejected |

---

## Response Examples

### Dashboard Data
```json
{
  "todayBookings": 5,
  "monthlyRevenue": 2235000,
  "availableKavling": 4,
  "totalKavling": 8,
  "availableGear": 111,
  "totalGear": 123,
  "pendingBookings": [...],
  "pendingCount": 3,
  "weather": {
    "temp": 21,
    "description": "Cerah Berawan",
    "humidity": 65,
    "wind": 12
  }
}
```

### Weekly Revenue
```json
[
  {"week": 1, "start": "01", "end": "07", "revenue": 500000, "bookings": 2},
  {"week": 2, "start": "08", "end": "14", "revenue": 750000, "bookings": 3},
  {"week": 3, "start": "15", "end": "21", "revenue": 0, "bookings": 0},
  {"week": 4, "start": "22", "end": "31", "revenue": 985000, "bookings": 4}
]
```

---

## Models

### Kavling
```php
[
  'id' => int,
  'nama' => string,
  'kapasitas' => int,
  'harga_per_malam' => int,
  'fasilitas' => json,
  'status' => 'tersedia'|'terisi'|'maintenance',
  'gambar' => string|null,
]
```

### Peralatan
```php
[
  'id' => int,
  'nama' => string,
  'kategori' => string,
  'harga_sewa' => int,
  'stok_total' => int,
  'kondisi' => 'baik'|'perlu_perbaikan'|'rusak',
  'gambar' => string|null,
]
```

### Booking
```php
[
  'id' => int,
  'code' => string, // BK-XXXXXX
  'user_id' => int,
  'kavling_id' => int,
  'tanggal_check_in' => date,
  'tanggal_check_out' => date,
  'total_harga' => int,
  'status' => 'pending'|'confirmed'|'cancelled'|'completed',
  'bukti_pembayaran' => string|null,
]
```

---

## Services

### DashboardService
- `getDashboardData()` - Get all dashboard stats
- `getPendingBookingsPreview()` - Get 5 pending bookings
- `getWeatherData()` - Get weather from API

### GalleryService
- `getPendingPhotos()` - Photos awaiting moderation
- `getApprovedPhotos()` - Approved photos
- `approvePhoto(id)` - Approve a photo
- `rejectPhoto(id)` - Reject a photo
- `bulkApprove(ids)` - Bulk approve
- `bulkReject(ids)` - Bulk reject

### WeatherService
- `getCurrentWeather()` - Get weather from OpenWeatherMap
- Coordinates: Gunung Luhur (-6.7318, 106.4572)
- Cache: 30 minutes
