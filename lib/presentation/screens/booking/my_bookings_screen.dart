import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/booking_provider.dart';
import '../../../data/models/booking_model.dart';

class MyBookingsScreen extends ConsumerStatefulWidget {
  const MyBookingsScreen({super.key});

  @override
  ConsumerState<MyBookingsScreen> createState() => _MyBookingsScreenState();
}

class _MyBookingsScreenState extends ConsumerState<MyBookingsScreen>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    Future.microtask(() => ref.read(bookingProvider.notifier).loadBookings());
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final state = ref.watch(bookingProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Booking Saya'),
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white.withValues(alpha: 0.6),
          indicatorWeight: 3,
          labelStyle: const TextStyle(fontWeight: FontWeight.bold),
          unselectedLabelStyle: const TextStyle(fontWeight: FontWeight.normal),
          tabs: const [
            Tab(text: 'Aktif'),
            Tab(text: 'Selesai'),
            Tab(text: 'Batal'),
          ],
        ),
      ),
      body: state.isLoading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabController,
              children: [
                _BookingList(
                  bookings: state.bookings
                      .where(
                        (b) =>
                            b.status == 'pending' ||
                            b.status == 'confirmed' ||
                            b.status == 'waiting_confirmation' ||
                            b.status == 'checked_in',
                      )
                      .toList(),
                  emptyMessage: 'Tidak ada booking aktif',
                  onRefresh: () async {
                    await ref.read(bookingProvider.notifier).loadBookings();
                  },
                ),
                _BookingList(
                  bookings: state.bookings
                      .where((b) => b.status == 'completed')
                      .toList(),
                  emptyMessage: 'Belum ada booking selesai',
                  onRefresh: () async {
                    await ref.read(bookingProvider.notifier).loadBookings();
                  },
                ),
                _BookingList(
                  bookings: state.bookings
                      .where(
                        (b) =>
                            b.status == 'cancelled' || b.status == 'rejected',
                      )
                      .toList(),
                  emptyMessage: 'Tidak ada booking dibatalkan',
                  onRefresh: () async {
                    await ref.read(bookingProvider.notifier).loadBookings();
                  },
                ),
              ],
            ),
    );
  }
}

class _BookingList extends StatelessWidget {
  final List<Booking> bookings;
  final String emptyMessage;
  final Future<void> Function()? onRefresh;

  const _BookingList({
    required this.bookings,
    required this.emptyMessage,
    this.onRefresh,
  });

  @override
  Widget build(BuildContext context) {
    if (bookings.isEmpty) {
      return RefreshIndicator(
        onRefresh: onRefresh ?? () async {},
        child: SingleChildScrollView(
          physics: const AlwaysScrollableScrollPhysics(),
          child: SizedBox(
            height: MediaQuery.of(context).size.height * 0.7,
            child: Center(
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(
                    Icons.receipt_long_outlined,
                    size: 64,
                    color: AppColors.textMuted,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    emptyMessage,
                    style: TextStyle(color: AppColors.textSecondary),
                  ),
                ],
              ),
            ),
          ),
        ),
      );
    }

    return RefreshIndicator(
      onRefresh: onRefresh ?? () async {},
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: bookings.length,
        itemBuilder: (context, index) {
          return _BookingCard(booking: bookings[index]);
        },
      ),
    );
  }
}

class _BookingCard extends ConsumerWidget {
  final Booking booking;

  const _BookingCard({required this.booking});

  Color get statusColor {
    switch (booking.status) {
      case 'pending':
        return AppColors.warning;
      case 'waiting_confirmation':
        return Colors.orange;
      case 'confirmed':
        return AppColors.success;
      case 'checked_in':
        return Colors.blue;
      case 'completed':
        return Colors.grey;
      case 'cancelled':
      case 'rejected':
        return AppColors.error;
      default:
        return AppColors.textMuted;
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return Container(
      margin: const EdgeInsets.only(bottom: 16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 20,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: () async {
            await context.push('/booking/${booking.id}');
            // Refresh list when returning from detail page
            ref.read(bookingProvider.notifier).loadBookings();
          },
          borderRadius: BorderRadius.circular(20),
          child: Padding(
            padding: const EdgeInsets.all(20),
            child: Column(
              children: [
                // Header: ID and Status
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.all(10),
                          decoration: BoxDecoration(
                            color: AppColors.primary.withValues(alpha: 0.1),
                            shape: BoxShape.circle,
                          ),
                          child: const Icon(
                            Icons.receipt_long_rounded,
                            color: AppColors.primary,
                            size: 24,
                          ),
                        ),
                        const SizedBox(width: 12),
                        Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              booking.code,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 16,
                                color: AppColors.textPrimary,
                              ),
                            ),
                            if (booking.kavling != null)
                              Text(
                                booking.kavling!.nama,
                                style: const TextStyle(
                                  color: AppColors.textSecondary,
                                  fontSize: 13,
                                ),
                              ),
                          ],
                        ),
                      ],
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: statusColor.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(30),
                        border: Border.all(
                          color: statusColor.withValues(alpha: 0.2),
                        ),
                      ),
                      child: Text(
                        booking.statusLabel,
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.bold,
                          color: statusColor,
                        ),
                      ),
                    ),
                  ],
                ),
                const Padding(
                  padding: EdgeInsets.symmetric(vertical: 16),
                  child: Divider(height: 1),
                ),
                // Body: Dates and Info
                Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Expanded(
                      flex: 3,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          _buildInfoRow(
                            Icons.calendar_today_rounded,
                            DateFormat(
                              'dd MMM yyyy',
                              'id_ID',
                            ).format(booking.tanggalCheckIn),
                            'Check-in',
                          ),
                          const SizedBox(height: 12),
                          _buildInfoRow(
                            Icons.event_busy_rounded,
                            DateFormat(
                              'dd MMM yyyy',
                              'id_ID',
                            ).format(booking.tanggalCheckOut),
                            'Check-out',
                          ),
                        ],
                      ),
                    ),
                    Container(
                      height: 50,
                      width: 1,
                      color: Colors.grey.shade200,
                      margin: const EdgeInsets.symmetric(horizontal: 16),
                    ),
                    Expanded(
                      flex: 2,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          const Text(
                            'Total Biaya',
                            style: TextStyle(
                              color: AppColors.textSecondary,
                              fontSize: 12,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            booking.formattedTotal,
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                              color: AppColors.primary,
                            ),
                          ),
                          const SizedBox(height: 4),
                          Container(
                            padding: const EdgeInsets.symmetric(
                              horizontal: 8,
                              vertical: 2,
                            ),
                            decoration: BoxDecoration(
                              color: Colors.grey.shade100,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Text(
                              '${booking.totalNights} Malam',
                              style: const TextStyle(
                                color: AppColors.textSecondary,
                                fontSize: 11,
                                fontWeight: FontWeight.w500,
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
                // Footer: Action Button
                if (booking.canUploadPayment) ...[
                  const SizedBox(height: 20),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () => context.push('/booking/${booking.id}'),
                      icon: const Icon(Icons.upload_file_rounded, size: 18),
                      label: const Text('Upload Bukti Pembayaran'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primary,
                        foregroundColor: Colors.white,
                        shadowColor: AppColors.primary.withValues(alpha: 0.3),
                        elevation: 4,
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        padding: const EdgeInsets.symmetric(vertical: 12),
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildInfoRow(IconData icon, String text, String label) {
    return Row(
      children: [
        Container(
          padding: const EdgeInsets.all(8),
          decoration: BoxDecoration(
            color: Colors.grey.shade50,
            borderRadius: BorderRadius.circular(8),
          ),
          child: Icon(icon, size: 16, color: AppColors.textMuted),
        ),
        const SizedBox(width: 12),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: const TextStyle(
                fontSize: 10,
                color: AppColors.textSecondary,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              text,
              style: const TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: AppColors.textPrimary,
              ),
            ),
          ],
        ),
      ],
    );
  }
}
