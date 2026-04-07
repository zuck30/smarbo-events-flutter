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

  // UI Strings
  static const String loginTitle = 'Login to manage your events';
  static const String registerTitle = 'Create Account';
  static const String dashboardTitle = 'System Overview';
  static const String ownerDashboardTitle = 'Welcome Back!';
  static const String manageUsers = 'Manage Users';
  static const String manageEvents = 'Manage Events';
  static const String settings = 'Account Settings';
  static const String logout = 'Logout';
  static const String delete = 'Delete';
  static const String cancel = 'Cancel';
  static const String create = 'Create';
  static const String add = 'Add';
  static const String update = 'Update';
  static const String save = 'Save';
  static const String downloadReport = 'Download Report';
}
