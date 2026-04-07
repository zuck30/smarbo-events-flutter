import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:intl/intl.dart';

class AdminEventsScreen extends StatefulWidget {
  const AdminEventsScreen({super.key});

  @override
  State<AdminEventsScreen> createState() => _AdminEventsScreenState();
}

class _AdminEventsScreenState extends State<AdminEventsScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _events = [];
  List<dynamic> _owners = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadData();
  }

  Future<void> _loadData() async {
    try {
      final eventsRes = await supabase.from('events').select('*, profiles(full_name)').order('event_date', ascending: false);
      final ownersRes = await supabase.from('profiles').select().eq('role', 'event_owner');
      setState(() {
        _events = eventsRes;
        _owners = ownersRes;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showCreateEventDialog() {
    final nameController = TextEditingController();
    final locationController = TextEditingController();
    final descriptionController = TextEditingController();
    DateTime selectedDate = DateTime.now();
    String? selectedOwnerId;
    String selectedType = 'harusi';

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Create New Event'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: nameController, decoration: const InputDecoration(labelText: 'Event Name')),
                DropdownButtonFormField<String>(
                  value: selectedType,
                  items: ['harusi', 'sendoff', 'kitchen_party', 'nyingine'].map((t) => DropdownMenuItem(value: t, child: Text(t.toUpperCase()))).toList(),
                  onChanged: (val) => setDialogState(() => selectedType = val!),
                  decoration: const InputDecoration(labelText: 'Event Type'),
                ),
                ListTile(
                  title: Text('Date: ${DateFormat('yyyy-MM-dd').format(selectedDate)}'),
                  trailing: const Icon(Icons.calendar_today),
                  onTap: () async {
                    final picked = await showDatePicker(context: context, initialDate: selectedDate, firstDate: DateTime.now(), lastDate: DateTime(2100));
                    if (picked != null) setDialogState(() => selectedDate = picked);
                  },
                ),
                TextField(controller: locationController, decoration: const InputDecoration(labelText: 'Location')),
                DropdownButtonFormField<String>(
                  value: selectedOwnerId,
                  items: _owners.map((o) => DropdownMenuItem(value: o['id'].toString(), child: Text(o['full_name'] ?? o['username']))).toList(),
                  onChanged: (val) => setDialogState(() => selectedOwnerId = val),
                  decoration: const InputDecoration(labelText: 'Event Owner'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('CANCEL')),
            ElevatedButton(
              onPressed: () async {
                if (nameController.text.isEmpty || selectedOwnerId == null) return;
                await supabase.from('events').insert({
                  'event_name': nameController.text,
                  'event_type': selectedType,
                  'event_date': DateFormat('yyyy-MM-dd').format(selectedDate),
                  'location': locationController.text,
                  'description': descriptionController.text,
                  'event_owner_id': selectedOwnerId,
                  'created_by': supabase.auth.currentUser!.id,
                });
                if (mounted) {
                  Navigator.pop(context);
                  _loadData();
                }
              },
              child: const Text('CREATE'),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Manage Events', style: TextStyle(fontWeight: FontWeight.w900))),
      floatingActionButton: FloatingActionButton(
        onPressed: _showCreateEventDialog,
        backgroundColor: AppTheme.primaryColor,
        child: const Icon(Icons.add, color: Colors.white),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadData,
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
                        '${event['profiles']?['full_name'] ?? 'Unknown'} • ${DateFormat('MMM d, y').format(DateTime.parse(event['event_date']))}',
                        style: const TextStyle(fontSize: 12, color: Colors.grey),
                      ),
                      trailing: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.delete, color: Colors.red),
                            onPressed: () async {
                              final confirm = await showDialog<bool>(
                                context: context,
                                builder: (context) => AlertDialog(
                                  title: const Text('Delete Event'),
                                  content: const Text('Are you sure you want to delete this event? All associated data will be lost.'),
                                  actions: [
                                    TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('CANCEL')),
                                    TextButton(onPressed: () => Navigator.pop(context, true), child: const Text('DELETE', style: TextStyle(color: Colors.red))),
                                  ],
                                ),
                              );
                              if (confirm == true) {
                                await supabase.from('events').delete().eq('id', event['id']);
                                _loadData();
                              }
                            },
                          ),
                        ],
                      ),
                    ),
                  );
                },
              ),
            ),
    );
  }
}
