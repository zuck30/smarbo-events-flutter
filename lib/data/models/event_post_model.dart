import 'package:smarbo_events/domain/entities/event_post.dart';

class EventPostModel extends EventPost {
  const EventPostModel({
    required super.id,
    required super.eventId,
    required super.title,
    super.content,
    required super.postType,
    super.mediaUrl,
    required super.mediaType,
    required super.postedBy,
    required super.createdAt,
    required super.updatedAt,
    super.posterName,
    super.posterAvatar,
    super.likesCount = 0,
    super.commentsCount = 0,
  });

  factory EventPostModel.fromJson(Map<String, dynamic> json) {
    return EventPostModel(
      id: json['id'],
      eventId: json['event_id'],
      title: json['title'],
      content: json['content'],
      postType: json['post_type'] ?? 'update',
      mediaUrl: json['media_url'],
      mediaType: json['media_type'] ?? 'image',
      postedBy: json['posted_by'],
      createdAt: DateTime.parse(json['created_at']),
      updatedAt: DateTime.parse(json['updated_at']),
      posterName: json['profiles']?['full_name'],
      posterAvatar: json['profiles']?['avatar_url'],
      likesCount: json['likes_count'] ?? 0,
      commentsCount: json['comments_count'] ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'event_id': eventId,
      'title': title,
      'content': content,
      'post_type': postType,
      'media_url': mediaUrl,
      'media_type': mediaType,
      'posted_by': postedBy,
    };
  }
}
