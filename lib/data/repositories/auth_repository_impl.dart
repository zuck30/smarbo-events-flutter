import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/data/models/profile_model.dart';
import 'package:smarbo_events/domain/entities/profile.dart';
import 'package:smarbo_events/domain/repositories/auth_repository.dart';

class SupabaseAuthRepository implements AuthRepository {
  final SupabaseClient _supabase;

  SupabaseAuthRepository(this._supabase);

  @override
  Future<Profile?> getCurrentUser() async {
    final user = _supabase.auth.currentUser;
    if (user == null) return null;

    final response = await _supabase
        .from('profiles')
        .select()
        .eq('id', user.id)
        .single();

    return ProfileModel.fromJson(response);
  }

  @override
  Future<void> signIn({required String email, required String password}) async {
    await _supabase.auth.signInWithPassword(email: email, password: password);
  }

  @override
  Future<void> signUp({
    required String email,
    required String password,
    required String username,
    required String fullName,
  }) async {
    await _supabase.auth.signUp(
      email: email,
      password: password,
      data: {
        'username': username,
        'full_name': fullName,
        'role': 'event_owner',
      },
    );
  }

  @override
  Future<void> signOut() async {
    await _supabase.auth.signOut();
  }

  @override
  Future<void> updatePassword({required String currentPassword, required String newPassword}) async {
    // Supabase allows password update without current password if logged in,
    // but for extra security we could re-authenticate.
    // Here we follow simple update.
    await _supabase.auth.updateUser(UserAttributes(password: newPassword));
  }
}
