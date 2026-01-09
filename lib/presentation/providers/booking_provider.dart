import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/booking_model.dart';
import '../../data/models/kavling_model.dart';
import '../../data/models/peralatan_model.dart';
import '../../data/repositories/booking_repository.dart';

// Cart Item for equipment
class CartItem {
  final Peralatan peralatan;
  int quantity;

  CartItem({required this.peralatan, this.quantity = 1});

  int get subtotal => peralatan.hargaSewa * quantity;
}

// Booking State
class BookingState {
  final List<Booking> bookings;
  final bool isLoading;
  final String? error;
  final Booking? selectedBooking;
  
  // Booking flow
  final Kavling? selectedKavling;
  final DateTime? checkInDate;
  final DateTime? checkOutDate;
  final List<CartItem> cart;

  const BookingState({
    this.bookings = const [],
    this.isLoading = false,
    this.error,
    this.selectedBooking,
    this.selectedKavling,
    this.checkInDate,
    this.checkOutDate,
    this.cart = const [],
  });

  BookingState copyWith({
    List<Booking>? bookings,
    bool? isLoading,
    String? error,
    Booking? selectedBooking,
    Kavling? selectedKavling,
    DateTime? checkInDate,
    DateTime? checkOutDate,
    List<CartItem>? cart,
  }) {
    return BookingState(
      bookings: bookings ?? this.bookings,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      selectedBooking: selectedBooking ?? this.selectedBooking,
      selectedKavling: selectedKavling ?? this.selectedKavling,
      checkInDate: checkInDate ?? this.checkInDate,
      checkOutDate: checkOutDate ?? this.checkOutDate,
      cart: cart ?? this.cart,
    );
  }

  int get totalNights {
    if (checkInDate == null || checkOutDate == null) return 0;
    return checkOutDate!.difference(checkInDate!).inDays;
  }

  int get kavlingTotal {
    if (selectedKavling == null || totalNights == 0) return 0;
    return selectedKavling!.hargaPerMalam * totalNights;
  }

  int get equipmentTotal {
    return cart.fold(0, (sum, item) => sum + (item.subtotal * totalNights));
  }

  int get grandTotal => kavlingTotal + equipmentTotal;

  bool get canSubmit => 
      selectedKavling != null && 
      checkInDate != null && 
      checkOutDate != null &&
      totalNights > 0;
}

// Booking Notifier
class BookingNotifier extends StateNotifier<BookingState> {
  final BookingRepository _repository = bookingRepository;

  BookingNotifier() : super(const BookingState());

  // Load my bookings
  Future<void> loadBookings() async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final bookings = await _repository.getMyBookings();
      state = state.copyWith(bookings: bookings, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  // Load booking detail
  Future<void> loadBookingDetail(int id) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final booking = await _repository.getById(id);
      state = state.copyWith(selectedBooking: booking, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  // Booking flow methods
  void selectKavling(Kavling kavling) {
    state = state.copyWith(selectedKavling: kavling);
  }

  void setDates(DateTime checkIn, DateTime checkOut) {
    state = state.copyWith(checkInDate: checkIn, checkOutDate: checkOut);
  }

  void addToCart(Peralatan peralatan) {
    final existingIndex = state.cart.indexWhere(
      (item) => item.peralatan.id == peralatan.id,
    );

    if (existingIndex >= 0) {
      // Update quantity
      final updatedCart = List<CartItem>.from(state.cart);
      updatedCart[existingIndex].quantity++;
      state = state.copyWith(cart: updatedCart);
    } else {
      // Add new item
      state = state.copyWith(
        cart: [...state.cart, CartItem(peralatan: peralatan)],
      );
    }
  }

  void removeFromCart(int peralatanId) {
    state = state.copyWith(
      cart: state.cart.where((item) => item.peralatan.id != peralatanId).toList(),
    );
  }

  void updateCartQuantity(int peralatanId, int quantity) {
    if (quantity <= 0) {
      removeFromCart(peralatanId);
      return;
    }

    final updatedCart = state.cart.map((item) {
      if (item.peralatan.id == peralatanId) {
        item.quantity = quantity;
      }
      return item;
    }).toList();

    state = state.copyWith(cart: updatedCart);
  }

  void clearBookingFlow() {
    state = state.copyWith(
      selectedKavling: null,
      checkInDate: null,
      checkOutDate: null,
      cart: [],
    );
  }

  // Submit booking
  Future<BookingResult> submitBooking() async {
    if (!state.canSubmit) {
      return BookingResult.error(message: 'Lengkapi data booking terlebih dahulu');
    }

    state = state.copyWith(isLoading: true, error: null);

    final items = state.cart.map((item) => {
      'peralatan_id': item.peralatan.id,
      'qty': item.quantity,  // Laravel expects 'qty', not 'jumlah'
    }).toList();

    final result = await _repository.createBooking(
      kavlingId: state.selectedKavling!.id,
      checkIn: state.checkInDate!,
      checkOut: state.checkOutDate!,
      items: items,
    );

    if (result.isSuccess) {
      clearBookingFlow();
      await loadBookings();
    }

    state = state.copyWith(isLoading: false, error: result.message);
    return result;
  }

  // Upload payment
  Future<BookingResult> uploadPayment(int bookingId, String imagePath) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _repository.uploadPayment(bookingId, imagePath);

    if (result.isSuccess) {
      await loadBookings();
    }

    state = state.copyWith(isLoading: false, error: result.message);
    return result;
  }

  // Cancel booking
  Future<BookingResult> cancelBooking(int bookingId) async {
    state = state.copyWith(isLoading: true, error: null);

    final result = await _repository.cancelBooking(bookingId);

    if (result.isSuccess) {
      await loadBookings();
    }

    state = state.copyWith(isLoading: false, error: result.message);
    return result;
  }
}

// Provider
final bookingProvider = StateNotifierProvider<BookingNotifier, BookingState>((ref) {
  return BookingNotifier();
});
