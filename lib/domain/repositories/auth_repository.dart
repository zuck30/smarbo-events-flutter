import 'package:smarbo_events/domain/entities/profile.dart';

abstract class AuthRepository {
  Future<Profile?> getCurrentUser();
  Future<void> signIn({required String email, required String password});
  Future<void> signUp({
    required String email,
    required String password,
    required String username,
    required String fullName,
  });
  Future<void> signOut();
  Future<void> updatePassword({required String currentPassword, required String newPassword});
}
