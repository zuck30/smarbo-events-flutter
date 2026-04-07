import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class OwnerContributionsScreen extends StatefulWidget {
  const OwnerContributionsScreen({super.key});

  @override
  State<OwnerContributionsScreen> createState() => _OwnerContributionsScreenState();
}

class _OwnerContributionsScreenState extends State<OwnerContributionsScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _contributions = [];
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
          _loadContributions();
        } else {
          _isLoading = false;
        }
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _loadContributions() async {
    if (_selectedEventId == null) return;
    setState(() => _isLoading = true);
    try {
      final response = await supabase.from('contributions').select().eq('event_id', _selectedEventId!);
      setState(() {
        _contributions = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showAddContributionDialog() {
    final nameController = TextEditingController();
    final phoneController = TextEditingController();
    final promisedController = TextEditingController();
    final paidController = TextEditingController();

    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Add Contribution'),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(controller: nameController, decoration: const InputDecoration(labelText: 'Contributor Name')),
              TextField(controller: phoneController, decoration: const InputDecoration(labelText: 'Phone Number')),
              TextField(controller: promisedController, decoration: const InputDecoration(labelText: 'Promised Amount'), keyboardType: TextInputType.number),
              TextField(controller: paidController, decoration: const InputDecoration(labelText: 'Paid Amount'), keyboardType: TextInputType.number),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text('CANCEL')),
          ElevatedButton(
            onPressed: () async {
              if (nameController.text.isEmpty || _selectedEventId == null) return;
              final promised = double.tryParse(promisedController.text) ?? 0;
              final paid = double.tryParse(paidController.text) ?? 0;
              await supabase.from('contributions').insert({
                'event_id': _selectedEventId,
                'contributor_name': nameController.text,
                'phone_number': phoneController.text,
                'promised_amount': promised,
                'paid_amount': paid,
                'status': paid >= promised ? 'approved' : 'pending',
              });
              if (mounted) {
                Navigator.pop(context);
                _loadContributions();
              }
            },
            child: const Text('ADD'),
          ),
        ],
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Contributions', style: TextStyle(fontWeight: FontWeight.w900))),
      floatingActionButton: FloatingActionButton(
        onPressed: _showAddContributionDialog,
        backgroundColor: AppTheme.primaryColor,
        child: const Icon(Icons.add, color: Colors.white),
      ),
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
                _loadContributions();
              },
            ),
          ),
          Expanded(
            child: _isLoading
                ? const Center(child: CircularProgressIndicator())
                : ListView.builder(
                    itemCount: _contributions.length,
                    padding: const EdgeInsets.all(24.0),
                    itemBuilder: (context, index) {
                      final c = _contributions[index];
                      return Card(
                        child: ListTile(
                          title: Text(c['contributor_name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                          subtitle: Text('TZS ${c['paid_amount']} / TZS ${c['promised_amount']}', style: const TextStyle(color: Colors.grey)),
                          trailing: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                c['status'] == 'approved' ? Icons.check_circle : Icons.pending,
                                color: c['status'] == 'approved' ? Colors.green : Colors.orange,
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete, color: Colors.red, size: 20),
                                onPressed: () async {
                                  await supabase.from('contributions').delete().eq('id', c['id']);
                                  _loadContributions();
                                },
                              ),
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
}
