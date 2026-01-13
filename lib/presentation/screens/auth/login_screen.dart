import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:rive/rive.dart' hide Image;
import '../../../core/theme/app_theme.dart';
import '../../providers/auth_provider.dart';
import '../../widgets/custom_text_field.dart';
import '../../widgets/loading_button.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _formKey = GlobalKey<FormState>();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();

  // Rive State Machine
  StateMachineController? controller;
  SMIInput<bool>? isFocus;
  SMIInput<double>? numLook;
  SMIInput<bool>? isPrivateField; // Hands up covering eyes
  SMIInput<bool>? isPrivateFieldShow; // Peeking through fingers
  SMITrigger? successTrigger;
  SMITrigger? failTrigger;

  final FocusNode _emailFocus = FocusNode();
  final FocusNode _passwordFocus = FocusNode();

  bool _obscurePassword = true;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _emailFocus.addListener(_emailFocusChanged);
    _passwordFocus.addListener(_passwordFocusChanged);
  }

  void _emailFocusChanged() {
    if (_emailFocus.hasFocus) {
      isFocus?.change(true);
      isPrivateField?.change(false);
      isPrivateFieldShow?.change(false);
    } else {
      isFocus?.change(false);
    }
  }

  void _passwordFocusChanged() {
    if (_passwordFocus.hasFocus) {
      isFocus?.change(false); // Stop looking around
      isPrivateField?.change(true); // Hands up
      isPrivateFieldShow?.change(!_obscurePassword); // Peek if visible
    } else {
      isPrivateField?.change(false);
      isPrivateFieldShow?.change(false);
    }
  }

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    _emailFocus.dispose();
    _passwordFocus.dispose();
    controller?.dispose();
    super.dispose();
  }

  Future<void> _handleLogin() async {
    FocusScope.of(context).unfocus(); // Close keyboard
    
    if (_formKey.currentState?.validate() ?? false) {
      // Local loading handling
      setState(() => _isLoading = true);

      // Make bear look busy/focusing during loading
      // For the new animation, isFocus=true seems appropriate or just numLook moving
      isPrivateField?.change(false);
      isFocus?.change(true); 

      // Note: We expect AuthNotifier NOT to set global loading to prevent redirects
      final success = await ref
          .read(authProvider.notifier)
          .login(_emailController.text.trim(), _passwordController.text);
      
      // Stop checking animation and local loading
      isFocus?.change(false);
      if (mounted) setState(() => _isLoading = false);

      if (!mounted) return;

      if (success) {
        // Try to fire animation safely
        try {
          successTrigger?.fire();
        } catch (_) {}
        
        // Wait for animation to play
        await Future.delayed(const Duration(milliseconds: 1500));
        
        if (mounted) {
           // This will update status to authenticated and trigger router redirect
           ref.read(authProvider.notifier).finalizeLogin();
           // Force navigation
           context.go('/home');
        }
      } else {
        // Show error snackbar
        final errorMessage = ref.read(authProvider).errorMessage;
        if (errorMessage != null && mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            SnackBar(
              content: Text(errorMessage),
              backgroundColor: AppColors.error,
            ),
          );
        }

        // Fire fail animation
        try {
          failTrigger?.fire();
        } catch (_) {}
      }
    }
  }

  Future<void> _handleGoogleLogin() async {
    setState(() => _isLoading = true);
    isPrivateField?.change(false);
    isFocus?.change(true); // Bear looks busy

    // Note: We expect AuthNotifier NOT to set global loading
    final success = await ref.read(authProvider.notifier).loginWithGoogle();
    
    isFocus?.change(false); // Stop busy animation
    if (mounted) setState(() => _isLoading = false);

    if (!mounted) return;

    if (success) {
      // Try to fire animation safely
      try {
        successTrigger?.fire();
      } catch (_) {}
      
      // Wait for animation
      await Future.delayed(const Duration(milliseconds: 1500));
      
      if (mounted) {
        // Update state to authenticated
        ref.read(authProvider.notifier).finalizeLogin();
        // Force navigation to home to ensure user isn't stuck
        context.go('/home');
      }
    } else {
      // Show error snackbar
      final errorMessage = ref.read(authProvider).errorMessage;
      if (errorMessage != null && mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text(errorMessage),
            backgroundColor: AppColors.error,
          ),
        );
      }

      try {
        failTrigger?.fire();
      } catch (_) {}
      await Future.delayed(const Duration(milliseconds: 1000));
    }
  }

  @override
  Widget build(BuildContext context) {
    // Only use local loading state, ignore global auth status loading to prevent UI flicker/redirects
    final isLoading = _isLoading;

    // Show error snackbar
    return Scaffold(
      body: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            // Responsive sizing based on screen height
            final screenHeight = constraints.maxHeight;
            final riveSize = screenHeight < 600 ? 180.0 : 220.0;
            final topPadding = screenHeight < 600 ? 16.0 : 32.0;

            return SingleChildScrollView(
              padding: const EdgeInsets.all(24),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  SizedBox(height: topPadding),

                  // Logo & Title
                  Center(
                    child: Column(
                      children: [
                        Text(
                          'LuhurCamp',
                          style: Theme.of(context).textTheme.headlineMedium
                              ?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: AppColors.primary,
                              ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Selamat datang kembali!',
                          style: Theme.of(context).textTheme.bodyLarge
                              ?.copyWith(color: AppColors.textSecondary),
                        ),
                        const SizedBox(height: 16),
                        SizedBox(
                          height: riveSize,
                          width: riveSize,
                          child: RiveAnimation.asset(
                            'assets/animation/auth-teddy.riv',
                            fit: BoxFit.contain,
                            stateMachines: const ['Login Machine'],
                            onInit: (artboard) {
                              controller = StateMachineController.fromArtboard(
                                artboard,
                                'Login Machine',
                              );
                              if (controller != null) {
                                artboard.addController(controller!);
                                isFocus = controller?.getBoolInput('isFocus');
                                numLook = controller?.getNumberInput('numLook');
                                isPrivateField = controller?.getBoolInput('isPrivateField');
                                isPrivateFieldShow = controller?.getBoolInput('isPrivateFieldShow');
                                successTrigger = controller?.getTriggerInput('successTrigger');
                                failTrigger = controller?.getTriggerInput('failTrigger');
                              }
                            },
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Login Form
                  Form(
                    key: _formKey,
                    child: Column(
                      children: [
                        CustomTextField(
                          controller: _emailController,
                          label: 'Email',
                          hint: 'Masukkan email Anda',
                          keyboardType: TextInputType.emailAddress,
                          prefixIcon: Icons.email_outlined,
                          focusNode: _emailFocus,
                          onChanged: (value) {
                            // Mata mengikuti panjang email (0-100 range)
                            numLook?.change(
                              (value.length * 2.5).clamp(0, 100).toDouble(),
                            );
                          },
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Email wajib diisi';
                            }
                            if (!value.contains('@')) {
                              return 'Email tidak valid';
                            }
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        CustomTextField(
                          controller: _passwordController,
                          label: 'Password',
                          hint: 'Masukkan password',
                          obscureText: _obscurePassword,
                          focusNode: _passwordFocus,
                          prefixIcon: Icons.lock_outline,
                          suffixIcon: IconButton(
                            onPressed: () {
                              setState(() {
                                _obscurePassword = !_obscurePassword;
                              });
                              if (_passwordFocus.hasFocus) {
                                // If password visible, peek (shown=true). If hidden, cover eyes (shown=false).
                                isPrivateFieldShow?.change(!_obscurePassword);
                              }
                            },
                            icon: Icon(
                              _obscurePassword
                                  ? Icons.visibility_outlined
                                  : Icons.visibility_off_outlined,
                                color: AppColors.textMuted,
                            ),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) {
                              return 'Password wajib diisi';
                            }
                            return null;
                          },
                        ),
                        // Forgot Password Button
                        Align(
                          alignment: Alignment.centerRight,
                          child: TextButton(
                            onPressed: () => context.push('/forgot-password'),
                            child: Text(
                              'Lupa Password?',
                              style: TextStyle(
                                color: AppColors.primary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Login Button
                  LoadingButton(
                    onPressed: isLoading ? null : () {
                      _handleLogin();
                    },
                    isLoading: false, // Disable spinner as requested
                    child: Text(isLoading ? 'Sedang Memproses...' : 'Masuk'),
                  ),

                  const SizedBox(height: 16),

                  // Google Login Button
                  OutlinedButton.icon(
                    onPressed: isLoading ? null : _handleGoogleLogin,
                    icon: Image.asset(
                      'assets/images/google_logo.png',
                      height: 24,
                    ),
                    label: const Text('Masuk dengan Google'),
                    style: OutlinedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),

                  const SizedBox(height: 24),

                  // Register Link
                  Wrap(
                    alignment: WrapAlignment.center,
                    crossAxisAlignment: WrapCrossAlignment.center,
                    children: [
                      Text(
                        'Belum punya akun? ',
                        style: TextStyle(color: AppColors.textSecondary),
                      ),
                      TextButton(
                        onPressed: () => context.push('/register'),
                        child: const Text(
                          'Daftar',
                          style: TextStyle(fontWeight: FontWeight.bold),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            );
          },
        ),
      ),
    );
  }
}
