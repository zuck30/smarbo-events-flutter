import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/domain/entities/event_post.dart';

class PostDetailsScreen extends StatefulWidget {
  final EventPost post;

  const PostDetailsScreen({super.key, required this.post});

  @override
  State<PostDetailsScreen> createState() => _PostDetailsScreenState();
}

class _PostDetailsScreenState extends State<PostDetailsScreen> {
  final _commentController = TextEditingController();

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Post Details', style: TextStyle(fontWeight: FontWeight.w900))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  backgroundImage: widget.post.posterAvatar != null
                      ? NetworkImage(widget.post.posterAvatar!)
                      : null,
                  child: widget.post.posterAvatar == null ? const Icon(Icons.person) : null,
                ),
                const SizedBox(width: 12),
                Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(widget.post.posterName ?? 'Unknown', style: const TextStyle(fontWeight: FontWeight.bold)),
                    Text(
                      DateFormat('MMM d, y, HH:mm').format(widget.post.createdAt),
                      style: const TextStyle(fontSize: 12, color: Colors.grey),
                    ),
                  ],
                ),
              ],
            ),
            const SizedBox(height: 24),
            Text(
              widget.post.title,
              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900, color: Colors.white),
            ),
            const SizedBox(height: 16),
            if (widget.post.mediaUrl != null)
              ClipRRect(
                borderRadius: BorderRadius.circular(24),
                child: Image.network(widget.post.mediaUrl!, width: double.infinity, fit: BoxFit.cover),
              ),
            const SizedBox(height: 24),
            Text(
              widget.post.content ?? '',
              style: const TextStyle(fontSize: 16, color: Colors.grey),
            ),
            const SizedBox(height: 32),
            const Divider(color: Colors.white10),
            Row(
              children: [
                IconButton(onPressed: () {}, icon: const Icon(Icons.thumb_up_alt_outlined)),
                Text('${widget.post.likesCount} Likes'),
                const SizedBox(width: 24),
                IconButton(onPressed: () {}, icon: const Icon(Icons.comment_outlined)),
                Text('${widget.post.commentsCount} Comments'),
              ],
            ),
            const SizedBox(height: 32),
            const Text('Comments', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
            const SizedBox(height: 24),
            // Comments list would go here
            TextField(
              controller: _commentController,
              decoration: InputDecoration(
                hintText: 'Add a comment...',
                suffixIcon: IconButton(onPressed: () {}, icon: const Icon(Icons.send, color: AppTheme.primaryColor)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
