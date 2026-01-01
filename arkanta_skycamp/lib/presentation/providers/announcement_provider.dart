
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/announcement_model.dart';
import '../../data/repositories/announcement_repository.dart';

// State
class AnnouncementState {
  final List<Announcement> announcements;
  final bool isLoading;
  final String? error;

  AnnouncementState({
    this.announcements = const [],
    this.isLoading = false,
    this.error,
  });

  AnnouncementState copyWith({
    List<Announcement>? announcements,
    bool? isLoading,
    String? error,
  }) {
    return AnnouncementState(
      announcements: announcements ?? this.announcements,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

// Notifier
class AnnouncementNotifier extends StateNotifier<AnnouncementState> {
  final AnnouncementRepository _repository;

  AnnouncementNotifier(this._repository) : super(AnnouncementState());

  Future<void> loadAnnouncements() async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final announcements = await _repository.getAnnouncements();
      state = state.copyWith(
        announcements: announcements,
        isLoading: false,
      );
    } catch (e) {
      state = state.copyWith(
        isLoading: false,
        error: e.toString(),
      );
    }
  }
}

// Provider
final announcementProvider = StateNotifierProvider<AnnouncementNotifier, AnnouncementState>((ref) {
  final repository = ref.watch(announcementRepositoryProvider);
  return AnnouncementNotifier(repository);
});
