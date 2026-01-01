import 'package:flutter/foundation.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Platform-aware secure storage wrapper
/// Uses SharedPreferences for Web (flutter_secure_storage has limited web support)
/// Uses FlutterSecureStorage for mobile platforms
class SecureStorage {
  static const String _tokenKey = 'auth_token';
  
  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();
  SharedPreferences? _prefs;
  
  Future<void> _initPrefs() async {
    _prefs ??= await SharedPreferences.getInstance();
  }
  
  Future<void> write({required String key, required String? value}) async {
    if (kIsWeb) {
      await _initPrefs();
      if (value != null) {
        await _prefs!.setString(key, value);
      } else {
        await _prefs!.remove(key);
      }
    } else {
      await _secureStorage.write(key: key, value: value);
    }
  }
  
  Future<String?> read({required String key}) async {
    if (kIsWeb) {
      await _initPrefs();
      return _prefs!.getString(key);
    } else {
      return await _secureStorage.read(key: key);
    }
  }
  
  Future<void> delete({required String key}) async {
    if (kIsWeb) {
      await _initPrefs();
      await _prefs!.remove(key);
    } else {
      await _secureStorage.delete(key: key);
    }
  }
  
  // Convenience methods for auth token
  Future<void> saveToken(String token) async {
    await write(key: _tokenKey, value: token);
  }
  
  Future<String?> getToken() async {
    return await read(key: _tokenKey);
  }
  
  Future<void> clearToken() async {
    await delete(key: _tokenKey);
  }
  
  Future<bool> hasToken() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }
}

// Singleton instance
final secureStorage = SecureStorage();
