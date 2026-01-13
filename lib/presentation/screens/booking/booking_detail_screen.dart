import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import 'package:image_picker/image_picker.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:flutter_animate/flutter_animate.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/booking_provider.dart';

class BookingDetailScreen extends ConsumerStatefulWidget {
  final int bookingId;

  const BookingDetailScreen({super.key, required this.bookingId});

  @override
  ConsumerState<BookingDetailScreen> createState() =>
      _BookingDetailScreenState();
}

class _BookingDetailScreenState extends ConsumerState<BookingDetailScreen> {
  Timer? _pollingTimer;

  @override
  void initState() {
    super.initState();
    Future.microtask(
      () => ref
          .read(bookingProvider.notifier)
          .loadBookingDetail(widget.bookingId),
    );
    _startPolling();
  }

  @override
  void dispose() {
    _pollingTimer?.cancel();
    super.dispose();
  }

  void _startPolling() {
    // Poll every 3 seconds to check for status updates (e.g. Scanned by Admin)
    _pollingTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      ref.read(bookingProvider.notifier).loadBookingDetail(widget.bookingId, silent: true);
    });
  }

  void _showStatusChangeDialog(String status) {
    bool isCheckIn = status == 'checked_in';
    bool isCheckOut =
        status == 'completed'; // Assuming completed means checked out

    if (!isCheckIn && !isCheckOut) return;

    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Dialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        backgroundColor: Colors.white,
        elevation: 10,
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Animated Icon
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: (isCheckIn ? Colors.green : Colors.blue).withValues(
                    alpha: 0.1,
                  ),
                  shape: BoxShape.circle,
                ),
                child:
                    Icon(
                          isCheckIn
                              ? Icons.check_circle_rounded
                              : Icons.handshake_rounded,
                          size: 64,
                          color: isCheckIn ? Colors.green : Colors.blue,
                        )
                        .animate(
                          onPlay: (controller) =>
                              controller.repeat(reverse: true),
                        )
                        .scale(
                          begin: const Offset(0.8, 0.8),
                          end: const Offset(1.2, 1.2),
                          duration: 800.ms,
                        ),
              ),
              const SizedBox(height: 20),

              // Title
              Text(
                isCheckIn ? 'Check-in Berhasil!' : 'Terima Kasih!',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 22,
                  fontWeight: FontWeight.bold,
                  color: AppColors.textPrimary,
                ),
              ).animate().fadeIn().slideY(begin: 0.5, end: 0),

              const SizedBox(height: 12),

              // Message
              Text(
                isCheckIn
                    ? 'Selamat datang di LuhurCamp! Selamat menikmati liburan Anda.'
                    : 'Check-out berhasil. Sampai jumpa di petualangan berikutnya!',
                textAlign: TextAlign.center,
                style: const TextStyle(
                  fontSize: 16,
                  color: AppColors.textSecondary,
                ),
              ),

              const SizedBox(height: 24),

              // Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.pop(context),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: isCheckIn
                        ? AppColors.primary
                        : Colors.blue,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                  ),
                  child: const Text(
                    'OK, Mantap!',
                    style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                  ),
                ),
              ).animate().fadeIn(delay: 400.ms),
            ],
          ),
        ),
      ),
    );
  }

  Future<void> _uploadPayment() async {
    final picker = ImagePicker();
    final image = await picker.pickImage(source: ImageSource.gallery);

    if (image == null) return;

    final result = await ref
        .read(bookingProvider.notifier)
        .uploadPayment(widget.bookingId, image.path);

    if (!mounted) return;

    if (result.isSuccess) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Bukti pembayaran berhasil diupload'),
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

  Future<void> _cancelBooking() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Batalkan Booking?'),
        content: const Text('Apakah Anda yakin ingin membatalkan booking ini?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Tidak'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(context, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Ya, Batalkan'),
          ),
        ],
      ),
    );

    if (confirm != true) return;

    final result = await ref
        .read(bookingProvider.notifier)
        .cancelBooking(widget.bookingId);

    if (!mounted) return;

    if (result.isSuccess) {
      ScaffoldMessenger.of(
        context,
      ).showSnackBar(const SnackBar(content: Text('Booking dibatalkan')));
      context.go('/my-bookings');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Gagal membatalkan'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    // Listen for status changes
    ref.listen(bookingProvider, (previous, next) {
      if (previous?.selectedBooking?.status != next.selectedBooking?.status) {
        final newStatus = next.selectedBooking?.status;
        if (newStatus != null) {
          _showStatusChangeDialog(newStatus);
        }
      }
    });

    final state = ref.watch(bookingProvider);
    final booking = state.selectedBooking;

    if (state.isLoading && booking == null) {
      return const Scaffold(body: Center(child: CircularProgressIndicator()));
    }

    if (booking == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Detail Booking')),
        body: const Center(child: Text('Booking tidak ditemukan')),
      );
    }

    // Determine if QR Code should be shown
    final showQr = [
      'confirmed',
      'checked_in',
      'completed',
    ].contains(booking.status);

    return Scaffold(
      body: DefaultTabController(
        length: showQr ? 2 : 1,
        child: NestedScrollView(
          headerSliverBuilder: (context, innerBoxIsScrolled) {
            return [
              SliverAppBar(
                expandedHeight: 200,
                pinned: true,
                flexibleSpace: FlexibleSpaceBar(
                  background: Container(
                    decoration: const BoxDecoration(
                      gradient: LinearGradient(
                        colors: [AppColors.primaryDark, AppColors.primary],
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                      ),
                    ),
                    child: SafeArea(
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.white.withValues(alpha: 0.2),
                              shape: BoxShape.circle,
                            ),
                            child: const Icon(
                              Icons.holiday_village_rounded,
                              size: 40,
                              color: Colors.white,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            booking.code,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 24,
                              fontWeight: FontWeight.bold,
                              letterSpacing: 1,
                            ),
                          ),
                          Text(
                            DateFormat(
                              'dd MMMM yyyy',
                              'id_ID',
                            ).format(booking.tanggalCheckIn),
                            style: TextStyle(
                              color: Colors.white.withValues(alpha: 0.8),
                              fontSize: 14,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                leading: IconButton(
                  icon: const Icon(
                    Icons.arrow_back_ios_new,
                    color: Colors.white,
                  ),
                  onPressed: () {
                     if (context.canPop()) {
                       context.pop();
                     } else {
                       context.go('/my-bookings'); // Better fallback than home
                     }
                  },
                ),
                title: innerBoxIsScrolled ? Text(booking.code) : null,
                centerTitle: true,
                backgroundColor: AppColors.primary,
              ),
              if (showQr)
                SliverPersistentHeader(
                  delegate: _SliverTabBarDelegate(
                    const TabBar(
                      labelColor: AppColors.primary,
                      unselectedLabelColor: Colors.grey,
                      indicatorColor: AppColors.primary,
                      indicatorWeight: 3,
                      tabs: [
                        Tab(
                          text: 'Detail Booking',
                          icon: Icon(Icons.info_outline),
                        ),
                        Tab(text: 'Tiket & QR', icon: Icon(Icons.qr_code_2)),
                      ],
                    ),
                  ),
                  pinned: true,
                ),
            ];
          },
          body: TabBarView(
            children: [
              // Tab 1: Detail
              SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Status Card with Premium Look
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(vertical: 32, horizontal: 24), // Increased vertical padding for balance
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: _getStatusColor(
                              booking.status,
                            ).withValues(alpha: 0.1),
                            blurRadius: 20,
                            offset: const Offset(0, 10),
                          ),
                        ],
                        border: Border.all(
                          color: _getStatusColor(
                            booking.status,
                          ).withValues(alpha: 0.2),
                          width: 1,
                        ),
                      ),
                      child: Column(
                        mainAxisAlignment: MainAxisAlignment.center, // Center vertically
                        children: [
                          Container(
                                padding: const EdgeInsets.all(16),
                                decoration: BoxDecoration(
                                  color: _getStatusColor(
                                    booking.status,
                                  ).withValues(alpha: 0.1),
                                  shape: BoxShape.circle,
                                ),
                                child: Icon(
                                  _getStatusIcon(booking.status),
                                  size: 48, // Slightly larger
                                  color: _getStatusColor(booking.status),
                                ),
                              )
                              .animate(
                                target: booking.status == 'checked_in' ? 1 : 0,
                              )
                              .scale(
                                duration: 400.ms,
                                curve: Curves.elasticOut,
                              ),
                          const SizedBox(height: 16),
                          Text(
                            booking.statusLabel,
                            style: TextStyle(
                              fontSize: 20,
                              fontWeight: FontWeight.bold,
                              color: _getStatusColor(booking.status),
                            ),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            _getStatusDescription(booking.status),
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: AppColors.textSecondary,
                              fontSize: 13,
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 24),

                    // Booking Info Section
                    _SectionHeader(
                      title: 'Informasi Pesanan',
                      icon: Icons.calendar_today_rounded,
                    ),
                    const SizedBox(height: 16),
                    _DetailCard(
                      children: [
                        _InfoRow(
                          label: 'Check In',
                          value: DateFormat(
                            'EEEE, dd MMMM yyyy',
                            'id_ID',
                          ).format(booking.tanggalCheckIn),
                        ),
                        const Divider(height: 24),
                        _InfoRow(
                          label: 'Check Out',
                          value: DateFormat(
                            'EEEE, dd MMMM yyyy',
                            'id_ID',
                          ).format(booking.tanggalCheckOut),
                        ),
                        const Divider(height: 24),
                        _InfoRow(
                          label: 'Durasi',
                          value: '${booking.totalNights} Malam',
                        ),
                        const Divider(height: 24),
                        _InfoRow(
                          label: 'Tamu',
                          value:
                              '${booking.kavling?.kapasitas ?? 2} Orang (Maks)',
                        ),
                      ],
                    ),

                    const SizedBox(height: 24),

                    // Kavling Info
                    if (booking.kavling != null) ...[
                      _SectionHeader(
                        title: 'Lokasi Kavling',
                        icon: Icons.map_rounded,
                      ),
                      const SizedBox(height: 16),
                      Container(
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.05),
                              blurRadius: 10,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: ListTile(
                          contentPadding: const EdgeInsets.all(16),
                          leading: ClipRRect(
                            borderRadius: BorderRadius.circular(12),
                            child: Container(
                              width: 60,
                              height: 60,
                              color: AppColors.primary.withValues(alpha: 0.1),
                              child: const Icon(
                                Icons.landscape_rounded,
                                color: AppColors.primary,
                                size: 32,
                              ),
                            ),
                          ),
                          title: Text(
                            booking.kavling!.nama,
                            style: const TextStyle(fontWeight: FontWeight.bold),
                          ),
                          subtitle: Text(
                            'Kapasitas ${booking.kavling!.kapasitas} orang',
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],

                    // Equipment
                    if (booking.items.isNotEmpty) ...[
                      _SectionHeader(
                        title: 'Peralatan Tambahan',
                        icon: Icons.backpack_rounded,
                      ),
                      const SizedBox(height: 16),
                      ...booking.items.map(
                        (item) => Padding(
                          padding: const EdgeInsets.only(bottom: 12),
                          child: Container(
                            decoration: BoxDecoration(
                              color: Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.grey.shade200),
                            ),
                            child: ListTile(
                              leading: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: AppColors.secondary.withValues(
                                    alpha: 0.1,
                                  ),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: const Icon(
                                  Icons.inventory_2_outlined,
                                  color: AppColors.secondary,
                                  size: 20,
                                ),
                              ),
                              title: Text(
                                item.peralatan?.nama ?? 'Item',
                                style: const TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                              trailing: Text(
                                '${item.jumlah}x',
                                style: const TextStyle(
                                  fontWeight: FontWeight.bold,
                                ),
                              ),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(height: 24),
                    ],

                    // Payment Summary
                    Container(
                      padding: const EdgeInsets.all(20),
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [AppColors.primary, AppColors.primaryLight],
                          begin: Alignment.topLeft,
                          end: Alignment.bottomRight,
                        ),
                        borderRadius: BorderRadius.circular(20),
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.primary.withValues(alpha: 0.3),
                            blurRadius: 12,
                            offset: const Offset(0, 6),
                          ),
                        ],
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          const Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'Total Pembayaran',
                                style: TextStyle(
                                  color: Colors.white70,
                                  fontSize: 13,
                                ),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Grand Total',
                                style: TextStyle(
                                  color: Colors.white,
                                  fontWeight: FontWeight.bold,
                                  fontSize: 16,
                                ),
                              ),
                            ],
                          ),
                          Text(
                            booking.formattedTotal,
                            style: const TextStyle(
                              fontSize: 22,
                              fontWeight: FontWeight.bold,
                              color: Colors.white,
                            ),
                          ),
                        ],
                      ),
                    ),

                    const SizedBox(height: 32),

                    // Actions
                    if (booking.canUploadPayment)
                      ElevatedButton.icon(
                        onPressed: _uploadPayment,
                        icon: const Icon(Icons.upload_file_rounded),
                        label: const Text('Upload Bukti Pembayaran'),
                        style: ElevatedButton.styleFrom(
                          minimumSize: const Size(double.infinity, 54),
                          backgroundColor: Colors.white,
                          foregroundColor: AppColors.primary,
                          elevation: 2,
                          side: const BorderSide(color: AppColors.primary),
                        ),
                      ).animate().fadeIn(delay: 200.ms),

                    if (booking.canCancel) ...[
                      const SizedBox(height: 16),
                      SizedBox(
                        width: double.infinity,
                        child: TextButton.icon(
                          onPressed: _cancelBooking,
                          icon: const Icon(Icons.cancel_outlined, size: 20),
                          label: const Text('Batalkan Pesanan'),
                          style: TextButton.styleFrom(
                            foregroundColor: AppColors.error,
                          ),
                        ),
                      ),
                    ],
                    const SizedBox(height: 40),
                  ],
                ),
              ),

              // Tab 2: QR Code
              if (showQr)
                Center(
                  child: SingleChildScrollView(
                    padding: const EdgeInsets.all(20),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Container(
                          padding: const EdgeInsets.all(32),
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(30),
                            boxShadow: [
                              BoxShadow(
                                color: AppColors.primary.withValues(alpha: 0.1),
                                blurRadius: 30,
                                offset: const Offset(0, 15),
                              ),
                            ],
                          ),
                          child: Column(
                            children: [
                              QrImageView(
                                data: booking.code,
                                version: QrVersions.auto,
                                size: 260.0,
                                eyeStyle: const QrEyeStyle(
                                  eyeShape: QrEyeShape.square,
                                  color: AppColors.primary,
                                ),
                                dataModuleStyle: const QrDataModuleStyle(
                                  dataModuleShape: QrDataModuleShape.circle,
                                  color: AppColors.primary,
                                ),
                              ),
                              const SizedBox(height: 20),
                              Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 20,
                                  vertical: 8,
                                ),
                                decoration: BoxDecoration(
                                  color: AppColors.background,
                                  borderRadius: BorderRadius.circular(12),
                                ),
                                child: Row(
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    const Icon(
                                      Icons.qr_code,
                                      size: 18,
                                      color: AppColors.textSecondary,
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      booking.code,
                                      style: const TextStyle(
                                        fontFamily: 'Monospace',
                                        fontSize: 18,
                                        fontWeight: FontWeight.bold,
                                        letterSpacing: 1,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ).animate().scale(
                          duration: 400.ms,
                          curve: Curves.easeOutBack,
                        ),
                        const SizedBox(height: 32),
                        const Text(
                          'Tunjukkan QR Code ini kepada petugas',
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: AppColors.textPrimary,
                          ),
                        ),
                        const SizedBox(height: 8),
                        const Text(
                          'Digunakan untuk proses Check-in saat\ntiba di lokasi perkemahan.',
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            color: AppColors.textSecondary,
                            height: 1.5,
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
            ],
          ),
        ),
      ),
    );
  }

  String _getStatusDescription(String status) {
    switch (status) {
      case 'pending':
        return 'Menunggu pembayaran';
      case 'waiting_confirmation':
        return 'Menunggu konfirmasi admin';
      case 'confirmed':
        return 'Booking dikonfirmasi. Siap untuk Check-in!';
      case 'checked_in':
        return 'Selamat menimati waktu camping Anda!';
      case 'completed':
        return 'Terima kasih telah berkunjung.';
      case 'cancelled':
        return 'Booking telah dibatalkan.';
      default:
        return 'Status booking saat ini.';
    }
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'pending':
      case 'waiting_confirmation':
        return AppColors.warning;
      case 'confirmed':
        return AppColors.success;
      case 'checked_in':
        return Colors.purple;
      case 'completed':
        return AppColors.info;
      case 'cancelled':
      case 'rejected':
        return AppColors.error;
      default:
        return AppColors.textMuted;
    }
  }

  IconData _getStatusIcon(String status) {
    switch (status) {
      case 'pending':
      case 'waiting_confirmation':
        return Icons.hourglass_top_rounded;
      case 'confirmed':
        return Icons.check_circle_rounded;
      case 'checked_in':
        return Icons.verified_user_rounded;
      case 'completed':
        return Icons.task_alt_rounded;
      case 'cancelled':
      case 'rejected':
        return Icons.cancel_rounded;
      default:
        return Icons.info_rounded;
    }
  }
}

class _SliverTabBarDelegate extends SliverPersistentHeaderDelegate {
  final TabBar _tabBar;

  _SliverTabBarDelegate(this._tabBar);

  @override
  double get minExtent => _tabBar.preferredSize.height;
  @override
  double get maxExtent => _tabBar.preferredSize.height;

  @override
  Widget build(
    BuildContext context,
    double shrinkOffset,
    bool overlapsContent,
  ) {
    return Container(color: Colors.white, child: _tabBar);
  }

  @override
  bool shouldRebuild(_SliverTabBarDelegate oldDelegate) {
    return false;
  }
}

class _SectionHeader extends StatelessWidget {
  final String title;
  final IconData icon;

  const _SectionHeader({required this.title, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Icon(icon, size: 20, color: AppColors.primary),
        const SizedBox(width: 8),
        Text(
          title,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: AppColors.textPrimary,
          ),
        ),
      ],
    );
  }
}

class _DetailCard extends StatelessWidget {
  final List<Widget> children;

  const _DetailCard({required this.children});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(children: children),
    );
  }
}

class _InfoRow extends StatelessWidget {
  final String label;
  final String value;

  const _InfoRow({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(label, style: const TextStyle(color: AppColors.textSecondary)),
        Text(
          value,
          style: const TextStyle(
            fontWeight: FontWeight.w600,
            color: AppColors.textPrimary,
          ),
        ),
      ],
    );
  }
}
