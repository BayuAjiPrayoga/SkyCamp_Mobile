import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/user_model.dart';
import '../../data/repositories/auth_repository.dart';

export '../../data/repositories/auth_repository.dart' show UpdateProfileResult;

// Auth State
enum AuthStatus { initial, loading, authenticated, unauthenticated, error }

class AuthState {
  final AuthStatus status;
  final User? user;
  final String? errorMessage;

  const AuthState({
    this.status = AuthStatus.initial,
    this.user,
    this.errorMessage,
  });

  AuthState copyWith({
    AuthStatus? status,
    User? user,
    String? errorMessage,
  }) {
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
        state = state.copyWith(
          status: AuthStatus.authenticated,
          user: user,
        );
        return;
      }
    }
    
    state = state.copyWith(status: AuthStatus.unauthenticated);
  }

  Future<bool> login(String email, String password) async {
    state = state.copyWith(status: AuthStatus.loading);
    
    final result = await _repository.login(email, password);
    
    if (result.isSuccess) {
      state = state.copyWith(
        status: AuthStatus.authenticated,
        user: result.user,
      );
      return true;
    }
    
    state = state.copyWith(
      status: AuthStatus.error,
      errorMessage: result.message,
    );
    return false;
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
        status: AuthStatus.authenticated,
        user: result.user,
      );
      return true;
    }
    
    state = state.copyWith(
      status: AuthStatus.error,
      errorMessage: result.message,
    );
    return false;
  }

  Future<void> logout() async {
    state = state.copyWith(status: AuthStatus.loading);
    await _repository.logout();
    state = const AuthState(status: AuthStatus.unauthenticated);
  }

  Future<UpdateProfileResult> updateProfile({
    required String name,
    String? phone,
  }) async {
    final result = await _repository.updateProfile(
      name: name,
      phone: phone,
    );

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
}

// Provider
final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  return AuthNotifier();
});
