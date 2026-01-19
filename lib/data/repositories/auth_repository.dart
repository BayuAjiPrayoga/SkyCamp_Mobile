// Auth Repository - Operasi autentikasi (login, register, Google Sign-In, profile)

import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import 'package:firebase_auth/firebase_auth.dart' hide User;
import 'package:google_sign_in/google_sign_in.dart';
import '../../core/config/api_config.dart';
import '../../core/services/notification_service.dart';
import '../models/user_model.dart';

class AuthRepository {
  final ApiClient _apiClient = apiClient;

  // Login dengan email/password
  Future<AuthResult> login(String email, String password) async {
    try {
      final response = await _apiClient.post(ApiConfig.login, data: {'email': email, 'password': password});

      if (response.statusCode == 200) {
        final responseData = response.data['data'];
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);
        await _apiClient.saveToken(token);
        await notificationService.getTokenAndSync();
        return AuthResult.success(user: user, token: token);
      }
      return AuthResult.error(message: 'Login failed');
    } on DioException catch (e) {
      return AuthResult.error(message: e.response?.data['message'] ?? 'Network error');
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  // Register user baru
  Future<AuthResult> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    try {
      final response = await _apiClient.post(ApiConfig.register, data: {
        'name': name,
        'email': email,
        'password': password,
        'password_confirmation': passwordConfirmation,
        'phone': phone,
      });

      if (response.statusCode == 200 || response.statusCode == 201) {
        final responseData = response.data['data'];
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);
        await _apiClient.saveToken(token);
        return AuthResult.success(user: user, token: token);
      }
      return AuthResult.error(message: 'Registration failed');
    } on DioException catch (e) {
      final errors = e.response?.data['errors'];
      String message = 'Registration failed';
      if (errors != null && errors is Map) {
        final firstError = errors.values.first;
        if (firstError is List && firstError.isNotEmpty) {
          message = firstError.first.toString();
        }
      } else if (e.response?.data['message'] != null) {
        message = e.response!.data['message'];
      }
      return AuthResult.error(message: message);
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  // Get user data
  Future<User?> getUser() async {
    try {
      final response = await _apiClient.get(ApiConfig.user);
      if (response.statusCode == 200) {
        final userData = response.data['data'] ?? response.data;
        return User.fromJson(userData);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  // Logout
  Future<void> logout() async {
    try {
      await notificationService.removeTokenFromBackend();
      await _apiClient.post(ApiConfig.logout);
    } finally {
      await _apiClient.clearToken();
    }
  }

  Future<bool> isLoggedIn() async => await _apiClient.hasToken();

  // Update profile
  Future<UpdateProfileResult> updateProfile({required String name, String? phone}) async {
    try {
      final response = await _apiClient.put(ApiConfig.updateProfile, data: {'name': name, if (phone != null) 'phone': phone});
      if (response.statusCode == 200) {
        final userData = response.data['data'] ?? response.data;
        return UpdateProfileResult.success(user: User.fromJson(userData));
      }
      return UpdateProfileResult.error(message: 'Update failed');
    } on DioException catch (e) {
      return UpdateProfileResult.error(message: e.response?.data['message'] ?? 'Network error');
    } catch (e) {
      return UpdateProfileResult.error(message: e.toString());
    }
  }

  // Update avatar
  Future<AuthResult> updateAvatar(String imagePath) async {
    try {
      final formData = FormData.fromMap({'avatar': await MultipartFile.fromFile(imagePath)});
      final response = await _apiClient.postFormData('${ApiConfig.baseUrl}/user/avatar', formData);
      if (response.statusCode == 200) {
        final userData = response.data['data'] ?? response.data;
        return AuthResult.success(user: User.fromJson(userData), token: '');
      }
      return AuthResult.error(message: 'Update avatar failed');
    } on DioException catch (e) {
      return AuthResult.error(message: e.response?.data['message'] ?? 'Network error');
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  // Change password
  Future<AuthResult> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) async {
    try {
      final response = await _apiClient.post('${ApiConfig.baseUrl}/user/change-password', data: {
        'current_password': currentPassword,
        'password': newPassword,
        'password_confirmation': confirmPassword,
      });
      if (response.statusCode == 200) {
        return AuthResult.success(user: User.empty(), token: '');
      }
      return AuthResult.error(message: 'Change password failed');
    } on DioException catch (e) {
      return AuthResult.error(message: e.response?.data['message'] ?? 'Network error');
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  // Login dengan Google
  Future<AuthResult> loginWithGoogle() async {
    try {
      final googleSignIn = GoogleSignIn(
        scopes: ['email', 'profile'],
        serverClientId: '120652636812-l2gqg4oh47uhc2n6adbfgk5nv4ojjkig.apps.googleusercontent.com',
      );

      try { await googleSignIn.disconnect(); } catch (_) {}

      final googleUser = await googleSignIn.signIn();
      if (googleUser == null) return AuthResult.error(message: 'Login Google dibatalkan');

      final GoogleSignInAuthentication googleAuth = await googleUser.authentication;
      if (googleAuth.idToken == null) {
        return AuthResult.error(message: 'Gagal mendapatkan Google ID Token');
      }

      final credential = GoogleAuthProvider.credential(
        accessToken: googleAuth.accessToken,
        idToken: googleAuth.idToken,
      );

      final UserCredential userCredential = await FirebaseAuth.instance.signInWithCredential(credential);
      final idToken = await userCredential.user?.getIdToken();
      if (idToken == null) return AuthResult.error(message: 'Gagal mendapatkan Firebase ID Token');

      final response = await _apiClient.post('/auth/firebase-login', data: {'token': idToken});

      if (response.statusCode == 200) {
        final responseData = response.data['data'];
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);
        await _apiClient.saveToken(token);
        await notificationService.getTokenAndSync();
        return AuthResult.success(user: user, token: token);
      }
      return AuthResult.error(message: response.data['message'] ?? 'Login Backend failed');
    } on DioException catch (e) {
      return AuthResult.error(message: e.response?.data['message'] ?? 'Network error');
    } on FirebaseAuthException catch (e) {
      return AuthResult.error(message: 'Firebase Error: ${e.message}');
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  // Send password reset email
  Future<AuthResult> sendPasswordResetEmail(String email) async {
    try {
      final response = await _apiClient.post('/forgot-password', data: {'email': email});
      if (response.statusCode == 200) {
        return AuthResult.success(user: User.empty(), token: '');
      }
      return AuthResult.error(message: response.data['message'] ?? 'Gagal mengirim link reset password');
    } on DioException catch (e) {
      return AuthResult.error(message: e.response?.data['message'] ?? 'Terjadi kesalahan jaringan');
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }
}

// Result classes
class AuthResult {
  final bool isSuccess;
  final User? user;
  final String? token;
  final String? message;

  AuthResult._({required this.isSuccess, this.user, this.token, this.message});
  factory AuthResult.success({required User user, required String token}) => AuthResult._(isSuccess: true, user: user, token: token);
  factory AuthResult.error({required String message}) => AuthResult._(isSuccess: false, message: message);
}

class UpdateProfileResult {
  final bool isSuccess;
  final User? user;
  final String? message;

  UpdateProfileResult._({required this.isSuccess, this.user, this.message});
  factory UpdateProfileResult.success({required User user}) => UpdateProfileResult._(isSuccess: true, user: user);
  factory UpdateProfileResult.error({required String message}) => UpdateProfileResult._(isSuccess: false, message: message);
}

final authRepository = AuthRepository();
