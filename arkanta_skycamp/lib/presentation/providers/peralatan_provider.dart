import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../data/models/peralatan_model.dart';
import '../../data/repositories/peralatan_repository.dart';

// State class
class PeralatanState {
  final List<Peralatan> peralatanList;
  final bool isLoading;
  final String? error;
  final String? selectedCategory;
  final Peralatan? selectedPeralatan;

  const PeralatanState({
    this.peralatanList = const [],
    this.isLoading = false,
    this.error,
    this.selectedCategory,
    this.selectedPeralatan,
  });

  PeralatanState copyWith({
    List<Peralatan>? peralatanList,
    bool? isLoading,
    String? error,
    String? selectedCategory,
    Peralatan? selectedPeralatan,
  }) {
    return PeralatanState(
      peralatanList: peralatanList ?? this.peralatanList,
      isLoading: isLoading ?? this.isLoading,
      error: error,
      selectedCategory: selectedCategory ?? this.selectedCategory,
      selectedPeralatan: selectedPeralatan ?? this.selectedPeralatan,
    );
  }

  List<Peralatan> get filteredList {
    if (selectedCategory == null || selectedCategory == 'Semua') {
      return peralatanList;
    }
    return peralatanList
        .where((p) => p.kategori.toLowerCase() == selectedCategory!.toLowerCase())
        .toList();
  }

  List<String> get categories {
    final cats = peralatanList.map((p) => p.kategori).toSet().toList();
    cats.insert(0, 'Semua');
    return cats;
  }
}

// Notifier
class PeralatanNotifier extends StateNotifier<PeralatanState> {
  final PeralatanRepository _repository = peralatanRepository;

  PeralatanNotifier() : super(const PeralatanState());

  Future<void> loadPeralatan() async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final list = await _repository.getAll();
      state = state.copyWith(peralatanList: list, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }

  void setCategory(String? category) {
    state = state.copyWith(selectedCategory: category);
  }

  Future<void> loadPeralatanDetail(int id) async {
    state = state.copyWith(isLoading: true, error: null);
    
    try {
      final peralatan = await _repository.getById(id);
      state = state.copyWith(selectedPeralatan: peralatan, isLoading: false);
    } catch (e) {
      state = state.copyWith(isLoading: false, error: e.toString());
    }
  }
}

// Provider
final peralatanProvider = StateNotifierProvider<PeralatanNotifier, PeralatanState>((ref) {
  return PeralatanNotifier();
});
