import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class UnauthorizedScreen extends StatelessWidget {
  const UnauthorizedScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(Icons.lock_person, size: 100, color: Colors.red),
              const SizedBox(height: 32),
              const Text(
                'Access Denied',
                style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900),
              ),
              const SizedBox(height: 16),
              const Text(
                'You do not have permission to access this page. Please contact the administrator if you believe this is an error.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 16, color: Colors.grey),
              ),
              const SizedBox(height: 48),
              ElevatedButton(
                onPressed: () => context.go('/'),
                child: const Text('BACK TO HOME'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
