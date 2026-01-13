import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/kavling_model.dart';

class KavlingRepository {
  final ApiClient _apiClient = apiClient;

  Future<List<Kavling>> getAll({DateTime? checkIn, DateTime? checkOut}) async {
    try {
      String url = ApiConfig.kavlings;
      
      // Add query parameters if dates are provided
      if (checkIn != null && checkOut != null) {
        final checkInStr = checkIn.toIso8601String().split('T')[0];
        final checkOutStr = checkOut.toIso8601String().split('T')[0];
        url += '?check_in=$checkInStr&check_out=$checkOutStr';
      }

      final response = await _apiClient.get(url);
      
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data is List 
            ? response.data 
            : response.data['data'] ?? [];
        return data.map((json) => Kavling.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch kavlings');
    }
  }

  Future<Kavling?> getById(int id) async {
    try {
      final response = await _apiClient.get('${ApiConfig.kavlings}/$id');
      
      if (response.statusCode == 200) {
        // API returns: { "data": { "kavling": {...}, "is_available": true } }
        var data = response.data;
        
        // Unwrap 'data' if exists
        if (data is Map && data.containsKey('data')) {
          data = data['data'];
        }

        // Check if we have the nested structure
        if (data is Map && data.containsKey('kavling')) {
          final kavlingData = Map<String, dynamic>.from(data['kavling']);
          // Merge is_available if it exists as a sibling
          if (data.containsKey('is_available')) {
            kavlingData['is_available'] = data['is_available'];
          }
          return Kavling.fromJson(kavlingData);
        }
        
        return Kavling.fromJson(data);
      }
      return null;
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch kavling');
    }
  }

  Future<List<Kavling>> getAvailable() async {
    final all = await getAll();
    return all.where((k) => k.isAvailable).toList();
  }
}

final kavlingRepository = KavlingRepository();
