import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class OwnerDashboard extends StatefulWidget {
  const OwnerDashboard({super.key});

  @override
  State<OwnerDashboard> createState() => _OwnerDashboardState();
}

class _OwnerDashboardState extends State<OwnerDashboard> {
  final supabase = Supabase.instance.client;
  bool _isLoading = true;
  List<dynamic> _myEvents = [];
  Map<String, dynamic> _stats = {'total_events': 0, 'total_promised': 0, 'total_paid': 0};

  @override
  void initState() {
    super.initState();
    _loadDashboardData();
  }

  Future<void> _loadDashboardData() async {
    try {
      final user = supabase.auth.currentUser;
      if (user == null) return;

      final eventsRes = await supabase
          .from('events')
          .select()
          .eq('event_owner_id', user.id)
          .order('event_date', ascending: false);

      final eventsList = eventsRes as List;
      final eventIds = eventsList.map((e) => e['id']).toList();

      if (eventIds.isNotEmpty) {
        final contributionsRes = await supabase
            .from('contributions')
            .select('promised_amount, paid_amount')
            .inFilter('event_id', eventIds);

        double promisedTotal = 0;
        double paidTotal = 0;
        for (var c in contributionsRes) {
          promisedTotal += (c['promised_amount'] as num).toDouble();
          paidTotal += (c['paid_amount'] as num).toDouble();
        }

        setState(() {
          _myEvents = eventsList;
          _stats = {
            'total_events': eventsList.length,
            'total_promised': promisedTotal,
            'total_paid': paidTotal,
          };
          _isLoading = false;
        });
      } else {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('My Dashboard', style: TextStyle(fontWeight: FontWeight.w900)),
        actions: [
          IconButton(
            onPressed: () async {
              await supabase.auth.signOut();
              if (mounted) context.go('/login');
            },
            icon: const Icon(Icons.logout),
          ),
        ],
      ),
      drawer: Drawer(
        child: ListView(
          padding: EdgeInsets.zero,
          children: [
            const DrawerHeader(
              decoration: BoxDecoration(color: AppTheme.backgroundColor),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  Text('SmarboPlusEvent', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: AppTheme.primaryColor)),
                  Text('Event Owner', style: TextStyle(color: Colors.grey)),
                ],
              ),
            ),
            ListTile(
              leading: const Icon(Icons.dashboard),
              title: const Text('Dashboard'),
              onTap: () => Navigator.pop(context),
            ),
            ListTile(
              leading: const Icon(Icons.event),
              title: const Text('My Events'),
              onTap: () => context.push('/owner/events'),
            ),
            ListTile(
              leading: const Icon(Icons.check_circle),
              title: const Text('Attendance'),
              onTap: () => context.push('/owner/attendance'),
            ),
            ListTile(
              leading: const Icon(Icons.monetization_on),
              title: const Text('Contributions'),
              onTap: () => context.push('/owner/contributions'),
            ),
            ListTile(
              leading: const Icon(Icons.mail),
              title: const Text('Invitations'),
              onTap: () => context.push('/owner/invitations'),
            ),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text('Welcome Back!', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 24),
                  Row(
                    children: [
                      _buildStatBox('Total Events', _stats['total_events'].toString(), Colors.blue),
                      const SizedBox(width: 16),
                      _buildStatBox('Promised', 'TZS ${_stats['total_promised']}', Colors.green),
                    ],
                  ),
                  const SizedBox(height: 32),
                  const Text('My Events', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
                  const SizedBox(height: 16),
                  if (_myEvents.isEmpty)
                    const Center(child: Padding(padding: EdgeInsets.all(32.0), child: Text('No events found.')))
                  else
                    ListView.builder(
                      itemCount: _myEvents.length,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemBuilder: (context, index) {
                        final event = _myEvents[index];
                        return Card(
                          child: ListTile(
                            title: Text(event['event_name'], style: const TextStyle(fontWeight: FontWeight.bold)),
                            subtitle: Text(event['event_date'], style: const TextStyle(color: Colors.grey)),
                            onTap: () => context.push('/owner/events/${event['id']}'),
                          ),
                        );
                      },
                    ),
                ],
              ),
            ),
    );
  }

  Widget _buildStatBox(String label, String value, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: color.withOpacity(0.1),
          borderRadius: BorderRadius.circular(24),
          border: Border.all(color: color.withOpacity(0.2)),
        ),
        child: Column(
          children: [
            Text(value, style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: color)),
            const SizedBox(height: 4),
            Text(label, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.bold, color: Colors.grey)),
          ],
        ),
      ),
    );
  }
}
