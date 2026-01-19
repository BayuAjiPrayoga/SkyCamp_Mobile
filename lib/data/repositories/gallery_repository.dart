// Gallery Repository - Akses data galeri foto

import 'package:dio/dio.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/gallery_model.dart';

class GalleryRepository {
  final ApiClient _apiClient = apiClient;

  Future<List<GalleryPhoto>> getAll() async {
    try {
      final response = await _apiClient.get(ApiConfig.galleries);
      if (response.statusCode == 200) {
        dynamic rawData = response.data is List ? response.data : response.data['data'];
        if (rawData is Map && rawData.containsKey('data') && rawData['data'] is List) {
           rawData = rawData['data'];
        }
        final List<dynamic> data = rawData is List ? rawData : [];
        return data.map((json) => GalleryPhoto.fromJson(json)).toList();
      }
      return [];
    } on DioException catch (e) {
      throw Exception(e.response?.data['message'] ?? 'Failed to fetch gallery');
    }
  }

  Future<GalleryResult> uploadPhoto(String imagePath, String? caption) async {
    try {
      final formData = FormData.fromMap({
        'image': await MultipartFile.fromFile(imagePath),
        if (caption != null && caption.isNotEmpty) 'caption': caption,
      });

      final response = await _apiClient.postFormData(ApiConfig.galleries, formData);

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data is Map && response.data.containsKey('data')
            ? response.data['data']
            : response.data;
        return GalleryResult.success(photo: GalleryPhoto.fromJson(data));
      }
      return GalleryResult.error(message: 'Upload failed');
    } on DioException catch (e) {
      String errorMessage = 'Gagal mengupload foto';
      if (e.response?.statusCode == 413) {
        errorMessage = 'Ukuran foto terlalu besar (Maks 10MB)';
      } else if (e.response?.statusCode == 422) {
        if (e.response?.data is Map && e.response?.data['errors']?['image'] != null) {
          errorMessage = e.response?.data['errors']['image'][0]?.toString() ?? 'Validasi gagal';
        }
      } else if (e.response?.data?['message'] != null) {
        errorMessage = e.response?.data['message']?.toString() ?? 'Gagal mengupload foto';
      }
      return GalleryResult.error(message: errorMessage);
    }
  }
}

class GalleryResult {
  final bool isSuccess;
  final GalleryPhoto? photo;
  final String? message;

  GalleryResult._({required this.isSuccess, this.photo, this.message});
  factory GalleryResult.success({required GalleryPhoto photo}) => GalleryResult._(isSuccess: true, photo: photo);
  factory GalleryResult.error({required String message}) => GalleryResult._(isSuccess: false, message: message);
}

final galleryRepository = GalleryRepository();
