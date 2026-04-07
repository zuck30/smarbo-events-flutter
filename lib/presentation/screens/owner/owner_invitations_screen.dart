import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class OwnerInvitationsScreen extends StatefulWidget {
  const OwnerInvitationsScreen({super.key});

  @override
  State<OwnerInvitationsScreen> createState() => _OwnerInvitationsScreenState();
}

class _OwnerInvitationsScreenState extends State<OwnerInvitationsScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _invitations = [];
  bool _isLoading = true;
  String? _selectedEventId;
  List<dynamic> _events = [];

  @override
  void initState() {
    super.initState();
    _loadEvents();
  }

  Future<void> _loadEvents() async {
    try {
      final user = supabase.auth.currentUser;
      final eventsRes = await supabase.from('events').select().eq('event_owner_id', user!.id);
      setState(() {
        _events = eventsRes;
        if (_events.isNotEmpty) {
          _selectedEventId = _events[0]['id'];
          _loadInvitations();
        } else {
          _isLoading = false;
        }
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _loadInvitations() async {
    if (_selectedEventId == null) return;
    setState(() => _isLoading = true);
    try {
      final response = await supabase.from('invitations').select().eq('event_id', _selectedEventId!);
      setState(() {
        _invitations = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _updateStatus(String id, String status) async {
    try {
      await supabase.from('invitations').update({'status': status}).eq('id', id);
      _loadInvitations();
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Guest List', style: TextStyle(fontWeight: FontWeight.w900))),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(24.0),
            child: DropdownButtonFormField<String>(
              value: _selectedEventId,
              decoration: const InputDecoration(labelText: 'Select Event'),
              items: _events.map((e) => DropdownMenuItem(value: e['id'].toString(), child: Text(e['event_name']))).toList(),
              onChanged: (val) {
                setState(() => _selectedEventId = val);
                _loadInvitations();
              },
            ),
          ),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : ListView.builder(
                    itemCount: _invitations.length,
                    padding: const EdgeInsets.all(24.0),
                    itemBuilder: (context, index) {
                      final guest = _invitations[index];
                      return Card(
                        child: ListTile(
                          title: Text(guest['guest_name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                          subtitle: Text(guest['status'].toString().toUpperCase(), style: TextStyle(color: _getStatusColor(guest['status']))),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              IconButton(onPressed: () => _updateStatus(guest['id'], 'approved'), icon: const Icon(Icons.check, color: Colors.green)),
                              IconButton(onPressed: () => _updateStatus(guest['id'], 'disapproved'), icon: const Icon(Icons.close, color: Colors.red)),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'approved': return Colors.green;
      case 'disapproved': return Colors.red;
      default: return Colors.grey;
    }
  }
}
