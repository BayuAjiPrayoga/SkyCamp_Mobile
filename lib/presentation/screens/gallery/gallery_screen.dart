import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:image_picker/image_picker.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/gallery_provider.dart';

class GalleryScreen extends ConsumerStatefulWidget {
  const GalleryScreen({super.key});

  @override
  ConsumerState<GalleryScreen> createState() => _GalleryScreenState();
}

class _GalleryScreenState extends ConsumerState<GalleryScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => ref.read(galleryProvider.notifier).loadPhotos());
  }

  Future<void> _uploadPhoto() async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery);
    
    if (image == null) return;

    if (!mounted) return;

    // Show caption dialog
    final caption = await showDialog<String>(
      context: context,
      builder: (dialogContext) => _CaptionDialog(),
    );

    if (!mounted) return;

    final result = await ref.read(galleryProvider.notifier).uploadPhoto(
      image.path,
      caption,
    );

    if (!mounted) return;

    if (result.isSuccess) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Foto diupload! Menunggu persetujuan admin.'),
          backgroundColor: AppColors.success,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Upload gagal'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(galleryProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Galeri Foto'),
      ),
      body: _buildBody(state),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: _uploadPhoto,
        icon: const Icon(Icons.add_photo_alternate),
        label: const Text('Upload Foto'),
        backgroundColor: AppColors.primary,
      ),
    );
  }

  Widget _buildBody(GalleryState state) {
    if (state.isLoading) {
      return const Center(child: CircularProgressIndicator());
    }

    if (state.error != null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.error_outline, size: 64, color: AppColors.error),
            const SizedBox(height: 16),
            Text('Gagal memuat galeri'),
            const SizedBox(height: 8),
            ElevatedButton(
              onPressed: () => ref.read(galleryProvider.notifier).loadPhotos(),
              child: const Text('Coba Lagi'),
            ),
          ],
        ),
      );
    }

    if (state.photos.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.photo_library_outlined, size: 64, color: AppColors.textMuted),
            const SizedBox(height: 16),
            Text(
              'Belum ada foto',
              style: TextStyle(color: AppColors.textSecondary),
            ),
            const SizedBox(height: 8),
            Text(
              'Upload foto camping Anda!',
              style: TextStyle(color: AppColors.textMuted),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => ref.read(galleryProvider.notifier).loadPhotos(),
      child: GridView.builder(
        padding: const EdgeInsets.all(8),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 8,
          crossAxisSpacing: 8,
        ),
        itemCount: state.photos.length,
        itemBuilder: (context, index) {
          final photo = state.photos[index];
          return _PhotoCard(
            imageUrl: photo.imageUrl,
            caption: photo.caption,
            userName: photo.userName,
            onTap: () => _showPhotoDetail(photo.imageUrl, photo.caption),
          );
        },
      ),
    );
  }

  void _showPhotoDetail(String imageUrl, String? caption) {
    showDialog(
      context: context,
      builder: (context) => Dialog(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.vertical(top: Radius.circular(12)),
              child: CachedNetworkImage(
                imageUrl: imageUrl,
                fit: BoxFit.cover,
                placeholder: (context, url) => const SizedBox(
                  height: 200,
                  child: Center(child: CircularProgressIndicator()),
                ),
                errorWidget: (context, url, error) => const SizedBox(
                  height: 200,
                  child: Icon(Icons.broken_image, size: 48),
                ),
              ),
            ),
            if (caption != null && caption.isNotEmpty)
              Padding(
                padding: const EdgeInsets.all(16),
                child: Text(caption),
              ),
          ],
        ),
      ),
    );
  }
}

class _PhotoCard extends StatelessWidget {
  final String imageUrl;
  final String? caption;
  final String? userName;
  final VoidCallback onTap;

  const _PhotoCard({
    required this.imageUrl,
    this.caption,
    this.userName,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Card(
        clipBehavior: Clip.antiAlias,
        child: Stack(
          fit: StackFit.expand,
          children: [
            CachedNetworkImage(
              imageUrl: imageUrl,
              fit: BoxFit.cover,
              placeholder: (context, url) => Container(
                color: Colors.grey.shade200,
                child: const Center(child: CircularProgressIndicator()),
              ),
              errorWidget: (context, url, error) => Container(
                color: Colors.grey.shade200,
                child: const Icon(Icons.broken_image),
              ),
            ),
            if (caption != null || userName != null)
              Positioned(
                bottom: 0,
                left: 0,
                right: 0,
                child: Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.bottomCenter,
                      end: Alignment.topCenter,
                      colors: [
                        Colors.black.withValues(alpha: 0.7),
                        Colors.transparent,
                      ],
                    ),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      if (userName != null)
                        Text(
                          userName!,
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      if (caption != null)
                        Text(
                          caption!,
                          style: const TextStyle(
                            color: Colors.white70,
                            fontSize: 11,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                    ],
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _CaptionDialog extends StatefulWidget {
  @override
  State<_CaptionDialog> createState() => _CaptionDialogState();
}

class _CaptionDialogState extends State<_CaptionDialog> {
  final _controller = TextEditingController();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: const Text('Tambah Caption'),
      content: TextField(
        controller: _controller,
        decoration: const InputDecoration(
          hintText: 'Tulis caption (opsional)...',
        ),
        maxLines: 2,
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(context, null),
          child: const Text('Lewati'),
        ),
        ElevatedButton(
          onPressed: () => Navigator.pop(context, _controller.text),
          child: const Text('Upload'),
        ),
      ],
    );
  }
}
