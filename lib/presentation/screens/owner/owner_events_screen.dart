import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:intl/intl.dart';

class OwnerEventsScreen extends StatefulWidget {
  const OwnerEventsScreen({super.key});

  @override
  State<OwnerEventsScreen> createState() => _OwnerEventsScreenState();
}

class _OwnerEventsScreenState extends State<OwnerEventsScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _events = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadEvents();
  }

  Future<void> _loadEvents() async {
    try {
      final user = supabase.auth.currentUser;
      final response = await supabase.from('events').select().eq('event_owner_id', user!.id).order('event_date', ascending: false);
      setState(() {
        _events = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('My Events', style: TextStyle(fontWeight: FontWeight.w900))),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadEvents,
              child: ListView.builder(
                itemCount: _events.length,
                padding: const EdgeInsets.all(24.0),
                itemBuilder: (context, index) {
                  final event = _events[index];
                  return Card(
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundImage: event['cover_image'] != null ? NetworkImage(event['cover_image']) : null,
                        child: event['cover_image'] == null ? const Icon(Icons.event) : null,
                      ),
                      title: Text(event['event_name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text(
                        DateFormat('MMM d, y').format(DateTime.parse(event['event_date'])),
                        style: const TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                      trailing: IconButton(onPressed: () {}, icon: const Icon(Icons.chevron_right)),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
