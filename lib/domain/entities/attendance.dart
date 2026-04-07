import 'package:equatable/equatable.dart';

class Attendance extends Equatable {
  final String id;
  final String eventId;
  final String guestName;
  final String status;
  final DateTime? attendedAt;
  final DateTime createdAt;

  const Attendance({
    required this.id,
    required this.eventId,
    required this.guestName,
    required this.status,
    this.attendedAt,
    required this.createdAt,
  });

  @override
  List<Object?> get props => [id, eventId, guestName, status, attendedAt, createdAt];
}
