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

/// Global navigator key for navigation from outside widget tree (e.g. notifications)
final rootNavigatorKey = GlobalKey<NavigatorState>();

// Notifier untuk refresh router
class RouterRefreshNotifier extends ChangeNotifier {
  RouterRefreshNotifier(Ref ref) {
    ref.listen(authProvider, (previousState, newState) {
      // print('ROUTER DEBUG: Auth State Changed: ${previousState?.status} -> ${newState.status}');
      // Only refresh router if the STATUS changes
      if (previousState?.status != newState.status) {
        // print('ROUTER DEBUG: Status changed, notifying listeners!');
        notifyListeners();
      } else {
        // print('ROUTER DEBUG: Status unchanged, SKIPPING refresh.');
      }
    });
  }
}

final routerRefreshProvider = Provider((ref) => RouterRefreshNotifier(ref));

final routerProvider = Provider<GoRouter>((ref) {
  // DON'T watch authProvider here, otherwise the router gets recreated on every error message change!
  // final authState = ref.watch(authProvider); 
  final refreshNotifier = ref.watch(routerRefreshProvider);

  return GoRouter(
    navigatorKey: rootNavigatorKey,
    initialLocation: '/splash',
    refreshListenable: refreshNotifier,
    redirect: (context, state) {
      final authState = ref.read(authProvider); // Read latest state dynamically
      final isLoggedIn = authState.status == AuthStatus.authenticated;
      final isAuthRoute =
          state.matchedLocation == '/login' ||
          state.matchedLocation == '/register' ||
          state.matchedLocation == '/forgot-password';
      final isSplash = state.matchedLocation == '/splash';

      // print('ROUTER DEBUG: Redirect Check inside ${state.matchedLocation}');
      // print('ROUTER DEBUG: AuthStatus: ${authState.status}, isLoggedIn: $isLoggedIn');

      if (isSplash) return null;

      if (!isLoggedIn && !isAuthRoute) {
        // print('ROUTER DEBUG: Not logged in, redirecting to /login');
        return '/login';
      }

      if (isLoggedIn && isAuthRoute) {
        // print('ROUTER DEBUG: Logged in, redirecting to /home');
        return '/home';
      }

      // print('ROUTER DEBUG: No redirect needed.');
      return null;
    },
    routes: [
      // Auth & Splash routes (outside shell)
      GoRoute(
        path: '/splash',
        builder: (context, state) => const SplashScreen(),
      ),
      GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
      GoRoute(
        path: '/register',
        builder: (context, state) => const RegisterScreen(),
      ),
      GoRoute(
        path: '/forgot-password',
        builder: (context, state) => const ForgotPasswordScreen(),
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
                      final id =
                          int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
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
                      final id =
                          int.tryParse(state.pathParameters['id'] ?? '0') ?? 0;
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
        parentNavigatorKey: rootNavigatorKey,
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
        parentNavigatorKey: rootNavigatorKey,
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
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) =>
            _buildPageWithAnimation(const AnnouncementListScreen(), state),
      ),
      GoRoute(
        path: '/peralatan',
        parentNavigatorKey: rootNavigatorKey,
        pageBuilder: (context, state) =>
            _buildPageWithAnimation(const PeralatanListScreen(), state),
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
