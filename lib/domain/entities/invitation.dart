import 'package:equatable/equatable.dart';

class Invitation extends Equatable {
  final String id;
  final String eventId;
  final String guestName;
  final String? phoneNumber;
  final String status;
  final DateTime createdAt;
  final DateTime updatedAt;

  const Invitation({
    required this.id,
    required this.eventId,
    required this.guestName,
    this.phoneNumber,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
  });

  @override
  List<Object?> get props => [id, eventId, guestName, phoneNumber, status, createdAt, updatedAt];
}
