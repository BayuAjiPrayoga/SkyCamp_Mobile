import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../core/network/api_client.dart';
import '../../core/config/api_config.dart';
import '../models/announcement_model.dart';


final announcementRepositoryProvider = Provider<AnnouncementRepository>((ref) {
  return AnnouncementRepository(apiClient);
});

class AnnouncementRepository {
  final ApiClient _apiClient;

  AnnouncementRepository(this._apiClient);

  Future<List<Announcement>> getAnnouncements() async {
    try {
      final response = await _apiClient.get(ApiConfig.announcements);
      final data = response.data['data'] as List;
      return data.map((e) => Announcement.fromJson(e)).toList();
    } catch (e) {
      throw Exception('Failed to load announcements: $e');
    }
  }
}
