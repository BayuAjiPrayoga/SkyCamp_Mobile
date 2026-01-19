// HTTP Client wrapper menggunakan Dio
// Dengan interceptor untuk auto-attach auth token

import 'package:dio/dio.dart';
import '../config/api_config.dart';
import '../storage/secure_storage.dart';

class ApiClient {
  late Dio _dio;
  final SecureStorage _storage = SecureStorage();

  ApiClient() {
    _dio = Dio(BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: const Duration(milliseconds: ApiConfig.connectTimeout),
      receiveTimeout: const Duration(milliseconds: ApiConfig.receiveTimeout),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Interceptor untuk auto-attach Bearer token
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.getToken();
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        return handler.next(options);
      },
      onError: (error, handler) {
        return handler.next(error);
      },
    ));
  }

  // Token management
  Future<void> saveToken(String token) => _storage.saveToken(token);
  Future<void> clearToken() => _storage.deleteToken();
  Future<bool> hasToken() => _storage.hasToken();

  // HTTP Methods
  Future<Response> get(String path) => _dio.get(path);
  
  Future<Response> post(String path, {Map<String, dynamic>? data}) => 
      _dio.post(path, data: data);
  
  Future<Response> put(String path, {Map<String, dynamic>? data}) => 
      _dio.put(path, data: data);
  
  Future<Response> delete(String path) => _dio.delete(path);
  
  Future<Response> postFormData(String path, FormData formData) =>
      _dio.post(path, data: formData);
}

// Singleton instance
final apiClient = ApiClient();
