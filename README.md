# SmarboPlusEvent — Modern Event Management

A fully rewritten event management system from PHP to Flutter and Supabase.

## Setup Instructions

### 1. Supabase Setup
- Create a new project in [Supabase](https://supabase.com/).
- In the SQL Editor, run the migration scripts from the `supabase/migrations/` directory.
- Enable **Email Auth** in Authentication settings.
- Go to Project Settings > API to get your `SUPABASE_URL` and `SUPABASE_ANON_KEY`.

### 2. Flutter Configuration
- Install Flutter dependencies: `flutter pub get`.
- The app uses `--dart-define` for secure configuration. Run/Build with:
  ```bash
  flutter run --dart-define=SUPABASE_URL=YOUR_URL --dart-define=SUPABASE_ANON_KEY=YOUR_KEY
  ```

## Features
- **Admin Dashboard:** Full system overview, user and event management, system-wide reports.
- **Owner Dashboard:** Manage own events, track contributions, attendance, and invitations.
- **Social Features:** Real-time post updates with likes and comments.
- **Security:** PostgreSQL Row Level Security (RLS) policies.
- **Reporting:** Client-side PDF generation for events and system status.

## Tech Stack
- **Frontend:** Flutter (Material 3)
- **Backend:** Supabase (Auth, DB, Storage)
- **State Management:** flutter_bloc
- **Navigation:** GoRouter with Guarding
