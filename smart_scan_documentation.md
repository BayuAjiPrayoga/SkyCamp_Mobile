# Smart Scan & Inventory Logic Documentation

Berikut adalah dokumentasi lengkap mengenai perubahan yang dilakukan di Backend untuk mendukung fitur **Smart Scan (Check-In/Out)** dan **Manajemen Stok Otomatis**.

## 1. Perubahan Database (Schema)
Kami menambahkan kolom baru pada tabel `peralatan` untuk melacak stok yang tersedia secara real-time.

### Tabel: `peralatan`
| Kolom Baru | Tipe Data | Deskripsi |
| :--- | :--- | :--- |
| `stok_tersedia` | `INTEGER` | Jumlah stok yang fisik ada di gudang saat ini. Berkurang saat Check-In, bertambah saat Check-Out. |

> **Catatan SQL Manual (Supabase):**
> Karena kendala koneksi migrasi, kolom ini ditambahkan secara manual via Dashboard SQL Editor:
> ```sql
> ALTER TABLE peralatan ADD COLUMN stok_tersedia INTEGER;
> UPDATE peralatan SET stok_tersedia = stok_total;
> ALTER TABLE peralatan ALTER COLUMN stok_tersedia SET NOT NULL;
> ```

---

## 2. Logika Backend (`BookingController`)
Endpoint Scan QR Code telah di-upgrade menjadi "Smart Action".

**File:** `app/Http/Controllers/Admin/BookingController.php`
**Method:** `scanAction(Request $request)`

### Alur Logika:
1.  **Terima Input**: Membaca `code` booking dari QR Code.
2.  **Cek Status Booking**:
    *   **Jika `confirmed` atau `paid`**:
        *   Sistem melakukan **Check-In**.
        *   **Stok Berkurang**: `stok_tersedia` dikurangi sesuai jumlah sewa.
        *   Status berubah jadi `checked_in`.
        *   *Response JSON*: `{ action: 'check_in', status: 'success' }`
    *   **Jika `checked_in`**:
        *   Sistem melakukan **Check-Out**.
        *   **Stok Kembali**: `stok_tersedia` ditambah kembali.
        *   Status berubah jadi `completed`.
        *   *Response JSON*: `{ action: 'check_out', status: 'success' }`
    *   **Lainnya**: Return error (misal sudah completed).

---

## 3. API Routes
Route untuk scan telah diperbarui namanya agar lebih relevan.

**File:** `routes/web.php`
```php
// Sebelumnya: booking.scan-check-in
// Sekarang:
Route::post('/booking/scan', [BookingController::class, 'scanAction'])->name('booking.scan-action');
```

---

## 4. Status Mobile App (`LuhurCamp-Mobile`)
Berdasarkan analisis kode `LuhurCamp-Mobile` saat ini:

*   **QR Code**: Sudah tersedia di `BookingDetailScreen`.
    *   Path: `lib/presentation/screens/booking/booking_detail_screen.dart`
    *   Logic: QR Code muncul jika status booking `confirmed`, `checked_in`, atau `completed`.
*   **Koneksi API**:
    *   File: `lib/core/config/api_config.dart`
    *   Saat ini mengarah ke: **Production (Railway)**.
    *   *Saran*: Jika ingin testing fitur baru ini, ganti URL API ke **Local IP Laptop** (misal: `http://192.168.1.11:8000/api/v1`) sampai fitur ini di-deploy ke Production.

---

## 5. Ringkasan untuk Developer Mobile
Jika Anda ingin melakukan perubahan di Mobile App:
1.  Fitur QR Code **sudah ada**, jadi tidak perlu buat ulang.
2.  Pastikan **Base URL** mengarah ke server yang memiliki fitur Smart Scan (saat ini masih di Localhost saya/Anda, belum di Production Railway).
3.  Test flow: Scan QR di HP -> Cek Stok di Database/Admin -> Scan lagi untuk checkout.
