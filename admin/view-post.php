<?php
$pageTitle = 'View Post';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Get post ID
$postId = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($postId === 0) { header('Location: event-posts.php'); exit(); }

// Get post details
$stmt = $conn->prepare("
    SELECT ep.*, e.event_name, e.event_date, e.location, 
           u.full_name as posted_by_name
    FROM event_posts ep
    JOIN events e ON ep.event_id = e.id
    JOIN users u ON ep.posted_by = u.id
    WHERE ep.id = ?
");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header('Location: event-posts.php'); exit(); }
$post = $result->fetch_assoc();
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">View Post</h1>
            <p class="text-slate-500 font-medium">Discussion and updates for <span class="text-primary font-bold"><?php echo htmlspecialchars($post['event_name']); ?></span></p>
        </div>
        <div class="flex gap-3">
            <a href="event-posts.php?event_id=<?php echo $post['event_id']; ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>BACK TO POSTS</span>
            </a>
        </div>
    </header>

    <div class="max-w-4xl mx-auto">
        <article class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden mb-8">
            <!-- Post Header -->
            <div class="p-8 md:p-12 border-b border-slate-50 bg-slate-50/50">
                <div class="flex items-center gap-4 mb-6">
                    <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest">
                        <?php echo htmlspecialchars($post['post_type']); ?>
                    </span>
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <i class="far fa-calendar"></i>
                        <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-slate-900 leading-tight mb-8"><?php echo htmlspecialchars($post['title']); ?></h1>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-primary font-black">
                        <?php echo strtoupper(substr($post['posted_by_name'], 0, 1)); ?>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Published By</div>
                        <div class="text-slate-900 font-bold"><?php echo htmlspecialchars($post['posted_by_name']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Post Content -->
            <div class="p-8 md:p-12">
                <div class="prose prose-slate max-w-none text-slate-600 font-medium leading-relaxed text-lg">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>
            </div>

            <!-- Post Metadata -->
            <div class="p-8 md:px-12 md:py-8 bg-slate-50 border-t border-slate-100 flex flex-wrap gap-8">
                <div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Related Event</div>
                    <a href="view-event.php?id=<?php echo $post['event_id']; ?>" class="text-primary font-bold hover:underline"><?php echo htmlspecialchars($post['event_name']); ?></a>
                </div>
                <div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Event Date</div>
                    <div class="text-slate-700 font-bold"><?php echo date('M d, Y', strtotime($post['event_date'])); ?></div>
                </div>
                <div>
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Location</div>
                    <div class="text-slate-700 font-bold"><?php echo htmlspecialchars($post['location']); ?></div>
                </div>
            </div>
        </article>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>