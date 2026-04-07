import 'package:smarbo_events/domain/entities/event.dart';

class EventModel extends Event {
  const EventModel({
    required super.id,
    required super.eventName,
    required super.eventType,
    required super.eventDate,
    required super.location,
    super.description,
    super.coverImage,
    required super.eventOwnerId,
    required super.createdBy,
    required super.createdAt,
    required super.updatedAt,
    super.ownerName,
  });

  factory EventModel.fromJson(Map<String, dynamic> json) {
    return EventModel(
      id: json['id'],
      eventName: json['event_name'],
      eventType: json['event_type'],
      eventDate: DateTime.parse(json['event_date']),
      location: json['location'],
      description: json['description'],
      coverImage: json['cover_image'],
      eventOwnerId: json['event_owner_id'],
      createdBy: json['created_by'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      ownerName: json['profiles']?['full_name'], // Join profile if available
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_name': eventName,
      'event_type': eventType,
      'event_date': eventDate.toIso8601String().split('T')[0],
      'location': location,
      'description': description,
      'cover_image': coverImage,
      'event_owner_id': eventOwnerId,
      'created_by': createdBy,
    };
  }
}
