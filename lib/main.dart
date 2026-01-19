// Entry point aplikasi LuhurCamp Mobile
// Inisialisasi: Firebase, NotificationService, Date Formatting

import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';
import 'core/services/notification_service.dart';

// Handler untuk notifikasi background (harus top-level function)
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  // Handle message silently di background
}

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // Inisialisasi Firebase
  try {
    await Firebase.initializeApp();
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  } catch (e) {
    debugPrint('Firebase init failed: $e');
  }
  
  // Inisialisasi NotificationService
  try {
    await notificationService.initialize();
  } catch (e) {
    debugPrint('Notification Service init failed: $e');
  }
  
  // Subscribe ke topic announcements
  try {
    await notificationService.subscribeToTopic('announcements');
    debugPrint('FCM_TOPIC: Subscribed to announcements');
  } catch (e) {
    debugPrint('FCM_TOPIC: Subscription failed: $e');
  }
  
  // Inisialisasi format tanggal Indonesia
  await initializeDateFormatting('id_ID', null);
  
  runApp(const ProviderScope(child: LuhurCampApp()));
}

// Root widget aplikasi
class LuhurCampApp extends ConsumerWidget {
  const LuhurCampApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final router = ref.watch(routerProvider);

    return MaterialApp.router(
      title: 'LuhurCamp',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      routerConfig: router,
    );
  }
}
