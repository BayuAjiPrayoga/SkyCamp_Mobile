import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../presentation/screens/auth/login_screen.dart';
import '../../presentation/screens/auth/register_screen.dart';
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

final _rootNavigatorKey = GlobalKey<NavigatorState>();

final routerProvider = Provider<GoRouter>((ref) {
  final authState = ref.watch(authProvider);

  return GoRouter(
    navigatorKey: _rootNavigatorKey,
    initialLocation: '/splash',
    redirect: (context, state) {
      final isLoggedIn = authState.status == AuthStatus.authenticated;
      final isLoggingIn = state.matchedLocation == '/login' || 
                         state.matchedLocation == '/register';
      final isSplash = state.matchedLocation == '/splash';

      if (isSplash) return null;

      if (!isLoggedIn && !isLoggingIn) {
        return '/login';
      }

      if (isLoggedIn && isLoggingIn) {
        return '/home';
      }

      return null;
    },
    routes: [
      // Auth & Splash routes (outside shell)
      GoRoute(
        path: '/splash',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(
        path: '/login',
        builder: (context, state) => const LoginScreen(),
      ),
      GoRoute(
        path: '/register',
        builder: (context, state) => const RegisterScreen(),
      ),
      
      // Main app with bottom navigation
      StatefulShellRoute.indexedStack(
        builder: (context, state, navigationShell) {
          return MainShell(navigationShell: navigationShell);
        },
        branches: [
          // Home Branch
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/home',
                builder: (context, state) => const HomeScreen(),
                routes: [
                  GoRoute(
                    path: 'announcement',
                    builder: (context, state) => const AnnouncementListScreen(),
                  ),
                  GoRoute(
                    path: 'peralatan',
                    builder: (context, state) => const PeralatanListScreen(),
                  ),
                ],
              ),
            ],
          ),
          // Kavling Branch
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/kavling',
                builder: (context, state) => const KavlingListScreen(),
                routes: [
                  GoRoute(
                    path: ':id',
                    pageBuilder: (context, state) {
                      final id = int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
                      return _buildPageWithAnimation(
                        KavlingDetailScreen(kavlingId: id),
                        state,
                      );
                    },
                  ),
                ],
              ),
            ],
          ),
          // Booking Branch
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/my-bookings',
                builder: (context, state) => const MyBookingsScreen(),
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
            ],
          ),
          // Gallery Branch
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/gallery',
                builder: (context, state) => const GalleryScreen(),
              ),
            ],
          ),
          // Profile Branch
          StatefulShellBranch(
            routes: [
              GoRoute(
                path: '/profile',
                builder: (context, state) => const ProfileScreen(),
              ),
            ],
          ),
        ],
      ),
      
      // Standalone routes (full screen, no bottom nav)
      GoRoute(
        path: '/booking/new',
        parentNavigatorKey: _rootNavigatorKey,
        pageBuilder: (context, state) {
          final kavlingId = state.uri.queryParameters['kavling_id'];
          return _buildPageWithAnimation(
            BookingFlowScreen(
              kavlingId: kavlingId != null ? int.tryParse(kavlingId) : null,
            ),
            state,
          );
        },
      ),
      GoRoute(
        path: '/booking/:id',
        parentNavigatorKey: _rootNavigatorKey,
        pageBuilder: (context, state) {
          final id = int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
          return _buildPageWithAnimation(
            BookingDetailScreen(bookingId: id),
            state,
          );
        },
      ),
      GoRoute(
        path: '/announcement',
        parentNavigatorKey: _rootNavigatorKey,
        pageBuilder: (context, state) => _buildPageWithAnimation(
          const AnnouncementListScreen(),
          state,
        ),
      ),
      GoRoute(
        path: '/peralatan',
        parentNavigatorKey: _rootNavigatorKey,
        pageBuilder: (context, state) => _buildPageWithAnimation(
          const PeralatanListScreen(),
          state,
        ),
      ),
    ],
  );
});

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
