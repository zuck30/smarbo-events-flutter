import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/domain/entities/event.dart';
import 'package:smarbo_events/domain/entities/event_post.dart';
import 'package:smarbo_events/presentation/screens/auth/login_screen.dart';
import 'package:smarbo_events/presentation/screens/auth/register_screen.dart';
import 'package:smarbo_events/presentation/screens/admin/admin_dashboard.dart';
import 'package:smarbo_events/presentation/screens/admin/admin_users_screen.dart';
import 'package:smarbo_events/presentation/screens/admin/admin_events_screen.dart';
import 'package:smarbo_events/presentation/screens/admin/admin_settings_screen.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_dashboard.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_events_screen.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_attendance_screen.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_contributions_screen.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_invitations_screen.dart';
import 'package:smarbo_events/presentation/screens/owner/owner_reports_screen.dart';
import 'package:smarbo_events/presentation/screens/shared/event_details_screen.dart';
import 'package:smarbo_events/presentation/screens/shared/post_details_screen.dart';
import 'package:smarbo_events/presentation/screens/shared/unauthorized_screen.dart';

class AppRouter {
  static final supabase = Supabase.instance.client;
  static String? _cachedRole;

  static void setRole(String? role) => _cachedRole = role;
  static void clearRole() => _cachedRole = null;

  static final router = GoRouter(
    initialLocation: '/',
    redirect: (context, state) async {
      final session = supabase.auth.currentSession;
      final loggingIn = state.matchedLocation == '/login' || state.matchedLocation == '/register';

      if (session == null) {
        clearRole();
        return loggingIn ? null : '/login';
      }

      if (_cachedRole == null) {
        try {
          final profile = await supabase
              .from('profiles')
              .select('role')
              .eq('id', session.user.id)
              .single();
          _cachedRole = profile['role'];
        } catch (e) {
          return '/login';
        }
      }

      if (loggingIn || state.matchedLocation == '/') {
        return _cachedRole == 'admin' ? '/admin' : '/owner';
      }

      if (state.matchedLocation.startsWith('/admin') && _cachedRole != 'admin') {
        return '/unauthorized';
      }

      return null;
    },
    routes: [
      GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
      GoRoute(path: '/register', builder: (context, state) => const RegisterScreen()),

      // Admin Routes
      GoRoute(path: '/admin', builder: (context, state) => const AdminDashboard()),
      GoRoute(path: '/admin/users', builder: (context, state) => const AdminUsersScreen()),
      GoRoute(path: '/admin/events', builder: (context, state) => const AdminEventsScreen()),
      GoRoute(path: '/admin/settings', builder: (context, state) => const AdminSettingsScreen()),

      // Owner Routes
      GoRoute(path: '/owner', builder: (context, state) => const OwnerDashboard()),
      GoRoute(path: '/owner/events', builder: (context, state) => const OwnerEventsScreen()),
      GoRoute(path: '/owner/attendance', builder: (context, state) => const OwnerAttendanceScreen()),
      GoRoute(path: '/owner/contributions', builder: (context, state) => const OwnerContributionsScreen()),
      GoRoute(path: '/owner/invitations', builder: (context, state) => const OwnerInvitationsScreen()),
      GoRoute(path: '/owner/reports', builder: (context, state) => const OwnerReportsScreen()),

      // Shared Routes
      GoRoute(
        path: '/events/:id',
        builder: (context, state) => EventDetailsScreen(event: state.extra as Event),
      ),
      GoRoute(
        path: '/posts/:id',
        builder: (context, state) => PostDetailsScreen(post: state.extra as EventPost),
      ),
      GoRoute(path: '/unauthorized', builder: (context, state) => const UnauthorizedScreen()),
    ],
  );
}
