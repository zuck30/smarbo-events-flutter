# SmarboPlusEvent — Modern Event Management

A fully rewritten event management system from PHP to Flutter and Supabase.

## Setup Instructions

### 1. Supabase Setup
- Create a new project in [Supabase](https://supabase.com/).
- In the SQL Editor, run the following migration scripts from the `supabase/migrations/` directory:
  - `001_initial_schema.sql`
  - `002_rls_policies.sql`
- Enable **Email Auth** in the Authentication settings.
- Go to Project Settings > API to get your `SUPABASE_URL` and `SUPABASE_ANON_KEY`.

### 2. Flutter Configuration
- Install Flutter dependencies: `flutter pub get`.
- Update `lib/main.dart` with your Supabase credentials:
  ```dart
  await Supabase.initialize(
    url: 'YOUR_SUPABASE_URL',
    anonKey: 'YOUR_SUPABASE_ANON_KEY',
  );
  ```

### 3. Running the App
- Start the app on any platform: `flutter run`.
- To build for production: `flutter build web` or `flutter build apk`.

## Features
- **Admin Dashboard:** Full system overview, user and event management, reports.
- **Owner Dashboard:** Manage your own events, track contributions, attendance, and invitations.
- **Shared Modules:** Real-time post updates with likes and comments.
- **Role-based Security:** Enforced via Supabase Row Level Security (RLS).
- **Responsive Design:** Works on mobile, tablet, and web (Material 3).

## Tech Stack
- **Frontend:** Flutter 3.x
- **Backend:** Supabase (PostgreSQL, Auth, Storage)
- **State Management:** BLoC (or direct Supabase streams)
- **Navigation:** GoRouter with Guards
