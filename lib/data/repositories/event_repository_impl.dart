import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/data/models/event_model.dart';
import 'package:smarbo_events/data/models/event_post_model.dart';
import 'package:smarbo_events/data/models/contribution_model.dart';
import 'package:smarbo_events/data/models/invitation_model.dart';
import 'package:smarbo_events/data/models/attendance_model.dart';
import 'package:smarbo_events/domain/entities/event.dart';
import 'package:smarbo_events/domain/entities/event_post.dart';
import 'package:smarbo_events/domain/entities/contribution.dart';
import 'package:smarbo_events/domain/entities/invitation.dart';
import 'package:smarbo_events/domain/entities/attendance.dart';
import 'package:smarbo_events/domain/repositories/event_repository.dart';

class SupabaseEventRepository implements EventRepository {
  final SupabaseClient _supabase;

  SupabaseEventRepository(this._supabase);

  @override
  Future<List<Event>> getEvents() async {
    final response = await _supabase
        .from('events')
        .select('*, profiles(full_name)')
        .order('event_date', ascending: false);

    return (response as List).map((json) => EventModel.fromJson(json)).toList();
  }

  @override
  Future<Event> getEventById(String id) async {
    final response = await _supabase
        .from('events')
        .select('*, profiles(full_name)')
        .eq('id', id)
        .single();

    return EventModel.fromJson(response);
  }

  @override
  Future<void> createEvent(Event event) async {
    await _supabase.from('events').insert({
      'event_name': event.eventName,
      'event_type': event.eventType,
      'event_date': event.eventDate.toIso8601String().split('T')[0],
      'location': event.location,
      'description': event.description,
      'cover_image': event.coverImage,
      'event_owner_id': event.eventOwnerId,
      'created_by': _supabase.auth.currentUser!.id,
    });
  }

  @override
  Future<void> updateEvent(Event event) async {
    await _supabase.from('events').update({
      'event_name': event.eventName,
      'event_type': event.eventType,
      'event_date': event.eventDate.toIso8601String().split('T')[0],
      'location': event.location,
      'description': event.description,
      'cover_image': event.coverImage,
    }).eq('id', event.id);
  }

  @override
  Future<void> deleteEvent(String id) async {
    await _supabase.from('events').delete().eq('id', id);
  }

  @override
  Future<List<EventPost>> getEventPosts(String eventId) async {
    final response = await _supabase
        .from('event_posts')
        .select('*, profiles(full_name, avatar_url)')
        .eq('event_id', eventId)
        .order('created_at', ascending: false);

    return (response as List).map((json) => EventPostModel.fromJson(json)).toList();
  }

  @override
  Future<void> createPost(EventPost post) async {
    await _supabase.from('event_posts').insert({
      'event_id': post.eventId,
      'title': post.title,
      'content': post.content,
      'post_type': post.postType,
      'media_url': post.mediaUrl,
      'media_type': post.mediaType,
      'posted_by': _supabase.auth.currentUser!.id,
    });
  }

  @override
  Future<void> deletePost(String id) async {
    await _supabase.from('event_posts').delete().eq('id', id);
  }

  @override
  Future<List<Contribution>> getContributions(String eventId) async {
    final response = await _supabase
        .from('contributions')
        .select()
        .eq('event_id', eventId)
        .order('created_at', ascending: false);

    return (response as List).map((json) => ContributionModel.fromJson(json)).toList();
  }

  @override
  Future<void> addContribution(Contribution contribution) async {
    await _supabase.from('contributions').insert({
      'event_id': contribution.eventId,
      'contributor_name': contribution.contributorName,
      'phone_number': contribution.phoneNumber,
      'promised_amount': contribution.promisedAmount,
      'paid_amount': contribution.paidAmount,
      'status': contribution.paidAmount >= contribution.promisedAmount ? 'approved' : 'pending',
    });
  }

  @override
  Future<void> updateContribution(Contribution contribution) async {
    await _supabase.from('contributions').update({
      'contributor_name': contribution.contributorName,
      'phone_number': contribution.phoneNumber,
      'promised_amount': contribution.promisedAmount,
      'paid_amount': contribution.paidAmount,
      'status': contribution.paidAmount >= contribution.promisedAmount ? 'approved' : 'pending',
    }).eq('id', contribution.id);
  }

  @override
  Future<void> deleteContribution(String id) async {
    await _supabase.from('contributions').delete().eq('id', id);
  }

  @override
  Future<List<Invitation>> getInvitations(String eventId) async {
    final response = await _supabase
        .from('invitations')
        .select()
        .eq('event_id', eventId)
        .order('guest_name', ascending: true);

    return (response as List).map((json) => InvitationModel.fromJson(json)).toList();
  }

  @override
  Future<void> updateInvitationStatus(String id, String status) async {
    await _supabase.from('invitations').update({'status': status}).eq('id', id);
  }

  @override
  Future<List<Attendance>> getAttendance(String eventId) async {
    final response = await _supabase
        .from('attendance')
        .select()
        .eq('event_id', eventId)
        .order('guest_name', ascending: true);

    return (response as List).map((json) => AttendanceModel.fromJson(json)).toList();
  }

  @override
  Future<void> recordAttendance(String eventId, String guestName, String status) async {
    // Check if exists
    final check = await _supabase
        .from('attendance')
        .select()
        .eq('event_id', eventId)
        .eq('guest_name', guestName)
        .maybeSingle();

    if (check != null) {
      await _supabase.from('attendance').update({
        'status': status,
        'attended_at': status == 'approved' ? DateTime.now().toIso8601String() : null,
      }).eq('id', check['id']);
    } else {
      await _supabase.from('attendance').insert({
        'event_id': eventId,
        'guest_name': guestName,
        'status': status,
        'attended_at': status == 'approved' ? DateTime.now().toIso8601String() : null,
      });
    }
  }

  @override
  Future<void> toggleLike(String postId) async {
    final userId = _supabase.auth.currentUser!.id;
    final existing = await _supabase
        .from('event_post_likes')
        .select()
        .eq('post_id', postId)
        .eq('user_id', userId)
        .maybeSingle();

    if (existing != null) {
      await _supabase.from('event_post_likes').delete().eq('id', existing['id']);
    } else {
      await _supabase.from('event_post_likes').insert({'post_id': postId, 'user_id': userId});
    }
  }

  @override
  Future<void> addComment(String postId, String comment) async {
    await _supabase.from('event_post_comments').insert({
      'post_id': postId,
      'user_id': _supabase.auth.currentUser!.id,
      'comment': comment,
    });
  }

  @override
  Future<List<Map<String, dynamic>>> getComments(String postId) async {
    final response = await _supabase
        .from('event_post_comments')
        .select('*, profiles(full_name, avatar_url)')
        .eq('post_id', postId)
        .order('created_at', ascending: true);
    return List<Map<String, dynamic>>.from(response);
  }
}
