import 'package:flutter/material.dart';
import 'package:supabase_flutter/supabase_flutter.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';

class AdminUsersScreen extends StatefulWidget {
  const AdminUsersScreen({super.key});

  @override
  State<AdminUsersScreen> createState() => _AdminUsersScreenState();
}

class _AdminUsersScreenState extends State<AdminUsersScreen> {
  final supabase = Supabase.instance.client;
  List<dynamic> _users = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadUsers();
  }

  Future<void> _loadUsers() async {
    try {
      final response = await supabase.from('profiles').select().order('created_at', ascending: false);
      setState(() {
        _users = response;
        _isLoading = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _showCreateUserDialog() {
    final emailController = TextEditingController();
    final passwordController = TextEditingController();
    final usernameController = TextEditingController();
    final fullNameController = TextEditingController();
    String selectedRole = 'event_owner';

    showDialog(
      context: context,
      builder: (context) => StatefulBuilder(
        builder: (context, setDialogState) => AlertDialog(
          title: const Text('Create New User'),
          content: SingleChildScrollView(
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                TextField(controller: fullNameController, decoration: const InputDecoration(labelText: 'Full Name')),
                TextField(controller: usernameController, decoration: const InputDecoration(labelText: 'Username')),
                TextField(controller: emailController, decoration: const InputDecoration(labelText: 'Email')),
                TextField(controller: passwordController, decoration: const InputDecoration(labelText: 'Password'), obscureText: true),
                DropdownButtonFormField<String>(
                  value: selectedRole,
                  items: ['admin', 'event_owner'].map((r) => DropdownMenuItem(value: r, child: Text(r.toUpperCase()))).toList(),
                  onChanged: (val) => setDialogState(() => selectedRole = val!),
                  decoration: const InputDecoration(labelText: 'Role'),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.pop(context), child: const Text('CANCEL')),
            ElevatedButton(
              onPressed: () async {
                if (emailController.text.isEmpty || passwordController.text.isEmpty) return;
                try {
                  await supabase.auth.signUp(
                    email: emailController.text.trim(),
                    password: passwordController.text.trim(),
                    data: {
                      'username': usernameController.text.trim(),
                      'full_name': fullNameController.text.trim(),
                      'role': selectedRole,
                    },
                  );
                  if (mounted) {
                    Navigator.pop(context);
                    _loadUsers();
                  }
                } catch (e) {
                  if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(e.toString()), backgroundColor: Colors.red));
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
      appBar: AppBar(title: const Text('Manage Users', style: TextStyle(fontWeight: FontWeight.w900))),
      floatingActionButton: FloatingActionButton(
        onPressed: _showCreateUserDialog,
        backgroundColor: AppTheme.primaryColor,
        child: const Icon(Icons.add, color: Colors.white),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : RefreshIndicator(
              onRefresh: _loadUsers,
              child: ListView.builder(
                itemCount: _users.length,
                padding: const EdgeInsets.all(24.0),
                itemBuilder: (context, index) {
                  final user = _users[index];
                  return Card(
                    child: ListTile(
                      leading: CircleAvatar(
                        backgroundImage: user['avatar_url'] != null ? NetworkImage(user['avatar_url']) : null,
                        child: user['avatar_url'] == null ? const Icon(Icons.person) : null,
                      ),
                      title: Text(user['full_name'] ?? user['username'], style: const TextStyle(fontWeight: FontWeight.bold)),
                      subtitle: Text(user['role'].toString().toUpperCase(), style: const TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: Colors.grey)),
                      trailing: user['role'] != 'admin' ? IconButton(
                        icon: const Icon(Icons.delete, color: Colors.red),
                        onPressed: () async {
                          final confirm = await showDialog<bool>(
                            context: context,
                            builder: (context) => AlertDialog(
                              title: const Text('Delete User'),
                              content: const Text('Are you sure you want to delete this user?'),
                              actions: [
                                TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('CANCEL')),
                                TextButton(onPressed: () => Navigator.pop(context, true), child: const Text('DELETE', style: TextStyle(color: Colors.red))),
                              ],
                            ),
                          );
                          if (confirm == true) {
                            // Deleting a user in auth is only possible via admin SDK or direct SQL
                            // For now, we will mark as inactive in profiles or delete profile (RLS handles access)
                            await supabase.from('profiles').delete().eq('id', user['id']);
                            _loadUsers();
                          }
                        },
                      ) : null,
                    ),
                  );
                },
              ),
            ),
    );
  }
}
