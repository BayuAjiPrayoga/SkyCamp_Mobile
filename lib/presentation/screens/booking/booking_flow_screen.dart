import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:intl/intl.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/booking_provider.dart';
import '../../providers/kavling_provider.dart';

class BookingFlowScreen extends ConsumerStatefulWidget {
  final int? kavlingId;

  const BookingFlowScreen({super.key, this.kavlingId});

  @override
  ConsumerState<BookingFlowScreen> createState() => _BookingFlowScreenState();
}

class _BookingFlowScreenState extends ConsumerState<BookingFlowScreen> {
  DateTime? _checkIn;
  DateTime? _checkOut;

  @override
  void initState() {
    super.initState();
    // Load kavling if id provided
    if (widget.kavlingId != null) {
      Future.microtask(() async {
        await ref.read(kavlingProvider.notifier).loadKavlingDetail(widget.kavlingId!);
        final kavling = ref.read(kavlingProvider).selectedKavling;
        if (kavling != null) {
          ref.read(bookingProvider.notifier).selectKavling(kavling);
        }
      });
    }
  }

  Future<void> _selectCheckIn() async {
    final date = await showDatePicker(
      context: context,
      initialDate: DateTime.now().add(const Duration(days: 1)),
      firstDate: DateTime.now(),
      lastDate: DateTime.now().add(const Duration(days: 365)),
    );
    if (date != null) {
      setState(() {
        _checkIn = date;
        if (_checkOut != null && _checkOut!.isBefore(date)) {
          _checkOut = null;
        }
      });
      _updateDates();
    }
  }

  Future<void> _selectCheckOut() async {
    if (_checkIn == null) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Pilih tanggal check-in terlebih dahulu')),
      );
      return;
    }

    final date = await showDatePicker(
      context: context,
      initialDate: _checkIn!.add(const Duration(days: 1)),
      firstDate: _checkIn!.add(const Duration(days: 1)),
      lastDate: _checkIn!.add(const Duration(days: 30)),
    );
    if (date != null) {
      setState(() => _checkOut = date);
      _updateDates();
    }
  }

  void _updateDates() {
    if (_checkIn != null && _checkOut != null) {
      ref.read(bookingProvider.notifier).setDates(_checkIn!, _checkOut!);
    }
  }

  Future<void> _submitBooking() async {
    final result = await ref.read(bookingProvider.notifier).submitBooking();
    
    if (!mounted) return;
    
    if (result.isSuccess) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Booking berhasil! Silakan upload bukti pembayaran.'),
          backgroundColor: AppColors.success,
        ),
      );
      context.go('/my-bookings');
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Booking gagal'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final bookingState = ref.watch(bookingProvider);
    final kavling = bookingState.selectedKavling;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Buat Booking'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Selected Kavling
            if (kavling != null) ...[
              const Text(
                'Kavling Dipilih',
                style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 8),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: AppColors.primary.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: AppColors.primary.withValues(alpha: 0.3)),
                ),
                child: Row(
                  children: [
                    Icon(Icons.home_work, color: AppColors.primary, size: 32),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            kavling.nama,
                            style: const TextStyle(
                              fontWeight: FontWeight.bold,
                              fontSize: 16,
                            ),
                          ),
                          Text(
                            '${kavling.formattedPrice}/malam',
                            style: TextStyle(color: AppColors.primary),
                          ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),
            ],

            // Date Selection
            const Text(
              'Pilih Tanggal',
              style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 12),
            Row(
              children: [
                Expanded(
                  child: _DateCard(
                    label: 'Check In',
                    date: _checkIn,
                    onTap: _selectCheckIn,
                  ),
                ),
                const SizedBox(width: 12),
                Expanded(
                  child: _DateCard(
                    label: 'Check Out',
                    date: _checkOut,
                    onTap: _selectCheckOut,
                  ),
                ),
              ],
            ),

            if (bookingState.totalNights > 0) ...[
              const SizedBox(height: 8),
              Text(
                '${bookingState.totalNights} malam',
                style: TextStyle(color: AppColors.textSecondary),
              ),
            ],

            const SizedBox(height: 24),

            // Equipment Cart
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Sewa Peralatan (Opsional)',
                  style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
                ),
                TextButton.icon(
                  onPressed: () => context.push('/peralatan'),
                  icon: const Icon(Icons.add, size: 18),
                  label: const Text('Tambah'),
                ),
              ],
            ),

            if (bookingState.cart.isEmpty)
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: Colors.grey.shade100,
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Row(
                  children: [
                    Icon(Icons.info_outline, color: AppColors.textMuted),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        'Belum ada peralatan ditambahkan',
                        style: TextStyle(color: AppColors.textSecondary),
                      ),
                    ),
                  ],
                ),
              )
            else
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: bookingState.cart.length,
                itemBuilder: (context, index) {
                  final item = bookingState.cart[index];
                  return Card(
                    child: ListTile(
                      leading: Container(
                        width: 48,
                        height: 48,
                        decoration: BoxDecoration(
                          color: AppColors.secondary.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: const Icon(Icons.backpack, color: AppColors.secondary),
                      ),
                      title: Text(item.peralatan.nama),
                      subtitle: Text(item.peralatan.formattedPrice),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.remove_circle_outline),
                            onPressed: () {
                              ref.read(bookingProvider.notifier).updateCartQuantity(
                                item.peralatan.id,
                                item.quantity - 1,
                              );
                            },
                          ),
                          Text('${item.quantity}'),
                          IconButton(
                            icon: const Icon(Icons.add_circle_outline),
                            onPressed: () {
                              ref.read(bookingProvider.notifier).updateCartQuantity(
                                item.peralatan.id,
                                item.quantity + 1,
                              );
                            },
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),

            const SizedBox(height: 100),
          ],
        ),
      ),
      bottomNavigationBar: Container(
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
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Price Summary
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text('Total'),
                Text(
                  'Rp ${NumberFormat('#,###', 'id_ID').format(bookingState.grandTotal)}',
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: AppColors.primary,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: bookingState.canSubmit && !bookingState.isLoading
                    ? _submitBooking
                    : null,
                child: bookingState.isLoading
                    ? const SizedBox(
                        height: 20,
                        width: 20,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text('Konfirmasi Booking'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _DateCard extends StatelessWidget {
  final String label;
  final DateTime? date;
  final VoidCallback onTap;

  const _DateCard({
    required this.label,
    required this.date,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          border: Border.all(color: Colors.grey.shade300),
          borderRadius: BorderRadius.circular(12),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: TextStyle(
                fontSize: 12,
                color: AppColors.textMuted,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              date != null
                  ? DateFormat('dd MMM yyyy').format(date!)
                  : 'Pilih tanggal',
              style: TextStyle(
                fontWeight: FontWeight.w600,
                color: date != null ? AppColors.textPrimary : AppColors.textMuted,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
