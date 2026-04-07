import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/domain/entities/event.dart';

class EventDetailsScreen extends StatelessWidget {
  final Event event;

  const EventDetailsScreen({super.key, required this.event});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 250,
            pinned: true,
            flexibleSpace: FlexibleSpaceBar(
              title: Text(event.eventName, style: const TextStyle(fontWeight: FontWeight.w900, color: Colors.white)),
              background: event.coverImage != null
                  ? Image.network(event.coverImage!, fit: BoxFit.cover)
                  : Container(color: AppTheme.primaryColor),
            ),
          ),
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        decoration: BoxDecoration(
                          color: AppTheme.primaryColor.withOpacity(0.1),
                          borderRadius: BorderRadius.circular(50),
                        ),
                        child: Text(
                          event.eventType.toUpperCase(),
                          style: const TextStyle(color: AppTheme.primaryColor, fontWeight: FontWeight.w900, fontSize: 12),
                        ),
                      ),
                      const Spacer(),
                      Text(
                        DateFormat('MMMM d, y').format(event.eventDate),
                        style: const TextStyle(fontWeight: FontWeight.bold, color: Colors.grey),
                      ),
                    ],
                  ),
                  const SizedBox(height: 32),
                  const Text('Location', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.grey)),
                  const SizedBox(height: 8),
                  Text(event.location, style: const TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
                  const SizedBox(height: 32),
                  const Text('Description', style: TextStyle(fontSize: 12, fontWeight: FontWeight.w900, color: Colors.grey)),
                  const SizedBox(height: 8),
                  Text(
                    event.description ?? 'No description provided.',
                    style: const TextStyle(fontSize: 16, color: Colors.grey),
                  ),
                  const SizedBox(height: 48),
                  const Divider(color: Colors.white10),
                  const SizedBox(height: 24),
                  // Additional event details would go here (stats, posts, etc.)
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
