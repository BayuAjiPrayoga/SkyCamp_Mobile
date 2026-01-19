// Announcement Provider - State management pengumuman dengan caching

import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/announcement_model.dart';
import '../../data/repositories/announcement_repository.dart';

class AnnouncementState {
  final List<Announcement> announcements;
  final bool isLoading;
  final String? error;

  AnnouncementState({this.announcements = const [], this.isLoading = false, this.error});

  AnnouncementState copyWith({List<Announcement>? announcements, bool? isLoading, String? error}) => AnnouncementState(
    announcements: announcements ?? this.announcements,
    isLoading: isLoading ?? this.isLoading,
    error: error,
  );
}

class AnnouncementNotifier extends StateNotifier<AnnouncementState> {
  final AnnouncementRepository _repository;
  DateTime? _lastFetch;
  static const _cacheDuration = Duration(minutes: 10);

  AnnouncementNotifier(this._repository) : super(AnnouncementState());

  Future<void> loadAnnouncements({bool forceRefresh = false}) async {
    // Cache check
    if (!forceRefresh && state.announcements.isNotEmpty && _lastFetch != null &&
        DateTime.now().difference(_lastFetch!) < _cacheDuration) {
      return;
    }

    state = state.copyWith(isLoading: true, error: null);
    try {
      final announcements = await _repository.getAnnouncements();
      _lastFetch = DateTime.now();
      state = state.copyWith(announcements: announcements, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}

final announcementProvider = StateNotifierProvider<AnnouncementNotifier, AnnouncementState>((ref) {
  final repository = ref.watch(announcementRepositoryProvider);
  return AnnouncementNotifier(repository);
});
