import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class OwnerAttendanceScreen extends StatefulWidget {
  const OwnerAttendanceScreen({super.key});

  @override
  State<OwnerAttendanceScreen> createState() => _OwnerAttendanceScreenState();
}

class _OwnerAttendanceScreenState extends State<OwnerAttendanceScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _attendance = [];
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
          _loadAttendance();
        } else {
          _isLoading = false;
        }
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _loadAttendance() async {
    if (_selectedEventId == null) return;
    setState(() => _isLoading = true);
    try {
      final response = await supabase.from('attendance').select().eq('event_id', _selectedEventId!);
      setState(() {
        _attendance = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _checkIn(String id) async {
    try {
      await supabase.from('attendance').update({'status': 'approved', 'attended_at': DateTime.now().toIso8601String()}).eq('id', id);
      _loadAttendance();
    } catch (e) {
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Attendance Tracking', style: TextStyle(fontWeight: FontWeight.w900))),
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
                _loadAttendance();
              },
            ),
          ),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : ListView.builder(
                    itemCount: _attendance.length,
                    padding: const EdgeInsets.all(24.0),
                    itemBuilder: (context, index) {
                      final guest = _attendance[index];
                      return Card(
                        child: ListTile(
                          title: Text(guest['guest_name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                          subtitle: Text(guest['status'].toString().toUpperCase(), style: TextStyle(color: guest['status'] == 'approved' ? Colors.green : Colors.grey)),
                          trailing: guest['status'] != 'approved'
                              ? ElevatedButton(onPressed: () => _checkIn(guest['id']), child: const Text('CHECK-IN'))
                              : const Icon(Icons.check_circle, color: Colors.green),
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
