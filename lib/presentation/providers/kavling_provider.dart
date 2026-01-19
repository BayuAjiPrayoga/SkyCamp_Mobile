// Kavling Provider - State management kavling dengan caching

import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/kavling_model.dart';
import '../../data/repositories/kavling_repository.dart';

class KavlingState {
  final List<Kavling> kavlings;
  final bool isLoading;
  final String? error;
  final Kavling? selectedKavling;

  const KavlingState({this.kavlings = const [], this.isLoading = false, this.error, this.selectedKavling});

  KavlingState copyWith({List<Kavling>? kavlings, bool? isLoading, String? error, Kavling? selectedKavling}) => KavlingState(
    kavlings: kavlings ?? this.kavlings,
    isLoading: isLoading ?? this.isLoading,
    error: error,
    selectedKavling: selectedKavling ?? this.selectedKavling,
  );
}

class KavlingNotifier extends StateNotifier<KavlingState> {
  final KavlingRepository _repository = kavlingRepository;

  KavlingNotifier() : super(const KavlingState());

  DateTime? _lastFetch;
  static const _cacheDuration = Duration(minutes: 5);

  Future<void> loadKavlings({DateTime? checkIn, DateTime? checkOut, bool forceRefresh = false, bool silent = false}) async {
    // Cache check
    if (!forceRefresh && state.kavlings.isNotEmpty && _lastFetch != null &&
        DateTime.now().difference(_lastFetch!) < _cacheDuration && checkIn == null && checkOut == null) {
      return;
    }

    if (!silent) state = state.copyWith(isLoading: true, error: null);

    try {
      final kavlings = await _repository.getAll(checkIn: checkIn, checkOut: checkOut);
      _lastFetch = DateTime.now();
      state = state.copyWith(kavlings: kavlings, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  Future<void> loadKavlingDetail(int id) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final kavling = await _repository.getById(id);
      state = state.copyWith(selectedKavling: kavling, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  List<Kavling> get availableKavlings => state.kavlings.where((k) => k.isAvailable).toList();
}

final kavlingProvider = StateNotifierProvider<KavlingNotifier, KavlingState>((ref) => KavlingNotifier());
