import 'package:equatable/equatable.dart';

class Profile extends Equatable {
  final String id;
  final String username;
  final String? fullName;
  final String? phone;
  final String? avatarUrl;
  final String role;
  final bool isActive;
  final DateTime createdAt;

  const Profile({
    required this.id,
    required this.username,
    this.fullName,
    this.phone,
    this.avatarUrl,
    required this.role,
    this.isActive = true,
    required this.createdAt,
  });

  bool get isAdmin => role == 'admin';

  @override
  List<Object?> get props => [id, username, fullName, phone, avatarUrl, role, isActive, createdAt];
}
