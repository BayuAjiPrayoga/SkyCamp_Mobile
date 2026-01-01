import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/gallery_model.dart';
import '../../data/repositories/gallery_repository.dart';

class GalleryState {
  final List<GalleryPhoto> photos;
  final bool isLoading;
  final String? error;

  const GalleryState({
    this.photos = const [],
    this.isLoading = false,
    this.error,
  });

  GalleryState copyWith({
    List<GalleryPhoto>? photos,
    bool? isLoading,
    String? error,
  }) {
    return GalleryState(
      photos: photos ?? this.photos,
      isLoading: isLoading ?? this.isLoading,
      error: error,
    );
  }
}

class GalleryNotifier extends StateNotifier<GalleryState> {
  final GalleryRepository _repository = galleryRepository;

  GalleryNotifier() : super(const GalleryState());

  Future<void> loadPhotos() async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final photos = await _repository.getAll();
      // Only show approved photos
      final approved = photos.where((p) => p.isApproved).toList();
      state = state.copyWith(photos: approved, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<GalleryResult> uploadPhoto(String imagePath, String? caption) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _repository.uploadPhoto(imagePath, caption);

    if (result.isSuccess) {
      await loadPhotos();
    }

    state = state.copyWith(isLoading: false, error: result.message);
    return result;
  }
}

final galleryProvider = StateNotifierProvider<GalleryNotifier, GalleryState>((ref) {
  return GalleryNotifier();
});
