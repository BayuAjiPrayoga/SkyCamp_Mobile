import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/user_model.dart';

class AuthRepository {
  final ApiClient _apiClient = apiClient;

  Future<AuthResult> login(String email, String password) async {
    try {
      final response = await _apiClient.post(
        ApiConfig.login,
        data: {
          'email': email,
          'password': password,
        },
      );

      if (response.statusCode == 200) {
        final responseData = response.data['data']; // Access nested 'data' object
        final token = responseData['token'];
        final user = User.fromJson(responseData['user']);
        
        await _apiClient.saveToken(token);
        
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
        final responseData = response.data['data']; // Access nested 'data' object
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
        message = 'Tidak dapat terhubung ke server. Pastikan server berjalan di http://192.168.1.117:8000';
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
        return User.fromJson(response.data);
      }
      return null;
    } catch (e) {
      return null;
    }
  }

  Future<void> logout() async {
    try {
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
        data: {
          'name': name,
          if (phone != null) 'phone': phone,
        },
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
        return AuthResult.success(user: user, token: ''); // Token not needed here
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
        return AuthResult.success(user: User.empty(), token: ''); // Dummy user
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
}

class AuthResult {
  final bool isSuccess;
  final User? user;
  final String? token;
  final String? message;

  AuthResult._({
    required this.isSuccess,
    this.user,
    this.token,
    this.message,
  });

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

  UpdateProfileResult._({
    required this.isSuccess,
    this.user,
    this.message,
  });

  factory UpdateProfileResult.success({required User user}) {
    return UpdateProfileResult._(isSuccess: true, user: user);
  }

  factory UpdateProfileResult.error({required String message}) {
    return UpdateProfileResult._(isSuccess: false, message: message);
  }
}

// Singleton
final authRepository = AuthRepository();
