import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/kavling_provider.dart';

class KavlingDetailScreen extends ConsumerStatefulWidget {
  final int kavlingId;

  const KavlingDetailScreen({super.key, required this.kavlingId});

  @override
  ConsumerState<KavlingDetailScreen> createState() => _KavlingDetailScreenState();
}

class _KavlingDetailScreenState extends ConsumerState<KavlingDetailScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => 
      ref.read(kavlingProvider.notifier).loadKavlingDetail(widget.kavlingId)
    );
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(kavlingProvider);
    final kavling = state.selectedKavling;

    return Scaffold(
      body: state.isLoading
          ? const Center(child: CircularProgressIndicator())
          : kavling == null
              ? const Center(child: Text('Kavling tidak ditemukan'))
              : CustomScrollView(
                  slivers: [
                    // App Bar with Image
                    SliverAppBar(
                      expandedHeight: 250,
                      pinned: true,
                      flexibleSpace: FlexibleSpaceBar(
                        title: Text(kavling.nama),
                        background: kavling.gambar != null
                            ? CachedNetworkImage(
                                imageUrl: kavling.gambar!,
                                fit: BoxFit.cover,
                              )
                            : Container(
                                color: AppColors.primary.withValues(alpha: 0.2),
                                child: const Icon(
                                  Icons.landscape,
                                  size: 80,
                                  color: AppColors.primary,
                                ),
                              ),
                      ),
                    ),
                    // Content
                    SliverToBoxAdapter(
                      child: Padding(
                        padding: const EdgeInsets.all(16),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            // Price & Status
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      kavling.formattedPrice,
                                      style: const TextStyle(
                                        fontSize: 24,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primary,
                                      ),
                                    ),
                                    Text(
                                      'per malam',
                                      style: TextStyle(color: AppColors.textMuted),
                                    ),
                                  ],
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 12,
                                    vertical: 6,
                                  ),
                                  decoration: BoxDecoration(
                                    color: kavling.isAvailable
                                        ? AppColors.success.withValues(alpha: 0.1)
                                        : AppColors.error.withValues(alpha: 0.1),
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    kavling.isAvailable ? 'Tersedia' : 'Penuh',
                                    style: TextStyle(
                                      fontWeight: FontWeight.w600,
                                      color: kavling.isAvailable
                                          ? AppColors.success
                                          : AppColors.error,
                                    ),
                                  ),
                                ),
                              ],
                            ),

                            const SizedBox(height: 24),

                            // Info Card
                            Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                color: Colors.grey.shade100,
                                borderRadius: BorderRadius.circular(12),
                              ),
                              child: Row(
                                mainAxisAlignment: MainAxisAlignment.spaceAround,
                                children: [
                                  _InfoItem(
                                    icon: Icons.people,
                                    label: 'Kapasitas',
                                    value: '${kavling.kapasitas} orang',
                                  ),
                                  Container(
                                    width: 1,
                                    height: 40,
                                    color: Colors.grey.shade300,
                                  ),
                                  _InfoItem(
                                    icon: Icons.check_circle,
                                    label: 'Status',
                                    value: kavling.isAvailable ? 'Tersedia' : 'Penuh',
                                  ),
                                ],
                              ),
                            ),

                            const SizedBox(height: 24),

                            // Fasilitas
                            if (kavling.fasilitas.isNotEmpty) ...[
                              const Text(
                                'Fasilitas',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 12),
                              Wrap(
                                spacing: 8,
                                runSpacing: 8,
                                children: kavling.fasilitas.map((f) {
                                  return Chip(
                                    label: Text(f),
                                    backgroundColor: AppColors.primary.withValues(alpha: 0.1),
                                  );
                                }).toList(),
                              ),
                              const SizedBox(height: 24),
                            ],

                            // Deskripsi
                            if (kavling.deskripsi != null) ...[
                              const Text(
                                'Deskripsi',
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                              const SizedBox(height: 8),
                              Text(
                                kavling.deskripsi!,
                                style: TextStyle(
                                  color: AppColors.textSecondary,
                                  height: 1.5,
                                ),
                              ),
                            ],

                            const SizedBox(height: 100),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),
      bottomNavigationBar: kavling != null && kavling.isAvailable
          ? Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 10,
                    offset: const Offset(0, -5),
                  ),
                ],
              ),
              child: ElevatedButton(
                onPressed: () {
                  // Navigate to booking flow with kavling ID
                  context.push('/booking/new?kavling_id=${kavling.id}');
                },
                child: const Text('Book Sekarang'),
              ),
            )
          : null,
    );
  }
}

class _InfoItem extends StatelessWidget {
  final IconData icon;
  final String label;
  final String value;

  const _InfoItem({
    required this.icon,
    required this.label,
    required this.value,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        Icon(icon, color: AppColors.primary),
        const SizedBox(height: 4),
        Text(
          label,
          style: TextStyle(
            fontSize: 12,
            color: AppColors.textMuted,
          ),
        ),
        const SizedBox(height: 2),
        Text(
          value,
          style: const TextStyle(fontWeight: FontWeight.bold),
        ),
      ],
    );
  }
}
