import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/core/utils/report_generator.dart';
import 'package:smarbo_events/data/models/event_model.dart';
import 'package:smarbo_events/data/models/contribution_model.dart';

class OwnerReportsScreen extends StatefulWidget {
  const OwnerReportsScreen({super.key});

  @override
  State<OwnerReportsScreen> createState() => _OwnerReportsScreenState();
}

class _OwnerReportsScreenState extends State<OwnerReportsScreen> {
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
      final response = await supabase.from('events').select().eq('event_owner_id', user!.id);
      setState(() {
        _events = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _downloadReport(Map<String, dynamic> eventData) async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Downloading report...')));

      final event = EventModel.fromJson(eventData);

      final contributionsRes = await supabase
          .from('contributions')
          .select()
          .eq('event_id', event.id);

      final contributions = (contributionsRes as List)
          .map((json) => ContributionModel.fromJson(json))
          .toList();

      await ReportGenerator.generateEventReport(
        event: event,
        contributions: contributions,
      );
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Failed to generate report: $e'), backgroundColor: Colors.red),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Event Reports', style: TextStyle(fontWeight: FontWeight.w900))),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : ListView.builder(
              itemCount: _events.length,
              padding: const EdgeInsets.all(24.0),
              itemBuilder: (context, index) {
                final event = _events[index];
                return Card(
                  child: ListTile(
                    leading: const Icon(Icons.picture_as_pdf, color: Colors.red),
                    title: Text('Download Report for ${event['event_name']}', style: const TextStyle(fontWeight: FontWeight.bold)),
                    subtitle: const Text('Generates full financial and guest report', style: TextStyle(fontSize: 12, color: Colors.grey)),
                    onTap: () => _downloadReport(event),
                  ),
                );
              },
            ),
    );
  }
}
