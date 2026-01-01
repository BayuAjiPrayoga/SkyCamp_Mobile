import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/peralatan_provider.dart';
import '../../providers/booking_provider.dart';
import '../../../data/models/peralatan_model.dart';

class PeralatanListScreen extends ConsumerStatefulWidget {
  const PeralatanListScreen({super.key});

  @override
  ConsumerState<PeralatanListScreen> createState() => _PeralatanListScreenState();
}

class _PeralatanListScreenState extends ConsumerState<PeralatanListScreen> {
  @override
  void initState() {
    super.initState();
    Future.microtask(() => ref.read(peralatanProvider.notifier).loadPeralatan());
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(peralatanProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Sewa Peralatan'),
      ),
      body: Column(
        children: [
          // Category Filter
          if (state.categories.length > 1)
            Container(
              height: 50,
              padding: const EdgeInsets.symmetric(vertical: 8),
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 12),
                itemCount: state.categories.length,
                itemBuilder: (context, index) {
                  final category = state.categories[index];
                  final isSelected = state.selectedCategory == category ||
                      (state.selectedCategory == null && category == 'Semua');
                  
                  return Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 4),
                    child: ChoiceChip(
                      label: Text(category),
                      selected: isSelected,
                      selectedColor: AppColors.primary,
                      labelStyle: TextStyle(
                        color: isSelected ? Colors.white : AppColors.textPrimary,
                      ),
                      onSelected: (_) {
                        ref.read(peralatanProvider.notifier).setCategory(
                          category == 'Semua' ? null : category,
                        );
                      },
                    ),
                  );
                },
              ),
            ),
          // List
          Expanded(child: _buildBody(state)),
        ],
      ),
    );
  }

  Widget _buildBody(PeralatanState state) {
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
            Text('Gagal memuat data'),
            const SizedBox(height: 8),
            ElevatedButton(
              onPressed: () => ref.read(peralatanProvider.notifier).loadPeralatan(),
              child: const Text('Coba Lagi'),
            ),
          ],
        ),
      );
    }

    final list = state.filteredList;

    if (list.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.backpack_outlined, size: 64, color: AppColors.textMuted),
            const SizedBox(height: 16),
            Text('Tidak ada peralatan'),
          ],
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: () => ref.read(peralatanProvider.notifier).loadPeralatan(),
      child: GridView.builder(
        padding: const EdgeInsets.all(16),
        gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
          crossAxisCount: 2,
          mainAxisSpacing: 16,
          crossAxisSpacing: 16,
          childAspectRatio: 0.75,
        ),
        itemCount: list.length,
        itemBuilder: (context, index) {
          final item = list[index];
          return _PeralatanCard(
            peralatan: item,
            onTap: () {
              // Add to booking cart and go back
              ref.read(bookingProvider.notifier).addToCart(item);
              ScaffoldMessenger.of(context).showSnackBar(
                SnackBar(
                  content: Text('${item.nama} ditambahkan ke keranjang'),
                  duration: const Duration(seconds: 2),
                ),
              );
              context.pop();
            },
          );
        },
      ),
    );
  }
}

class _PeralatanCard extends StatelessWidget {
  final Peralatan peralatan;
  final VoidCallback onTap;

  const _PeralatanCard({required this.peralatan, required this.onTap});

  @override
  Widget build(BuildContext context) {
    return Card(
      clipBehavior: Clip.antiAlias,
      child: InkWell(
        onTap: onTap,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Image
            Expanded(
              flex: 3,
              child: Container(
                width: double.infinity,
                color: AppColors.secondary.withValues(alpha: 0.1),
                child: peralatan.gambar != null
                    ? CachedNetworkImage(
                        imageUrl: peralatan.gambar!,
                        fit: BoxFit.cover,
                        placeholder: (context, url) => const Center(
                          child: CircularProgressIndicator(),
                        ),
                        errorWidget: (context, url, error) => const Icon(
                          Icons.backpack,
                          size: 40,
                          color: AppColors.secondary,
                        ),
                      )
                    : const Center(
                        child: Icon(
                          Icons.backpack,
                          size: 40,
                          color: AppColors.secondary,
                        ),
                      ),
              ),
            ),
            // Content
            Expanded(
              flex: 2,
              child: Padding(
                padding: const EdgeInsets.all(12),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      peralatan.nama,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 14,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Stok: ${peralatan.stokTotal}',
                      style: TextStyle(
                        fontSize: 12,
                        color: peralatan.isAvailable
                            ? AppColors.textSecondary
                            : AppColors.error,
                      ),
                    ),
                    const Spacer(),
                    Text(
                      peralatan.formattedPrice,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        color: AppColors.primary,
                      ),
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
