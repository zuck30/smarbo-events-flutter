import 'package:equatable/equatable.dart';

class EventPost extends Equatable {
  final String id;
  final String eventId;
  final String title;
  final String? content;
  final String postType;
  final String? mediaUrl;
  final String mediaType;
  final String postedBy;
  final DateTime createdAt;
  final DateTime updatedAt;
  final String? posterName;
  final String? posterAvatar;
  final int likesCount;
  final int commentsCount;

  const EventPost({
    required this.id,
    required this.eventId,
    required this.title,
    this.content,
    required this.postType,
    this.mediaUrl,
    required this.mediaType,
    required this.postedBy,
    required this.createdAt,
    required this.updatedAt,
    this.posterName,
    this.posterAvatar,
    this.likesCount = 0,
    this.commentsCount = 0,
  });

  @override
  List<Object?> get props => [
        id, eventId, title, content, postType, mediaUrl, mediaType,
        postedBy, createdAt, updatedAt, posterName, posterAvatar,
        likesCount, commentsCount
      ];
}
