# 📱 LuhurCamp Mobile App Documentation

Dokumentasi lengkap untuk aplikasi mobile LuhurCamp - Camping Ground Booking App.
##  Dokumentasi Teknis

Dokumen teknis detail tersedia di repository utama:

-   **[SRS (Software Requirement Specification)](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SRS.md)**: Detail kebutuhan fungsional sistem.
-   **[SDD (System Design Document)](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/SDD.md)**: Arsitektur sistem, ERD, dan Topologi.
-   **[Technical Specification](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Technical_Spec.md)**: Stack teknologi dan standar kode.
-   **[Business Logic](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Business_Logic.md)**: Alur bisnis, flowchart, dan logika sistem.
-   **[Project Plan](https://github.com/BayuAjiPrayoga/LuhurCamp-Web-App/blob/main/docs/Project_Plan.md)**: Timeline dan roadmap pengembangan.

---

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


