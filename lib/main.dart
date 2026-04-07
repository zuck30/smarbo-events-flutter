import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/core/utils/router.dart';
import 'package:smarbo_events/data/repositories/auth_repository_impl.dart';
import 'package:smarbo_events/presentation/bloc/auth_bloc.dart';
import 'package:smarbo_events/presentation/bloc/auth_event.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();

  // Use environment variables or --dart-define for credentials
  const supabaseUrl = String.fromEnvironment('SUPABASE_URL', defaultValue: 'https://dummy.supabase.co');
  const supabaseAnonKey = String.fromEnvironment('SUPABASE_ANON_KEY', defaultValue: 'dummy_key');

  await Supabase.initialize(
    url: supabaseUrl,
    anonKey: supabaseAnonKey,
  );

  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return RepositoryProvider(
      create: (context) => SupabaseAuthRepository(Supabase.instance.client),
      child: BlocProvider(
        create: (context) => AuthBloc(
          authRepository: RepositoryProvider.of<SupabaseAuthRepository>(context),
        )..add(AppStarted()),
        child: MaterialApp.router(
          title: 'SmarboPlusEvent',
          theme: AppTheme.lightTheme,
          darkTheme: AppTheme.darkTheme,
          themeMode: ThemeMode.dark,
          routerConfig: AppRouter.router,
          debugShowCheckedModeBanner: false,
        ),
      ),
    );
  }
}
