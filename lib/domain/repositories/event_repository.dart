import 'package:smarbo_events/domain/entities/event.dart';
import 'package:smarbo_events/domain/entities/event_post.dart';
import 'package:smarbo_events/domain/entities/contribution.dart';
import 'package:smarbo_events/domain/entities/invitation.dart';
import 'package:smarbo_events/domain/entities/attendance.dart';

abstract class EventRepository {
  // Events
  Future<List<Event>> getEvents();
  Future<Event> getEventById(String id);
  Future<void> createEvent(Event event);
  Future<void> updateEvent(Event event);
  Future<void> deleteEvent(String id);

  // Posts
  Future<List<EventPost>> getEventPosts(String eventId);
  Future<void> createPost(EventPost post);
  Future<void> deletePost(String id);

  // Contributions
  Future<List<Contribution>> getContributions(String eventId);
  Future<void> addContribution(Contribution contribution);
  Future<void> updateContribution(Contribution contribution);
  Future<void> deleteContribution(String id);

  // Invitations
  Future<List<Invitation>> getInvitations(String eventId);
  Future<void> updateInvitationStatus(String id, String status);

  // Attendance
  Future<List<Attendance>> getAttendance(String eventId);
  Future<void> recordAttendance(String eventId, String guestName, String status);
}
