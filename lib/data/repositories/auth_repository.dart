import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import 'package:firebase_auth/firebase_auth.dart'
    hide User; // Avoid conflict with user_model
import 'package:google_sign_in/google_sign_in.dart';
import '../../core/config/api_config.dart';
import '../../core/services/notification_service.dart';
import '../models/user_model.dart';

class AuthRepository {
  final ApiClient _apiClient = apiClient;

  Future<AuthResult> login(String email, String password) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.login,
        data: {'email': email, 'password': password},
      );

      if (response.statusCode == 200) {
        final responseData = response.data['data'];
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);

        await _apiClient.saveToken(token);

        // Send FCM token to backend for push notifications
        await notificationService.getTokenAndSync();

        return AuthResult.success(user: user, token: token);
      }

      return AuthResult.error(message: 'Login failed');
    } on DioException catch (e) {
      return AuthResult.error(
        message: e.response?.data['message'] ?? 'Network error',
      );
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  Future<AuthResult> register({
    required String name,
    required String email,
    required String password,
    required String passwordConfirmation,
    String? phone,
  }) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.register,
        data: {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': passwordConfirmation,
          'phone': phone,
        },
      );

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
      } else if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.receiveTimeout) {
        message = 'Koneksi timeout. Periksa koneksi internet Anda.';
      } else if (e.type == DioExceptionType.connectionError) {
        message =
            'Tidak dapat terhubung ke server. Pastikan server berjalan di https://api.luhur.my.id';
      }

      return AuthResult.error(message: message);
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  Future<User?> getUser() async {
    try {
      final response = await _apiClient.get(ApiConfig.user);
      if (response.statusCode == 200) {
        // Handle both { data: {...} } and direct user object response
        final userData = response.data['data'] ?? response.data;
        return User.fromJson(userData);
      }
      return null;
    } catch (_) {
      return null;
    }
  }

  Future<void> logout() async {
    try {
      // Remove FCM token from backend before logging out
      await notificationService.removeTokenFromBackend();
      await _apiClient.post(ApiConfig.logout);
    } finally {
      await _apiClient.clearToken();
    }
  }

  Future<bool> isLoggedIn() async {
    return await _apiClient.hasToken();
  }

  Future<UpdateProfileResult> updateProfile({
    required String name,
    String? phone,
  }) async {
    try {
      final response = await _apiClient.put(
        ApiConfig.updateProfile,
        data: {'name': name, if (phone != null) 'phone': phone},
      );

      if (response.statusCode == 200) {
        final userData = response.data['data'] ?? response.data;
        final user = User.fromJson(userData);
        return UpdateProfileResult.success(user: user);
      }

      return UpdateProfileResult.error(message: 'Update failed');
    } on DioException catch (e) {
      return UpdateProfileResult.error(
        message: e.response?.data['message'] ?? 'Network error',
      );
    } catch (e) {
      return UpdateProfileResult.error(message: e.toString());
    }
  }

  Future<AuthResult> updateAvatar(String imagePath) async {
    try {
      final formData = FormData.fromMap({
        'avatar': await MultipartFile.fromFile(imagePath),
      });

      final response = await _apiClient.postFormData(
        '${ApiConfig.baseUrl}/user/avatar',
        formData,
      );

      if (response.statusCode == 200) {
        final userData = response.data['data'] ?? response.data;
        final user = User.fromJson(userData);
        return AuthResult.success(user: user, token: '');
      }

      return AuthResult.error(message: 'Update avatar failed');
    } on DioException catch (e) {
      return AuthResult.error(
        message: e.response?.data['message'] ?? 'Network error',
      );
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  Future<AuthResult> changePassword({
    required String currentPassword,
    required String newPassword,
    required String confirmPassword,
  }) async {
    try {
      final response = await _apiClient.post(
        '${ApiConfig.baseUrl}/user/change-password',
        data: {
          'current_password': currentPassword,
          'password': newPassword,
          'password_confirmation': confirmPassword,
        },
      );

      if (response.statusCode == 200) {
        return AuthResult.success(user: User.empty(), token: '');
      }

      return AuthResult.error(message: 'Change password failed');
    } on DioException catch (e) {
      return AuthResult.error(
        message: e.response?.data['message'] ?? 'Network error',
      );
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }

  Future<AuthResult> loginWithGoogle() async {
    try {
      // // print('GOOGLE_LOGIN: Starting Google Sign In...');
      // 1. Trigger Google Sign-In
      final googleSignIn = GoogleSignIn(
        scopes: ['email', 'profile'],
        serverClientId:
            '120652636812-l2gqg4oh47uhc2n6adbfgk5nv4ojjkig.apps.googleusercontent.com',
      );

      // Sign out first for fresh sign-in
      try {
        await googleSignIn.disconnect();
      } catch (_) {
        // Ignore if already disconnected
      }

      final googleUser = await googleSignIn.signIn();
      if (googleUser == null) {
        // // print('GOOGLE_LOGIN: Sign In Aborted by user.');
        return AuthResult.error(message: 'Login Google dibatalkan');
      }

      // // print('GOOGLE_LOGIN: User signed in: ${googleUser.email}');

      // 2. Obtain OAuth Details
      final GoogleSignInAuthentication googleAuth =
          await googleUser.authentication;

      if (googleAuth.idToken == null) {
        // // print('GOOGLE_LOGIN: ID Token is null.');
        return AuthResult.error(
          message:
              'Gagal mendapatkan Google ID Token. Pastikan SHA-1 sudah dikonfigurasi di Firebase Console.',
        );
      }

      // // print('GOOGLE_LOGIN: Obtained ID Token.');

      // 3. Create Credential for Firebase
      final credential = GoogleAuthProvider.credential(
        accessToken: googleAuth.accessToken,
        idToken: googleAuth.idToken,
      );

      // 4. Sign-in to Firebase
      // // print('GOOGLE_LOGIN: Signing into Firebase...');
      final UserCredential userCredential = await FirebaseAuth.instance
          .signInWithCredential(credential);

      // 5. Get Firebase ID Token
      final idToken = await userCredential.user?.getIdToken();
      if (idToken == null) {
        // // print('GOOGLE_LOGIN: Firebase ID Token is null.');
        return AuthResult.error(message: 'Gagal mendapatkan Firebase ID Token');
      }

      // // print('GOOGLE_LOGIN: Firebase Signed In. Token obtained. Sending to backend...');

      // 6. Send Token to Backend
      final response = await _apiClient.post(
        '/auth/firebase-login',
        data: {'token': idToken},
      );

      // // print('GOOGLE_LOGIN: Backend Response: ${response.statusCode}');

      if (response.statusCode == 200) {
        final responseData = response.data['data'];
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);

        await _apiClient.saveToken(token);

        // Send FCM token to backend for push notifications
        await notificationService.getTokenAndSync();

        // // print('GOOGLE_LOGIN: Success! Token saved.');
        return AuthResult.success(user: user, token: token);
      }

      // // print('GOOGLE_LOGIN: Backend failed: ${response.data}');
      return AuthResult.error(
        message: response.data['message'] ?? 'Login Backend failed',
      );
    } on DioException catch (e) {
      // // print('GOOGLE_LOGIN: DioError: ${e.message}');
      // // print('GOOGLE_LOGIN: Response Data: ${e.response?.data}'); // Added this line
      String errorMessage = 'Network error';
      if (e.response?.data != null && e.response?.data['message'] != null) {
        errorMessage = e.response!.data['message'];
      } else if (e.type == DioExceptionType.connectionTimeout) {
        errorMessage = 'Connection timeout';
      } else if (e.type == DioExceptionType.connectionError) {
        errorMessage = 'Cannot connect to server';
      }
      return AuthResult.error(message: errorMessage);
    } on FirebaseAuthException catch (e) {
      // // print('GOOGLE_LOGIN: FirebaseAuthException: ${e.message}');
      return AuthResult.error(message: 'Firebase Error: ${e.message}');
    } catch (e) {
      // // print('GOOGLE_LOGIN: Unknown Error: $e');
      return AuthResult.error(message: e.toString());
    }
  }

  Future<AuthResult> sendPasswordResetEmail(String email) async {
    try {
      final response = await _apiClient.post(
        '/forgot-password',
        data: {'email': email},
      );

      if (response.statusCode == 200) {
        return AuthResult.success(user: User.empty(), token: '');
      }

      return AuthResult.error(
        message:
            response.data['message'] ?? 'Gagal mengirim link reset password',
      );
    } on DioException catch (e) {
      return AuthResult.error(
        message: e.response?.data['message'] ?? 'Terjadi kesalahan jaringan',
      );
    } catch (e) {
      return AuthResult.error(message: e.toString());
    }
  }
}

class AuthResult {
  final bool isSuccess;
  final User? user;
  final String? token;
  final String? message;

  AuthResult._({required this.isSuccess, this.user, this.token, this.message});

  factory AuthResult.success({required User user, required String token}) {
    return AuthResult._(isSuccess: true, user: user, token: token);
  }

  factory AuthResult.error({required String message}) {
    return AuthResult._(isSuccess: false, message: message);
  }
}

class UpdateProfileResult {
  final bool isSuccess;
  final User? user;
  final String? message;

  UpdateProfileResult._({required this.isSuccess, this.user, this.message});

  factory UpdateProfileResult.success({required User user}) {
    return UpdateProfileResult._(isSuccess: true, user: user);
  }

  factory UpdateProfileResult.error({required String message}) {
    return UpdateProfileResult._(isSuccess: false, message: message);
  }
}

// Singleton
final authRepository = AuthRepository();
