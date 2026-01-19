// Secure Storage - Penyimpanan aman untuk token
// Mobile: FlutterSecureStorage, Web: SharedPreferences

import 'package:flutter/foundation.dart' show kIsWeb;
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';

class SecureStorage {
  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();
  SharedPreferences? _prefs;
  
  static const String _tokenKey = 'auth_token';

  Future<void> _initPrefs() async {
    _prefs ??= await SharedPreferences.getInstance();
  }

  // Generic write
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

  // Generic read
  Future<String?> read({required String key}) async {
    if (kIsWeb) {
      await _initPrefs();
      return _prefs!.getString(key);
    } else {
      return await _secureStorage.read(key: key);
    }
  }

  // Generic delete
  Future<void> delete({required String key}) async {
    if (kIsWeb) {
      await _initPrefs();
      await _prefs!.remove(key);
    } else {
      await _secureStorage.delete(key: key);
    }
  }

  // Token-specific methods
  Future<void> saveToken(String token) => write(key: _tokenKey, value: token);
  Future<String?> getToken() => read(key: _tokenKey);
  Future<void> deleteToken() => delete(key: _tokenKey);
  Future<bool> hasToken() async => (await getToken()) != null;
}
