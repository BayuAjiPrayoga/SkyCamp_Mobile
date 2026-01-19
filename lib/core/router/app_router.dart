// App Router - Konfigurasi navigasi dengan GoRouter
// Auto-redirect berdasarkan auth state, bottom navigation dengan StatefulShellRoute

import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../presentation/screens/auth/login_screen.dart';
import '../../presentation/screens/auth/register_screen.dart';
import '../../presentation/screens/auth/forgot_password_screen.dart';
import '../../presentation/screens/home/home_screen.dart';
import '../../presentation/screens/splash_screen.dart';
import '../../presentation/screens/kavling/kavling_list_screen.dart';
import '../../presentation/screens/kavling/kavling_detail_screen.dart';
import '../../presentation/screens/peralatan/peralatan_list_screen.dart';
import '../../presentation/screens/booking/booking_flow_screen.dart';
import '../../presentation/screens/booking/my_bookings_screen.dart';
import '../../presentation/screens/booking/booking_detail_screen.dart';
import '../../presentation/screens/profile/profile_screen.dart';
import '../../presentation/screens/gallery/gallery_screen.dart';
import '../../presentation/screens/announcement/announcement_list_screen.dart';
import '../../presentation/providers/auth_provider.dart';
import '../../presentation/widgets/main_shell.dart';

// Global key untuk navigasi dari luar widget tree (contoh: notification tap)
final rootNavigatorKey = GlobalKey<NavigatorState>();

// Notifier untuk trigger router refresh saat auth state berubah
class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Ref ref) {
    ref.listen(authProvider, (prev, next) {
      if (prev?.status != next.status) {
        notifyListeners();
      }
    });
  }
}

final routerRefreshProvider = Provider((ref) => RouterRefreshNotifier(ref));

// Router provider utama
final routerProvider = Provider<GoRouter>((ref) {
  final refreshNotifier = ref.watch(routerRefreshProvider);

  return GoRouter(
    navigatorKey: rootNavigatorKey,
    initialLocation: '/splash',
    refreshListenable: refreshNotifier,
    
    // Redirect logic berdasarkan auth state
    redirect: (context, state) {
      final authState = ref.read(authProvider);
      final isLoggedIn = authState.status == AuthStatus.authenticated;
      final isAuthRoute = state.matchedLocation == '/login' ||
          state.matchedLocation == '/register' ||
          state.matchedLocation == '/forgot-password';
      final isSplash = state.matchedLocation == '/splash';

      if (isSplash) return null;
      if (!isLoggedIn && !isAuthRoute) return '/login';
      if (isLoggedIn && isAuthRoute) return '/home';
      return null;
    },
    
    routes: [
      // Auth & Splash (tanpa bottom nav)
      GoRoute(path: '/splash', builder: (_, __) => const SplashScreen()),
      GoRoute(path: '/login', builder: (_, __) => const LoginScreen()),
      GoRoute(path: '/register', builder: (_, __) => const RegisterScreen()),
      GoRoute(path: '/forgot-password', builder: (_, __) => const ForgotPasswordScreen()),

      // Main app dengan bottom navigation
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) => MainShell(navigationShell: navigationShell),
        branches: [
          // Tab 1: Home
          StatefulShellBranch(routes: [
            GoRoute(
              path: '/home',
              builder: (_, __) => const HomeScreen(),
              routes: [
                GoRoute(path: 'announcement', builder: (_, __) => const AnnouncementListScreen()),
                GoRoute(path: 'peralatan', builder: (_, __) => const PeralatanListScreen()),
              ],
            ),
          ]),
          
          // Tab 2: Kavling
          StatefulShellBranch(routes: [
            GoRoute(
              path: '/kavling',
              builder: (_, __) => const KavlingListScreen(),
              routes: [
                GoRoute(
                  path: ':id',
                  pageBuilder: (context, state) {
                    final id = int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
                    return _buildPageWithAnimation(KavlingDetailScreen(kavlingId: id), state);
                  },
                ),
              ],
            ),
          ]),
          
          // Tab 3: My Bookings
          StatefulShellBranch(routes: [
            GoRoute(
              path: '/my-bookings',
              builder: (_, __) => const MyBookingsScreen(),
              routes: [
                GoRoute(
                  path: ':id',
                  builder: (context, state) {
                    final id = int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
                    return BookingDetailScreen(bookingId: id);
                  },
                ),
              ],
            ),
          ]),
          
          // Tab 4: Gallery
          StatefulShellBranch(routes: [
            GoRoute(path: '/gallery', builder: (_, __) => const GalleryScreen()),
          ]),
          
          // Tab 5: Profile
          StatefulShellBranch(routes: [
            GoRoute(path: '/profile', builder: (_, __) => const ProfileScreen()),
          ]),
        ],
      ),

      // Standalone routes (full screen, tanpa bottom nav)
      GoRoute(
        path: '/booking/new',
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) {
          final kavlingId = state.uri.queryParameters['kavling_id'];
          return _buildPageWithAnimation(
            BookingFlowScreen(kavlingId: kavlingId != null ? int.tryParse(kavlingId) : null),
            state,
          );
        },
      ),
      GoRoute(
        path: '/booking/:id',
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) {
          final id = int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
          return _buildPageWithAnimation(BookingDetailScreen(bookingId: id), state);
        },
      ),
      GoRoute(
        path: '/announcement',
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) => _buildPageWithAnimation(const AnnouncementListScreen(), state),
      ),
      GoRoute(
        path: '/peralatan',
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) => _buildPageWithAnimation(const PeralatanListScreen(), state),
      ),
    ],
  );
});

// Helper: Custom fade transition
Page<dynamic> _buildPageWithAnimation(Widget child, GoRouterState state) {
  return CustomTransitionPage(
    key: state.pageKey,
    child: child,
    transitionsBuilder: (context, animation, secondaryAnimation, child) {
      return FadeTransition(
        opacity: CurveTween(curve: Curves.easeInOut).animate(animation),
        child: child,
      );
    },
    transitionDuration: const Duration(milliseconds: 250),
  );
}
