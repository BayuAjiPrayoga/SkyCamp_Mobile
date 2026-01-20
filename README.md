# 📱 LuhurCamp Mobile App Documentation

Dokumentasi lengkap untuk aplikasi mobile LuhurCamp - Camping Ground Booking App.

## 📋 Table of Contents

1. [Overview](#-overview)
2. [Demo](#-demo)
3. [Screenshots](#-screenshots)
4. [Tech Stack](#-tech-stack)
5. [Project Structure](#-project-structure)
6. [Features](#-features)
7. [Setup & Installation](#-setup--installation)
8. [Repository Terkait](#-repository-terkait)

---

## 🎯 Overview

LuhurCamp adalah aplikasi mobile untuk reservasi camping ground yang terintegrasi dengan backend Laravel. Aplikasi ini memungkinkan pengguna untuk:

- Melihat daftar kavling (camping spot) yang tersedia
- Melakukan booking kavling dengan pilihan peralatan
- Melihat status booking dan upload bukti pembayaran
- Menerima notifikasi push real-time
- Melihat galeri foto dan pengumuman

---

## 🎬 Demo

Demo video aplikasi LuhurCamp Mobile tersedia di folder `docs/img asset/demo.gif` (file lokal - tidak di-upload ke GitHub karena ukuran > 100MB).

---

## 📸 Screenshots

Berikut adalah tampilan aplikasi LuhurCamp Mobile:

### Auth Screens

<table>
  <tr>
    <td align="center"><b>Login</b></td>
    <td align="center"><b>Register</b></td>
    <td align="center"><b>Loading</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/Loginpage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Registerpage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Loadingpage.png" width="200"/></td>
  </tr>
</table>

### Main Screens

<table>
  <tr>
    <td align="center"><b>Home</b></td>
    <td align="center"><b>Kavling</b></td>
    <td align="center"><b>Booking List</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/Homepage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Kavlingpage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Bookingpage.png" width="200"/></td>
  </tr>
</table>

### Booking Detail & QR Ticket

<table>
  <tr>
    <td align="center"><b>Booking Detail</b></td>
    <td align="center"><b>Tiket QR</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/detailbooking.png" width="200"/></td>
    <td><img src="docs/img%20asset/tiketqr.png" width="200"/></td>
  </tr>
</table>

### Features Screens

<table>
  <tr>
    <td align="center"><b>Peralatan</b></td>
    <td align="center"><b>Gallery</b></td>
    <td align="center"><b>Pengumuman</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/Peralatanpage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Galerypage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Pengumumanpage.png" width="200"/></td>
  </tr>
</table>

### Profile & Notifications

<table>
  <tr>
    <td align="center"><b>Profile</b></td>
    <td align="center"><b>Notifikasi Real-time</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/Profilpage.png" width="200"/></td>
    <td><img src="docs/img%20asset/Notifikasi-real-time.png" width="200"/></td>
  </tr>
</table>

### Check-in & Check-out (Smart Scan)

<table>
  <tr>
    <td align="center"><b>Check-in Popup</b></td>
    <td align="center"><b>Check-out Popup</b></td>
  </tr>
  <tr>
    <td><img src="docs/img%20asset/Cekinpopup.png" width="200"/></td>
    <td><img src="docs/img%20asset/Cekoutpopup.png" width="200"/></td>
  </tr>
</table>

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
├── main.dart                         # Entry point aplikasi
│
├── core/                             # Layer fondasi
│   ├── config/
│   │   └── api_config.dart           # Base URL & endpoint API
│   ├── network/
│   │   └── api_client.dart           # HTTP client Dio + auth token
│   ├── router/
│   │   └── app_router.dart           # Navigasi GoRouter
│   ├── services/
│   │   └── notification_service.dart # Push notification (FCM)
│   ├── storage/
│   │   └── secure_storage.dart       # Simpan token aman
│   └── theme/
│       └── app_theme.dart            # Warna, font, styling
│
├── data/                             # Layer data
│   ├── models/
│   │   ├── user_model.dart           # Data user
│   │   ├── booking_model.dart        # Data pemesanan
│   │   ├── kavling_model.dart        # Data slot camping
│   │   ├── peralatan_model.dart      # Data peralatan sewa
│   │   ├── gallery_model.dart        # Data foto galeri
│   │   └── announcement_model.dart   # Data pengumuman
│   │
│   └── repositories/                 # Akses API
│       ├── auth_repository.dart
│       ├── booking_repository.dart
│       ├── kavling_repository.dart
│       ├── peralatan_repository.dart
│       ├── gallery_repository.dart
│       └── announcement_repository.dart
│
└── presentation/                     # Layer UI
    ├── providers/                    # State management (Riverpod)
    ├── screens/                      # Halaman UI
    └── widgets/                      # Komponen reusable
```

Untuk dokumentasi arsitektur lengkap, lihat [Penjelasan.md](Penjelasan.md).

---

## ✨ Features

### 1. Authentication
- **Email/Password Login** - Login tradisional
- **Google Sign-In** - OAuth via Firebase Auth
- **Auto Login** - Token persistence dengan secure storage
- **Profile Management** - Update nama, telepon, avatar

### 2. Kavling Management
- Browse camping spots yang tersedia
- Lihat detail kavling (kapasitas, fasilitas, harga)
- Status ketersediaan real-time

### 3. Booking System
- Multi-step booking flow
- Pilih tanggal check-in/check-out
- Sewa peralatan camping (opsional)
- QR Code untuk check-in/check-out
- Tracking status booking

### 4. Push Notifications
- Notifikasi real-time untuk update booking
- Pengumuman dari admin
- Topic subscription

---

## 🚀 Setup & Installation

```bash
# Clone repository
git clone https://github.com/BayuAjiPrayoga/SkyCamp_Mobile.git

# Masuk ke direktori
cd SkyCamp_Mobile

# Install dependencies
flutter pub get

# Run aplikasi
flutter run
```

---

## 📂 Repository Terkait

| Repository | Deskripsi |
| :--------- | :-------- |
| [LuhurCamp-Web-App](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App) | Backend Laravel + Web Admin Panel |
| [SkyCamp_Mobile](https://github.com/BayuAjiPrayoga/SkyCamp_Mobile) | Aplikasi Mobile Flutter |

---

## 📚 Dokumentasi Teknis

Dokumen teknis detail tersedia di repository utama:

- **[SRS (Software Requirement Specification)](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SRS.md)**
- **[SDD (System Design Document)](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SDD.md)**
- **[Technical Specification](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Technical_Spec.md)**
- **[Business Logic](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Business_Logic.md)**

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

- Email: support@luhurcamp.com
- Developer: Bayu Aji Prayoga
