import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:image_picker/image_picker.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../core/theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/loading_button.dart';

class ProfileScreen extends ConsumerStatefulWidget {
  const ProfileScreen({super.key});

  @override
  ConsumerState<ProfileScreen> createState() => _ProfileScreenState();
}

class _ProfileScreenState extends ConsumerState<ProfileScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nameController;
  late TextEditingController _emailController;
  late TextEditingController _phoneController;
  bool _isEditing = false;
  bool _isUploadingAvatar = false;

  @override
  void initState() {
    super.initState();
    final user = ref.read(authProvider).user;
    _nameController = TextEditingController(text: user?.name ?? '');
    _emailController = TextEditingController(text: user?.email ?? '');
    _phoneController = TextEditingController(text: user?.phone ?? '');
  }

  @override
  void dispose() {
    _nameController.dispose();
    _emailController.dispose();
    _phoneController.dispose();
    super.dispose();
  }

  void _toggleEdit() {
    setState(() => _isEditing = !_isEditing);
  }

  Future<void> _handleLogout() async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Logout?'),
        content: const Text('Apakah Anda yakin ingin keluar?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(backgroundColor: AppColors.error),
            child: const Text('Logout'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      await ref.read(authProvider.notifier).logout();
      if (mounted) {
        context.go('/login');
      }
    }
  }

  Future<void> _handleUpdateProfile() async {
    if (_formKey.currentState?.validate() ?? false) {
      final result = await ref.read(authProvider.notifier).updateProfile(
        name: _nameController.text.trim(),
        phone: _phoneController.text.trim().isNotEmpty 
            ? _phoneController.text.trim() 
            : null,
      );

      if (!mounted) return;

      if (result.isSuccess) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Profil berhasil diperbarui!'),
            backgroundColor: AppColors.success,
          ),
        );
        _toggleEdit();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(result.message ?? 'Gagal memperbarui profil'),
            backgroundColor: AppColors.error,
          ),
        );
      }
    }
  }

  Future<void> _handleAvatarUpload() async {
    // Show source selection dialog
    final source = await showModalBottomSheet<ImageSource>(
      context: context,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (ctx) => SafeArea(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text(
                'Pilih Sumber Foto',
                style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 20),
              Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _SourceOption(
                    icon: Icons.camera_alt,
                    label: 'Kamera',
                    onTap: () => Navigator.pop(ctx, ImageSource.camera),
                  ),
                  _SourceOption(
                    icon: Icons.photo_library,
                    label: 'Galeri',
                    onTap: () => Navigator.pop(ctx, ImageSource.gallery),
                  ),
                ],
              ),
              const SizedBox(height: 10),
            ],
          ),
        ),
      ),
    );

    if (source == null) return;

    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(
      source: source,
      maxWidth: 512,
      maxHeight: 512,
      imageQuality: 80,
      preferredCameraDevice: CameraDevice.front,
    );

    if (pickedFile == null) return;

    setState(() => _isUploadingAvatar = true);
    
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(content: Text('Mengupload foto...')),
    );

    final result = await ref.read(authProvider.notifier).updateAvatar(pickedFile.path);

    if (!mounted) return;
    setState(() => _isUploadingAvatar = false);

    if (result.isSuccess) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Foto profil berhasil diperbarui!'),
          backgroundColor: AppColors.success,
        ),
      );
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(result.message ?? 'Gagal upload foto'),
          backgroundColor: AppColors.error,
        ),
      );
    }
  }

  void _showChangePasswordDialog() {
    final currentPassController = TextEditingController();
    final newPassController = TextEditingController();
    final confirmPassController = TextEditingController();
    final formKey = GlobalKey<FormState>();

    showDialog(
      context: context,
      builder: (dialogContext) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Ganti Password'),
        content: Form(
          key: formKey,
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              CustomTextField(
                controller: currentPassController,
                label: 'Password Saat Ini',
                obscureText: true,
                validator: (v) => v!.isEmpty ? 'Wajib diisi' : null,
              ),
              const SizedBox(height: 12),
              CustomTextField(
                controller: newPassController,
                label: 'Password Baru',
                obscureText: true,
                validator: (v) => v!.length < 8 ? 'Min. 8 karakter' : null,
              ),
              const SizedBox(height: 12),
              CustomTextField(
                controller: confirmPassController,
                label: 'Konfirmasi Password',
                obscureText: true,
                validator: (v) {
                  if (v != newPassController.text) return 'Password tidak sama';
                  return null;
                },
              ),
            ],
          ),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(dialogContext),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () async {
              if (formKey.currentState!.validate()) {
                Navigator.pop(dialogContext);

                final result = await ref.read(authProvider.notifier).changePassword(
                  currentPassword: currentPassController.text,
                  newPassword: newPassController.text,
                  confirmPassword: confirmPassController.text,
                );

                if (!mounted) return;

                if (result.isSuccess) {
                  ScaffoldMessenger.of(context).showSnackBar(
                    const SnackBar(
                      content: Text('Password berhasil diubah!'),
                      backgroundColor: AppColors.success,
                    ),
                  );
                } else {
                  ScaffoldMessenger.of(context).showSnackBar(
                    SnackBar(
                      content: Text(result.message ?? 'Gagal mengubah password'),
                      backgroundColor: AppColors.error,
                    ),
                  );
                }
              }
            },
            child: const Text('Simpan'),
          ),
        ],
      ),
    );
  }

  Future<void> _handleHelp() async {
    final uri = Uri.parse('https://wa.me/6281234567890?text=Halo%20Admin%20LuhurCamp,%20saya%20butuh%20bantuan');
    if (!await launchUrl(uri)) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Tidak dapat membuka WhatsApp')),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final user = authState.user;

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          // Modern Header with Gradient
          SliverAppBar(
            expandedHeight: 200,
            pinned: true,
            backgroundColor: AppColors.primary,
            flexibleSpace: FlexibleSpaceBar(
              background: Container(
                decoration: const BoxDecoration(
                  gradient: LinearGradient(
                    colors: [AppColors.primaryDark, AppColors.primary, AppColors.primaryLight],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                ),
                child: SafeArea(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const SizedBox(height: 20),
                      // Avatar with upload button
                      Stack(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(4),
                            decoration: BoxDecoration(
                              shape: BoxShape.circle,
                              border: Border.all(color: Colors.white, width: 3),
                            ),
                            child: user?.avatar != null && user!.avatar!.isNotEmpty
                                ? CircleAvatar(
                                    radius: 45,
                                    backgroundImage: CachedNetworkImageProvider(user.avatar!),
                                    backgroundColor: Colors.white,
                                  )
                                : CircleAvatar(
                                    radius: 45,
                                    backgroundColor: Colors.white,
                                    child: Text(
                                      user?.name.isNotEmpty == true 
                                          ? user!.name[0].toUpperCase() 
                                          : 'U',
                                      style: const TextStyle(
                                        fontSize: 36,
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primary,
                                      ),
                                    ),
                                  ),
                          ),
                          Positioned(
                            bottom: 0,
                            right: 0,
                            child: GestureDetector(
                              onTap: _isUploadingAvatar ? null : _handleAvatarUpload,
                              child: Container(
                                padding: const EdgeInsets.all(8),
                                decoration: BoxDecoration(
                                  color: Colors.white,
                                  shape: BoxShape.circle,
                                  boxShadow: [
                                    BoxShadow(
                                      color: Colors.black.withValues(alpha: 0.2),
                                      blurRadius: 4,
                                    ),
                                  ],
                                ),
                                child: _isUploadingAvatar
                                    ? const SizedBox(
                                        width: 20,
                                        height: 20,
                                        child: CircularProgressIndicator(strokeWidth: 2),
                                      )
                                    : const Icon(
                                        Icons.camera_alt,
                                        color: AppColors.primary,
                                        size: 20,
                                      ),
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      Text(
                        user?.name ?? 'User',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 20,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      Text(
                        user?.email ?? '',
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
            actions: [
              IconButton(
                icon: Icon(_isEditing ? Icons.close : Icons.edit, color: Colors.white),
                onPressed: _toggleEdit,
              ),
            ],
          ),

          // Content
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Profile Form Card
                  Container(
                    padding: const EdgeInsets.all(20),
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
                    child: Form(
                      key: _formKey,
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Informasi Profil',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 16),
                          CustomTextField(
                            controller: _nameController,
                            label: 'Nama Lengkap',
                            hint: 'Masukkan nama',
                            prefixIcon: Icons.person_outline,
                            enabled: _isEditing,
                          ),
                          const SizedBox(height: 16),
                          CustomTextField(
                            controller: _emailController,
                            label: 'Email',
                            hint: 'Masukkan email',
                            prefixIcon: Icons.email_outlined,
                            enabled: false,
                            keyboardType: TextInputType.emailAddress,
                          ),
                          const SizedBox(height: 16),
                          CustomTextField(
                            controller: _phoneController,
                            label: 'No. Telepon',
                            hint: '08xxxxxxxxxx',
                            prefixIcon: Icons.phone_outlined,
                            enabled: _isEditing,
                            keyboardType: TextInputType.phone,
                          ),
                          if (_isEditing) ...[
                            const SizedBox(height: 20),
                            LoadingButton(
                              onPressed: _handleUpdateProfile,
                              isLoading: authState.status == AuthStatus.loading,
                              child: const Text('Simpan Perubahan'),
                            ),
                          ],
                        ],
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Menu Section
                  const Text(
                    'Menu',
                    style: TextStyle(
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 12),
                  
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
                    child: Column(
                      children: [
                        _ModernMenuItem(
                          icon: Icons.receipt_long,
                          iconColor: AppColors.secondary,
                          title: 'Riwayat Booking',
                          subtitle: 'Lihat semua pesanan Anda',
                          onTap: () => context.push('/my-bookings'),
                        ),
                        const Divider(height: 1, indent: 56),
                        _ModernMenuItem(
                          icon: Icons.photo_library,
                          iconColor: Colors.purple,
                          title: 'Galeri Saya',
                          subtitle: 'Foto-foto camping Anda',
                          onTap: () => context.push('/gallery'),
                        ),
                        if (user?.authProvider != 'google') ...[
                          const Divider(height: 1, indent: 56),
                          _ModernMenuItem(
                            icon: Icons.lock_outline,
                            iconColor: Colors.orange,
                            title: 'Ganti Password',
                            subtitle: 'Ubah password akun',
                            onTap: _showChangePasswordDialog,
                          ),
                        ],
                        const Divider(height: 1, indent: 56),
                        _ModernMenuItem(
                          icon: Icons.help_outline,
                          iconColor: Colors.teal,
                          title: 'Bantuan',
                          subtitle: 'Hubungi admin via WhatsApp',
                          onTap: _handleHelp,
                        ),
                        const Divider(height: 1, indent: 56),
                        _ModernMenuItem(
                          icon: Icons.info_outline,
                          iconColor: Colors.blueGrey,
                          title: 'Tentang Aplikasi',
                          subtitle: 'LuhurCamp v1.0.0',
                          onTap: () {
                            showDialog(
                              context: context,
                              builder:
                                  (context) => Dialog(
                                    shape: RoundedRectangleBorder(
                                      borderRadius: BorderRadius.circular(20),
                                    ),
                                    child: Padding(
                                      padding: const EdgeInsets.all(24),
                                      child: Column(
                                        mainAxisSize: MainAxisSize.min,
                                        children: [
                                          // Logo
                                          Container(
                                            padding: const EdgeInsets.all(16),
                                            decoration: BoxDecoration(
                                              color: AppColors.primary
                                                  .withValues(alpha: 0.1),
                                              shape: BoxShape.circle,
                                            ),
                                            child: const Icon(
                                              Icons.forest,
                                              size: 48,
                                              color: AppColors.primary,
                                            ),
                                          ),
                                          const SizedBox(height: 16),
                                          // Title & Version
                                          const Text(
                                            'LuhurCamp',
                                            style: TextStyle(
                                              fontSize: 24,
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.textPrimary,
                                            ),
                                          ),
                                          Text(
                                            'v1.0.0',
                                            style: TextStyle(
                                              fontSize: 14,
                                              color: AppColors.textMuted,
                                            ),
                                          ),
                                          const SizedBox(height: 24),
                                          // Credits Divider
                                          Row(
                                            children: [
                                              Expanded(
                                                  child: Divider(
                                                    color: Colors.grey[300],
                                                  )),
                                              Padding(
                                                padding:
                                                    const EdgeInsets.symmetric(
                                                      horizontal: 8,
                                                    ),
                                                child: Text(
                                                  'CREDITS',
                                                  style: TextStyle(
                                                    fontSize: 10,
                                                    fontWeight: FontWeight.bold,
                                                    color: AppColors.textMuted,
                                                  ),
                                                ),
                                              ),
                                              Expanded(
                                                  child: Divider(
                                                    color: Colors.grey[300],
                                                  )),
                                            ],
                                          ),
                                          const SizedBox(height: 16),
                                          // Creator Info
                                          const Text(
                                            'Developed by:',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          const Text(
                                            'Bayu Aji Prayoga',
                                            style: TextStyle(
                                              fontSize: 16,
                                              fontWeight: FontWeight.bold,
                                              color: AppColors.textPrimary,
                                            ),
                                          ),
                                          Text(
                                            'NPM: 23552011194',
                                            style: TextStyle(
                                              fontSize: 14,
                                              color: AppColors.textSecondary,
                                            ),
                                          ),
                                          Text(
                                            'TIF RP 23 CNS A',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: AppColors.textMuted,
                                              fontStyle: FontStyle.italic,
                                            ),
                                          ),
                                          const SizedBox(height: 20),
                                          // Startup Branding
                                          Container(
                                            padding: const EdgeInsets.symmetric(
                                              horizontal: 12,
                                              vertical: 6,
                                            ),
                                            decoration: BoxDecoration(
                                              color: Colors.blue[50],
                                              borderRadius:
                                                  BorderRadius.circular(8),
                                              border: Border.all(
                                                color: Colors.blue.withValues(
                                                  alpha: 0.3,
                                                ),
                                              ),
                                            ),
                                            child: const Row(
                                              mainAxisSize: MainAxisSize.min,
                                              children: [
                                                Icon(
                                                  Icons.rocket_launch,
                                                  size: 16,
                                                  color: Colors.blue,
                                                ),
                                                SizedBox(width: 8),
                                                Text(
                                                  'Powered by Krevix',
                                                  style: TextStyle(
                                                    fontSize: 12,
                                                    fontWeight: FontWeight.bold,
                                                    color: Colors.blue,
                                                  ),
                                                ),
                                              ],
                                            ),
                                          ),
                                          const SizedBox(height: 24),
                                          // Actions
                                          Row(
                                            mainAxisAlignment:
                                                MainAxisAlignment.spaceEvenly,
                                            children: [
                                              TextButton(
                                                onPressed: () {
                                                  showLicensePage(
                                                    context: context,
                                                    applicationName:
                                                        'LuhurCamp',
                                                    applicationVersion:
                                                        '1.0.0',
                                                    applicationLegalese:
                                                        '© 2025 Bayu Aji Prayoga (Krevix)',
                                                  );
                                                },
                                                child: const Text('License'),
                                              ),
                                              TextButton(
                                                onPressed:
                                                    () => Navigator.pop(
                                                      context,
                                                    ),
                                                child: const Text('Close'),
                                              ),
                                            ],
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                            );
                          },
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Logout Button
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: _handleLogout,
                      icon: const Icon(Icons.logout),
                      label: const Text('Logout'),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.error.withValues(alpha: 0.1),
                        foregroundColor: AppColors.error,
                        elevation: 0,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                    ),
                  ),

                  const SizedBox(height: 100), // Bottom padding for nav bar
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _SourceOption extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  const _SourceOption({
    required this.icon,
    required this.label,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: AppColors.primary.withValues(alpha: 0.1),
              shape: BoxShape.circle,
            ),
            child: Icon(icon, color: AppColors.primary, size: 32),
          ),
          const SizedBox(height: 8),
          Text(label, style: const TextStyle(fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }
}

class _ModernMenuItem extends StatelessWidget {
  final IconData icon;
  final Color iconColor;
  final String title;
  final String subtitle;
  final VoidCallback onTap;

  const _ModernMenuItem({
    required this.icon,
    required this.iconColor,
    required this.title,
    required this.subtitle,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return ListTile(
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      leading: Container(
        padding: const EdgeInsets.all(10),
        decoration: BoxDecoration(
          color: iconColor.withValues(alpha: 0.1),
          borderRadius: BorderRadius.circular(10),
        ),
        child: Icon(icon, color: iconColor, size: 22),
      ),
      title: Text(
        title,
        style: const TextStyle(fontWeight: FontWeight.w600),
      ),
      subtitle: Text(
        subtitle,
        style: TextStyle(
          fontSize: 12,
          color: AppColors.textMuted,
        ),
      ),
      trailing: Icon(
        Icons.chevron_right,
        color: AppColors.textMuted,
      ),
    );
  }
}
