class AppConstants {
  static const String appName = 'SmarboPlusEvent';

  // Role names
  static const String roleAdmin = 'admin';
  static const String roleEventOwner = 'event_owner';

  // Event Types
  static const List<String> eventTypes = [
    'harusi',
    'sendoff',
    'kitchen_party',
    'nyingine',
  ];

  // Post Types
  static const List<String> postTypes = [
    'update',
    'photo',
    'video',
    'announcement',
  ];

  // Media Types
  static const String mediaTypeImage = 'image';
  static const String mediaTypeVideo = 'video';

  // Invitation Statuses
  static const String statusPending = 'pending';
  static const String statusApproved = 'approved';
  static const String statusDisapproved = 'disapproved';
}
