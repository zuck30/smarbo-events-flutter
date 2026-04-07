import 'package:smarbo_events/domain/entities/attendance.dart';

class AttendanceModel extends Attendance {
  const AttendanceModel({
    required super.id,
    required super.eventId,
    required super.guestName,
    required super.status,
    super.attendedAt,
    required super.createdAt,
  });

  factory AttendanceModel.fromJson(Map<String, dynamic> json) {
    return AttendanceModel(
      id: json['id'],
      eventId: json['event_id'],
      guestName: json['guest_name'],
      status: json['status'] ?? 'pending',
      attendedAt: json['attended_at'] != null ? DateTime.parse(json['attended_at']) : null,
      createdAt: DateTime.parse(json['created_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_id': eventId,
      'guest_name': guestName,
      'status': status,
      'attended_at': attendedAt?.toIso8601String(),
    };
  }
}
