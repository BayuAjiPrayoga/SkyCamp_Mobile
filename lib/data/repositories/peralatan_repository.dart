import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/peralatan_model.dart';

class PeralatanRepository {
  final ApiClient _apiClient = apiClient;

  Future<List<Peralatan>> getAll() async {
    try {
      final response = await _apiClient.get(ApiConfig.peralatan);
      
      if (response.statusCode == 200) {
        final List<dynamic> data = response.data is List 
            ? response.data 
            : response.data['data'] ?? [];
        return data.map((json) => Peralatan.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch peralatan');
    }
  }

  Future<Peralatan?> getById(int id) async {
    try {
      final response = await _apiClient.get('${ApiConfig.peralatan}/$id');
      
      if (response.statusCode == 200) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return Peralatan.fromJson(data);
      }
      return null;
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch peralatan');
    }
  }

  Future<List<Peralatan>> getByCategory(String category) async {
    final all = await getAll();
    return all.where((p) => p.kategori.toLowerCase() == category.toLowerCase()).toList();
  }

  Future<List<Peralatan>> getAvailable() async {
    final all = await getAll();
    return all.where((p) => p.isAvailable).toList();
  }
}

final peralatanRepository = PeralatanRepository();
