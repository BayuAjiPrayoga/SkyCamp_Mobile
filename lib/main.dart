import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';
import 'core/services/notification_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  // 1. Initialize Firebase first (Critical)
  try {
    await Firebase.initializeApp();
    // Background handler must be registered right after initialization
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  } catch (e) {
    debugPrint('Firebase init failed: $e');
  }

  // 2. Initialize Notification Service
  try {
    await notificationService.initialize();
  } catch (e) {
    debugPrint('Notification Service init failed: $e');
  }

  // 3. Subscribe to Announcements (Independent)
  try {
    await notificationService.subscribeToTopic('announcements');
    debugPrint('FCM_TOPIC: Subscribed to announcements');
  } catch (e) {
    debugPrint('FCM_TOPIC: Subscription failed: $e');
  }

  await initializeDateFormatting('id_ID', null);
  runApp(const ProviderScope(child: LuhurCampApp()));
}

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
