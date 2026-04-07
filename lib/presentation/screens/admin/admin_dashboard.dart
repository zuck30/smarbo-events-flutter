import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/core/utils/report_generator.dart';
import 'package:smarbo_events/data/models/event_model.dart';

class AdminDashboard extends StatefulWidget {
  const AdminDashboard({super.key});

  @override
  State<AdminDashboard> createState() => _AdminDashboardState();
}

class _AdminDashboardState extends State<AdminDashboard> {
  final supabase = Supabase.instance.client;
  bool _isLoading = true;
  Map<String, dynamic> _stats = {};

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    try {
      final totalEventsRes = await supabase.from('events').select('*').count();
      final totalOwnersRes = await supabase.from('profiles').select('*').eq('role', 'event_owner').count();

      final totalPromisedRes = await supabase.rpc('sum_promised_amount');
      final totalPaidRes = await supabase.rpc('sum_paid_amount');

      setState(() {
        _stats = {
          'total_events': totalEventsRes.count,
          'total_owners': totalOwnersRes.count,
          'total_promised': (totalPromisedRes as num).toDouble(),
          'total_paid': (totalPaidRes as num).toDouble(),
        };
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _downloadAdminReport() async {
    try {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Downloading admin report...')));

      final eventsRes = await supabase
          .from('events')
          .select('*, profiles(full_name)')
          .order('event_date', ascending: false);

      final events = (eventsRes as List)
          .map((json) => EventModel.fromJson(json))
          .toList();

      await ReportGenerator.generateAdminReport(
        totalEvents: _stats['total_events'],
        totalOwners: _stats['total_owners'],
        totalPromised: _stats['total_promised'],
        totalPaid: _stats['total_paid'],
        events: events,
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
      appBar: AppBar(
        title: const Text('Admin Dashboard', style: TextStyle(fontWeight: FontWeight.w900)),
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
                  Text('Admin Panel', style: TextStyle(color: Colors.grey)),
                ],
              ),
            ),
            ListTile(
              leading: const Icon(Icons.dashboard),
              title: const Text('Dashboard'),
              onTap: () => Navigator.pop(context),
            ),
            ListTile(
              leading: const Icon(Icons.people),
              title: const Text('Manage Users'),
              onTap: () => context.push('/admin/users'),
            ),
            ListTile(
              leading: const Icon(Icons.event),
              title: const Text('Manage Events'),
              onTap: () => context.push('/admin/events'),
            ),
            ListTile(
              leading: const Icon(Icons.settings),
              title: const Text('Settings'),
              onTap: () => context.push('/admin/settings'),
            ),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadStats,
              child: SingleChildScrollView(
                padding: const EdgeInsets.all(24.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    const Text('System Overview', style: TextStyle(fontSize: 32, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 24),
                    GridView.count(
                      crossAxisCount: MediaQuery.of(context).size.width > 600 ? 4 : 2,
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      crossAxisSpacing: 16,
                      mainAxisSpacing: 16,
                      children: [
                        _buildStatCard('Events', _stats['total_events'].toString(), Icons.calendar_today, Colors.blue),
                        _buildStatCard('Owners', _stats['total_owners'].toString(), Icons.people, Colors.green),
                        _buildStatCard('Promised', 'TZS ${_stats['total_promised']}', Icons.attach_money, Colors.orange),
                        _buildStatCard('Paid', 'TZS ${_stats['total_paid']}', Icons.credit_card, Colors.amber),
                      ],
                    ),
                    const SizedBox(height: 48),
                    const Text('Quick Actions', style: TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        _buildActionBtn('New User', Icons.person_add, () => context.push('/admin/users')),
                        const SizedBox(width: 16),
                        _buildActionBtn('New Event', Icons.add, () => context.push('/admin/events')),
                        const SizedBox(width: 16),
                        _buildActionBtn('Report', Icons.file_present, _downloadAdminReport),
                      ],
                    ),
                  ],
                ),
              ),
            ),
    );
  }

  Widget _buildStatCard(String label, String value, IconData icon, Color color) {
    return Card(
      elevation: 0,
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(icon, color: color, size: 32),
            const SizedBox(height: 12),
            Text(value, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.w900), textAlign: TextAlign.center),
            Text(label, style: const TextStyle(fontSize: 10, color: Colors.grey)),
          ],
        ),
      ),
    );
  }

  Widget _buildActionBtn(String label, IconData icon, VoidCallback onTap) {
    return Expanded(
      child: ElevatedButton.icon(
        onPressed: onTap,
        icon: Icon(icon),
        label: Text(label),
      ),
    );
  }
}
