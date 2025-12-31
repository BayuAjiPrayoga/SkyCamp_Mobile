# LuhurCamp Database & API Implementation Plan

Start Date: 2024-01-01
Status: Proposed

## 1. Database Schema (PostgreSQL Compatible)

This schema is designed to support the LuhurCamp admin dashboard and mobile app logic.

### 1.1 Users Table (`users`)
Stores both Admins and End-users (Mobile App Customers).

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `name` | VARCHAR(255) | NOT NULL | Full name |
| `email` | VARCHAR(255) | UNIQUE, NOT NULL | Email address |
| `phone` | VARCHAR(20) | NULL | Phone number |
| `password` | VARCHAR(255) | NOT NULL | Hashed password |
| `role` | VARCHAR(20) | DEFAULT 'customer' | 'admin' or 'customer' |
| `avatar` | VARCHAR(255) | NULL | Path to profile image |
| `email_verified_at` | TIMESTAMP | NULL | Verification timestamp |
| `remember_token` | VARCHAR(100) | NULL | Laravel auth token |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |
| `deleted_at` | TIMESTAMP | NULL | Soft Delete |

### 1.2 Kavlings Table (`kavlings`)
Master data for camping slots.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `nama` | VARCHAR(255) | NOT NULL | e.g. "Kavling A1" |
| `slug` | VARCHAR(255) | UNIQUE, NOT NULL | URL friendly slug |
| `kapasitas` | INTEGER | NOT NULL | Number of people |
| `harga_per_malam` | DECIMAL(10,2) | NOT NULL | Price per night |
| `deskripsi` | TEXT | NULL | Description |
| `gambar` | VARCHAR(255) | NULL | Path to image |
| `status` | VARCHAR(20) | DEFAULT 'aktif' | 'aktif', 'penuh', 'maintenance' |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |
| `deleted_at` | TIMESTAMP | NULL | Soft Delete |

### 1.3 Peralatan Table (`peralatan`)
Master data for rentable equipment.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `nama` | VARCHAR(255) | NOT NULL | e.g. "Tenda Dome 4P" |
| `kategori` | VARCHAR(50) | NOT NULL | 'tenda', 'masak', 'tidur', 'lainnya' |
| `stok_total` | INTEGER | NOT NULL, DEFAULT 0 | Total physical stock |
| `harga_sewa` | DECIMAL(10,2) | NOT NULL | Price per unit/night |
| `deskripsi` | TEXT | NULL | Description |
| `gambar` | VARCHAR(255) | NULL | Path to image |
| `kondisi` | VARCHAR(20) | DEFAULT 'baik' | 'baik', 'perlu_perbaikan', 'rusak' |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |
| `deleted_at` | TIMESTAMP | NULL | Soft Delete |

### 1.4 Bookings Table (`bookings`)
Core transaction table.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `code` | VARCHAR(20) | UNIQUE, NOT NULL | e.g. "BK-240101-001" |
| `user_id` | BIGINT | FK -> users.id | Customer who made booking |
| `kavling_id` | BIGINT | FK -> kavlings.id | Selected kavling (optional if just renting tools?) |
| `tanggal_check_in` | DATE | NOT NULL | Check-in date |
| `tanggal_check_out`| DATE | NOT NULL | Check-out date |
| `total_harga` | DECIMAL(12,2)| NOT NULL | Total cost (kavling + tools) |
| `status` | VARCHAR(20) | NOT NULL | 'pending', 'waiting_verification', 'confirmed', 'rejected', 'cancelled', 'completed' |
| `bukti_pembayaran` | VARCHAR(255) | NULL | Path to payment proof image |
| `rejection_reason` | TEXT | NULL | Reason if rejected |
| `qr_code` | VARCHAR(255) | NULL | JSON/Path for QR Code data |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |

### 1.5 Booking Items Table (`booking_items`)
Pivot table for rented equipment in a booking.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `booking_id` | BIGINT | FK -> bookings.id| |
| `peralatan_id` | BIGINT | FK -> peralatan.id| |
| `jumlah` | INTEGER | NOT NULL | Quantity rented |
| `harga_sewa` | DECIMAL(10,2)| NOT NULL | Snapshot of price at booking time |
| `subtotal` | DECIMAL(10,2)| NOT NULL | jumlah * harga_sewa |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |

### 1.6 Galeri Table (`galleries`)
User uploaded photos for moderation.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `user_id` | BIGINT | FK -> users.id | Uploader |
| `image_path` | VARCHAR(255) | NOT NULL | Path to photo |
| `caption` | TEXT | NULL | Photo caption |
| `status` | VARCHAR(20) | DEFAULT 'pending' | 'pending', 'approved', 'rejected' |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |

### 1.7 Pengumuman Table (`announcements`)
Admin announcements for mobile users.

| Column | Type | Constraints | Description |
|---|---|---|---|
| `id` | BIGSERIAL | PK | Auto-incrementing ID |
| `title` | VARCHAR(255) | NOT NULL | Headline |
| `content` | TEXT | NOT NULL | Body text |
| `type` | VARCHAR(20) | DEFAULT 'info' | 'info', 'warning', 'success' |
| `is_active` | BOOLEAN | DEFAULT TRUE | Visibility toggle |
| `created_at` | TIMESTAMP | NULL | |
| `updated_at` | TIMESTAMP | NULL | |

---

## 2. API Endpoints Plan (Mobile App Integration)

Base URL: `/api/v1`

### 2.1 Authentication
- `POST /register`: Register new customer
- `POST /login`: Login & get Sanctum token
- `POST /logout`: Invalidate token
- `GET /user`: Get current user profile

### 2.2 Master Data (Read Only)
- `GET /kavlings`: List all active kavlings
- `GET /kavlings/{id}`: Detail kavling
- `GET /peralatan`: List all active equipment
- `GET /peralatan/{id}`: Detail equipment

### 2.3 Transactions (Bookings)
- `POST /bookings`: Create new booking
- `GET /bookings`: List my bookings history
- `GET /bookings/{id}`: Detail booking
- `POST /bookings/{id}/upload-payment`: Upload proof of payment (changes status to 'waiting_verification')
- `POST /bookings/{id}/cancel`: User cancel booking (if not yet confirmed)

### 2.4 Gallery
- `GET /galleries`: List approved photos (Public feed)
- `POST /galleries`: Upload photo (Auth required)

### 2.5 General
- `GET /announcements`: Active announcements
- `GET /weather`: Current weather (proxy to OpenWeatherMap or stored data)

---

## 3. Implementation Steps

1.  **Migrations**: Create Laravel migration files for all tables above.
2.  **Models**: Generate Eloquent models with proper relationships.
    - `User` hasMany `Booking`, `Gallery`
    - `Kavling` hasMany `Booking`
    - `Booking` belongsTo `User`, `Kavling`
    - `Booking` hasMany `BookingItem`
    - `BookingItem` belongsTo `Peralatan`
3.  **Seeding**: Create seeders for:
    - Admin User (email: `admin@luhurcamp.com`, password: `password`)
    - Dummy Kavlings (A1 - A5)
    - Dummy Peralatan (Tenda, Kompor, dll)
    - Dummy Bookings & Photos for demo.
4.  **Integration**: Update existing stub Controllers to use these Models.

