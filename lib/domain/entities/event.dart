import 'package:equatable/equatable.dart';

class Event extends Equatable {
  final String id;
  final String eventName;
  final String eventType;
  final DateTime eventDate;
  final String location;
  final String? description;
  final String? coverImage;
  final String eventOwnerId;
  final String createdBy;
  final DateTime createdAt;
  final DateTime updatedAt;
  final String? ownerName;

  const Event({
    required this.id,
    required this.eventName,
    required this.eventType,
    required this.eventDate,
    required this.location,
    this.description,
    this.coverImage,
    required this.eventOwnerId,
    required this.createdBy,
    required this.createdAt,
    required this.updatedAt,
    this.ownerName,
  });

  @override
  List<Object?> get props => [
        id, eventName, eventType, eventDate, location, description,
        coverImage, eventOwnerId, createdBy, createdAt, updatedAt, ownerName
      ];
}
