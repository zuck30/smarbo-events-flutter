import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:smarbo_events/core/theme/app_theme.dart';
import 'package:smarbo_events/domain/entities/event_post.dart';
import 'package:supabase_flutter/supabase_flutter.dart';

class PostDetailsScreen extends StatefulWidget {
  final EventPost post;

  const PostDetailsScreen({super.key, required this.post});

  @override
  State<PostDetailsScreen> createState() => _PostDetailsScreenState();
}

class _PostDetailsScreenState extends State<PostDetailsScreen> {
  final _commentController = TextEditingController();
  final supabase = Supabase.instance.client;
  List<dynamic> _comments = [];
  bool _isLoadingComments = true;
  int _likesCount = 0;

  @override
  void initState() {
    super.initState();
    _likesCount = widget.post.likesCount;
    _loadComments();
  }

  Future<void> _loadComments() async {
    try {
      final response = await supabase
          .from('event_post_comments')
          .select('*, profiles(full_name, avatar_url)')
          .eq('post_id', widget.post.id)
          .order('created_at', ascending: true);

      setState(() {
        _comments = response;
        _isLoadingComments = false;
      });
    } catch (e) {
      if (mounted) setState(() => _isLoadingComments = false);
    }
  }

  Future<void> _toggleLike() async {
    try {
      final userId = supabase.auth.currentUser!.id;
      final existing = await supabase
          .from('event_post_likes')
          .select()
          .eq('post_id', widget.post.id)
          .eq('user_id', userId)
          .maybeSingle();

      if (existing != null) {
        await supabase.from('event_post_likes').delete().eq('id', existing['id']);
        setState(() => _likesCount--);
      } else {
        await supabase.from('event_post_likes').insert({'post_id': widget.post.id, 'user_id': userId});
        setState(() => _likesCount++);
      }
    } catch (e) {
      // Handle error
    }
  }

  Future<void> _postComment() async {
    if (_commentController.text.isEmpty) return;
    try {
      await supabase.from('event_post_comments').insert({
        'post_id': widget.post.id,
        'user_id': supabase.auth.currentUser!.id,
        'comment': _commentController.text,
      });
      _commentController.clear();
      _loadComments();
    } catch (e) {
      // Handle error
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Post Details', style: TextStyle(fontWeight: FontWeight.w900))),
      body: Column(
        children: [
          Expanded(
            child: SingleChildScrollView(
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
                      IconButton(onPressed: _toggleLike, icon: const Icon(Icons.thumb_up_alt_outlined)),
                      Text('$_likesCount Likes'),
                      const SizedBox(width: 24),
                      const Icon(Icons.comment_outlined, color: Colors.grey),
                      const SizedBox(width: 8),
                      Text('${_comments.length} Comments'),
                    ],
                  ),
                  const SizedBox(height: 32),
                  const Text('Comments', style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900, color: Colors.white)),
                  const SizedBox(height: 24),
                  if (_isLoadingComments)
                    const Center(child: CircularProgressIndicator())
                  else
                    ListView.builder(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      itemCount: _comments.length,
                      itemBuilder: (context, index) {
                        final comment = _comments[index];
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 16.0),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              CircleAvatar(
                                radius: 16,
                                backgroundImage: comment['profiles']?['avatar_url'] != null ? NetworkImage(comment['profiles']['avatar_url']) : null,
                                child: comment['profiles']?['avatar_url'] == null ? const Icon(Icons.person, size: 16) : null,
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(comment['profiles']?['full_name'] ?? 'Unknown', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 12)),
                                    Text(comment['comment'], style: const TextStyle(color: Colors.grey, fontSize: 14)),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
                ],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.all(16.0),
            child: TextField(
              controller: _commentController,
              decoration: InputDecoration(
                hintText: 'Add a comment...',
                suffixIcon: IconButton(onPressed: _postComment, icon: const Icon(Icons.send, color: AppTheme.primaryColor)),
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(16)),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
