// Notification Service - Firebase Cloud Messaging & Local Notifications
// Handle push notifications: permission, token sync, foreground/background messages

import 'dart:io';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:go_router/go_router.dart';
import '../network/api_client.dart';
import '../router/app_router.dart';

// Top-level handler untuk background messages (harus di luar class)
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  // Handle silently - sistem akan tampilkan notification otomatis
}

class NotificationService {
  // Singleton pattern
  static final NotificationService _instance = NotificationService._internal();
  factory NotificationService() => _instance;
  NotificationService._internal();

  final FirebaseMessaging _messaging = FirebaseMessaging.instance;
  final FlutterLocalNotificationsPlugin _localNotifications = FlutterLocalNotificationsPlugin();
  
  String? _fcmToken;
  String? get fcmToken => _fcmToken;

  // Inisialisasi service
  Future<void> initialize() async {
    await _requestPermission();
    await _initializeLocalNotifications();
    await _getFCMToken();
    await sendTokenToBackend(_fcmToken);

    // Listen token refresh
    _messaging.onTokenRefresh.listen((token) async {
      _fcmToken = token;
      await sendTokenToBackend(token);
    });

    // Handle foreground messages
    FirebaseMessaging.onMessage.listen(_handleForegroundMessage);
    
    // Handle notification tap (background)
    FirebaseMessaging.onMessageOpenedApp.listen(_handleNotificationTap);
    
    // Handle notification tap (terminated)
    final initialMessage = await _messaging.getInitialMessage();
    if (initialMessage != null) {
      _handleNotificationTap(initialMessage);
    }
  }

  // Request notification permission
  Future<void> _requestPermission() async {
    await _messaging.requestPermission(
      alert: true,
      badge: true,
      sound: true,
      provisional: false,
    );
  }

  // Setup local notifications untuk foreground display
  Future<void> _initializeLocalNotifications() async {
    const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');
    const iosSettings = DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );

    const initSettings = InitializationSettings(android: androidSettings, iOS: iosSettings);

    await _localNotifications.initialize(initSettings, onDidReceiveNotificationResponse: (_) {});

    // Create Android notification channels
    if (Platform.isAndroid) {
      const channel = AndroidNotificationChannel(
        'luhurcamp_channel',
        'LuhurCamp Notifications',
        description: 'Notifikasi umum',
        importance: Importance.defaultImportance,
      );
      
      const alertChannel = AndroidNotificationChannel(
        'luhurcamp_alert_channel',
        'LuhurCamp Alerts',
        description: 'Notifikasi penting',
        importance: Importance.max,
        sound: RawResourceAndroidNotificationSound('luhur_alert'),
        playSound: true,
      );

      final plugin = _localNotifications.resolvePlatformSpecificImplementation<AndroidFlutterLocalNotificationsPlugin>();
      await plugin?.createNotificationChannel(channel);
      await plugin?.createNotificationChannel(alertChannel);
    }
  }

  // Get FCM token
  Future<String?> _getFCMToken() async {
    try {
      _fcmToken = await _messaging.getToken();
      return _fcmToken;
    } catch (_) {
      return null;
    }
  }

  // Handle foreground message - tampilkan via local notification
  void _handleForegroundMessage(RemoteMessage message) {
    final notification = message.notification;
    if (notification != null) {
      _showLocalNotification(
        title: notification.title ?? 'LuhurCamp',
        body: notification.body ?? '',
        payload: message.data.toString(),
      );
    }
  }

  // Handle notification tap - navigate ke halaman yang sesuai
  void _handleNotificationTap(RemoteMessage message) {
    final data = message.data;
    
    if (data.containsKey('booking_id')) {
      final bookingId = int.tryParse(data['booking_id'].toString());
      if (bookingId != null) {
        rootNavigatorKey.currentContext?.go('/booking/$bookingId');
      }
    } else if (data.containsKey('type') && data['type'] == 'announcement') {
      rootNavigatorKey.currentContext?.go('/announcements');
    }
  }

  // Show local notification
  Future<void> _showLocalNotification({
    required String title,
    required String body,
    String? payload,
  }) async {
    const androidDetails = AndroidNotificationDetails(
      'luhurcamp_alert_channel',
      'LuhurCamp Alerts',
      channelDescription: 'Notifikasi penting',
      importance: Importance.max,
      priority: Priority.high,
      icon: '@mipmap/ic_launcher',
      sound: RawResourceAndroidNotificationSound('luhur_alert'),
      playSound: true,
    );

    const iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
      sound: 'luhur_alert.aiff',
    );

    const details = NotificationDetails(android: androidDetails, iOS: iosDetails);

    await _localNotifications.show(
      DateTime.now().millisecondsSinceEpoch ~/ 1000,
      title,
      body,
      details,
      payload: payload,
    );
  }

  // Subscribe/unsubscribe topics
  Future<void> subscribeToTopic(String topic) => _messaging.subscribeToTopic(topic);
  Future<void> unsubscribeFromTopic(String topic) => _messaging.unsubscribeFromTopic(topic);

  // Sync FCM token ke backend
  Future<bool> sendTokenToBackend(String? token) async {
    if (token == null || token.isEmpty) return false;

    try {
      final hasToken = await apiClient.hasToken();
      if (!hasToken) return false;

      await apiClient.put('/user/fcm-token', data: {'fcm_token': token});
      return true;
    } catch (_) {
      return false;
    }
  }

  // Hapus FCM token dari backend (saat logout)
  Future<void> removeTokenFromBackend() async {
    try {
      await apiClient.delete('/user/fcm-token');
    } catch (_) {}
  }

  // Get fresh token dan sync
  Future<String?> getTokenAndSync() async {
    _fcmToken = await _messaging.getToken();
    if (_fcmToken != null) {
      await sendTokenToBackend(_fcmToken);
    }
    return _fcmToken;
  }
}

// Singleton instance
final notificationService = NotificationService();
