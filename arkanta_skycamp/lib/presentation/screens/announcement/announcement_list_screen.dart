
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/announcement_provider.dart';

class AnnouncementListScreen extends ConsumerStatefulWidget {
  const AnnouncementListScreen({super.key});

  @override
  ConsumerState<AnnouncementListScreen> createState() => _AnnouncementListScreenState();
}

class _AnnouncementListScreenState extends ConsumerState<AnnouncementListScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => ref.read(announcementProvider.notifier).loadAnnouncements());
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(announcementProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Pengumuman'),
      ),
      body: _buildBody(state),
    );
  }

  Widget _buildBody(AnnouncementState state) {
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
            Text('Gagal memuat pengumuman'),
            const SizedBox(height: 8),
            ElevatedButton(
              onPressed: () => ref.read(announcementProvider.notifier).loadAnnouncements(),
              child: const Text('Coba Lagi'),
            ),
          ],
        ),
      );
    }

    if (state.announcements.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.campaign_outlined, size: 64, color: AppColors.textMuted),
            const SizedBox(height: 16),
            Text(
              'Belum ada pengumuman',
              style: TextStyle(color: AppColors.textSecondary),
            ),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => ref.read(announcementProvider.notifier).loadAnnouncements(),
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: state.announcements.length,
        itemBuilder: (context, index) {
          final announcement = state.announcements[index];
          return Card(
            margin: const EdgeInsets.only(bottom: 12),
            child: ExpansionTile(
              leading: Icon(
                Icons.info_outline,
                color: announcement.type == 'warning' ? AppColors.warning : AppColors.primary,
              ),
              title: Text(
                announcement.title,
                style: const TextStyle(fontWeight: FontWeight.bold),
              ),
              subtitle: Text(
                DateFormat('dd MMM yyyy').format(announcement.createdAt),
                style: TextStyle(color: AppColors.textSecondary, fontSize: 12),
              ),
              children: [
                Padding(
                  padding: const EdgeInsets.all(16),
                  child: SizedBox(
                    width: double.infinity,
                    child: Text(
                      announcement.content,
                      textAlign: TextAlign.left,
                      style: const TextStyle(height: 1.5),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
