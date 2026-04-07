import 'package:smarbo_events/domain/entities/invitation.dart';

class InvitationModel extends Invitation {
  const InvitationModel({
    required super.id,
    required super.eventId,
    required super.guestName,
    super.phoneNumber,
    required super.status,
    required super.createdAt,
    required super.updatedAt,
  });

  factory InvitationModel.fromJson(Map<String, dynamic> json) {
    return InvitationModel(
      id: json['id'],
      eventId: json['event_id'],
      guestName: json['guest_name'],
      phoneNumber: json['phone_number'],
      status: json['status'] ?? 'pending',
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_id': eventId,
      'guest_name': guestName,
      'phone_number': phoneNumber,
      'status': status,
    };
  }
}
