import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/user_model.dart';
import '../../data/repositories/auth_repository.dart';

export '../../data/repositories/auth_repository.dart' show UpdateProfileResult;

// Auth State
enum AuthStatus { initial, loading, authenticated, unauthenticated, error, loginSuccess }

class AuthState {
  final AuthStatus status;
  final User? user;
  final String? errorMessage;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.errorMessage,
  });

  AuthState copyWith({AuthStatus? status, User? user, String? errorMessage}) {
    return AuthState(
      status: status ?? this.status,
      user: user ?? this.user,
      errorMessage: errorMessage,
    );
  }
}

// Auth Notifier
class AuthNotifier extends StateNotifier<AuthState> {
  final AuthRepository _repository = authRepository;

  AuthNotifier() : super(const AuthState()) {
    checkAuthStatus();
  }

  Future<void> checkAuthStatus() async {
    state = state.copyWith(status: AuthStatus.loading);

    final isLoggedIn = await _repository.isLoggedIn();
    if (isLoggedIn) {
      final user = await _repository.getUser();
      if (user != null) {
        state = state.copyWith(status: AuthStatus.authenticated, user: user);
        return;
      }
    }

    state = state.copyWith(status: AuthStatus.unauthenticated);
  }

  Future<bool> login(String email, String password) async {
    // Note: Local loading is handled by UI (LoginScreen) to avoid redirects
    // state = state.copyWith(status: AuthStatus.loading);

    final result = await _repository.login(email, password);

    if (result.isSuccess) {
      state = state.copyWith(
        // Keep status as-is (unauthenticated) to prevent router refresh/redirect during animation
        // status: AuthStatus.loginSuccess, 
        user: result.user,
      );
      return true;
    }

    state = state.copyWith(
      // status: AuthStatus.error, <--- OLD
      status: state.status, // Keep current status to prevent router rebuild/green screen
      errorMessage: result.message,
    );
    return false;
  }

  Future<bool> loginWithGoogle() async {
    // Note: Local loading is handled by UI
    // state = state.copyWith(status: AuthStatus.loading);

    final result = await _repository.loginWithGoogle();

    if (result.isSuccess) {
      state = state.copyWith(
        // Keep status as-is (unauthenticated) to allow animation to finish
        // status: AuthStatus.loginSuccess,
        user: result.user,
      );
      return true;
    }

    state = state.copyWith(
      // status: AuthStatus.error,
      status: state.status, // Keep current status
      errorMessage: result.message,
    );
    return false;
  }

  void finalizeLogin() {
    // Called by UI after success animation is complete
    state = state.copyWith(status: AuthStatus.authenticated);
  }

  Future<bool> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    state = state.copyWith(status: AuthStatus.loading);

    final result = await _repository.register(
      name: name,
      email: email,
      password: password,
      passwordConfirmation: passwordConfirmation,
      phone: phone,
    );

    if (result.isSuccess) {
      state = state.copyWith(
        status: AuthStatus.authenticated, // Register creates session immediately
        user: result.user,
      );
      return true;
    }

    state = state.copyWith(
      // status: AuthStatus.error,
      status: state.status, // Keep current status
      errorMessage: result.message,
    );
    return false;
  }

  Future<void> logout() async {
    state = state.copyWith(status: AuthStatus.loading);
    await _repository.logout();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }

  /// Refresh user data from backend
  Future<void> refreshUser() async {
    final user = await _repository.getUser();
    if (user != null) {
      state = state.copyWith(user: user);
    }
  }

  Future<UpdateProfileResult> updateProfile({
    required String name,
    String? phone,
  }) async {
    final result = await _repository.updateProfile(name: name, phone: phone);

    if (result.isSuccess && result.user != null) {
      state = state.copyWith(user: result.user);
    }

    return result;
  }

  Future<AuthResult> updateAvatar(String imagePath) async {
    // Note: We don't set global loading here to avoid full screen loader
    // Local loading state should be handled by the UI
    final result = await _repository.updateAvatar(imagePath);

    if (result.isSuccess && result.user != null) {
      state = state.copyWith(user: result.user);
    }

    return result;
  }

  Future<AuthResult> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) async {
    return await _repository.changePassword(
      currentPassword: currentPassword,
      newPassword: newPassword,
      confirmPassword: confirmPassword,
    );
  }

  void clearError() {
    state = state.copyWith(
      status: state.user != null
          ? AuthStatus.authenticated
          : AuthStatus.unauthenticated,
      errorMessage: null,
    );
  }

  Future<bool> forgotPassword(String email) async {
    state = state.copyWith(status: AuthStatus.loading);

    final result = await _repository.sendPasswordResetEmail(email);

    if (result.isSuccess) {
      state = state.copyWith(
          status: AuthStatus.unauthenticated); // Reset to idle
      return true;
    }

    state = state.copyWith(
      status: AuthStatus.error,
      errorMessage: result.message,
    );
    return false;
  }
}

// Provider
final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
