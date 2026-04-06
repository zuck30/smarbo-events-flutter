<?php
$pageTitle = 'Event Posts';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Get events for dropdown
$eventsQuery = $conn->query("SELECT id, event_name FROM events ORDER BY event_date DESC");
$events = $eventsQuery->fetch_all(MYSQLI_ASSOC);

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_post'])) {
        $eventId = intval($_POST['event_id']);
        $title = sanitize($_POST['title'], $conn);
        $content = sanitize($_POST['content'], $conn);
        $postType = sanitize($_POST['post_type'], $conn);
        
        // Handle media uploads
        $media = [];
        if (isset($_FILES['media']) && !empty($_FILES['media']['name'][0])) {
            $media = $functions->handleMediaUpload($_FILES['media']);
        }
        
        $result = $functions->createEventPost($eventId, $title, $content, $postType, $user['id'], $media);
        
        if ($result['success']) {
            $_SESSION['success'] = 'Post created successfully!';
            header('Location: event-posts.php?event_id=' . $eventId);
            exit();
        } else {
            $_SESSION['error'] = $result['message'];
        }
    }
}

// Get selected event posts with pagination
$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$posts = [];
$totalPages = 1;

if ($selectedEventId > 0) {
    $postsData = $functions->getEventPosts($selectedEventId, $page);
    $posts = $postsData['posts'] ?? [];
    $totalPages = $postsData['total_pages'] ?? 1;
}
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Event Posts</h1>
            <p class="text-slate-500 font-medium">Manage announcements and media for events</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openCreateModal()" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-plus-circle"></i>
                <span>NEW POST</span>
            </button>
        </div>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <div class="glass p-6 rounded-[32px] mb-8">
        <form method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <label class="text-xs font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">Filter by Event:</label>
            <select name="event_id" onchange="this.form.submit()" class="w-full md:w-80 bg-white/50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all font-medium">
                <option value="">Choose an event...</option>
                <?php foreach ($events as $e): ?>
                <option value="<?php echo $e['id']; ?>" <?php echo $selectedEventId == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['event_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($selectedEventId > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <?php foreach ($posts as $p): ?>
            <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden group hover:border-primary/20 transition-all duration-300">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-6">
                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-wider">
                            <?php echo htmlspecialchars($p['post_type']); ?>
                        </span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <i class="far fa-calendar mr-1"></i> <?php echo date('M d, Y', strtotime($p['created_at'])); ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 mb-4 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($p['title']); ?></h3>
                    <p class="text-slate-500 text-sm font-medium line-clamp-3 mb-6"><?php echo strip_tags($p['content']); ?></p>
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <div class="flex items-center gap-2 text-xs font-black text-slate-400 uppercase">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo htmlspecialchars($p['posted_by_name']); ?></span>
                        </div>
                        <a href="view-post.php?id=<?php echo $p['id']; ?>" class="text-primary font-bold text-xs hover:underline flex items-center gap-1">
                            VIEW FULL <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($posts)): ?>
                <div class="col-span-full py-20 text-center glass rounded-[40px]">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 text-2xl mx-auto mb-4">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h4 class="text-slate-900 font-black uppercase">No posts yet</h4>
                    <p class="text-slate-500 font-medium">Create the first announcement for this event.</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="flex justify-center gap-2">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?event_id=<?php echo $selectedEventId; ?>&page=<?php echo $i; ?>" class="w-10 h-10 rounded-xl flex items-center justify-center font-bold transition-all <?php echo $page == $i ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-white border border-slate-200 text-slate-600 hover:border-primary/50'; ?>">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="py-32 text-center glass rounded-[40px]">
            <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 text-4xl mx-auto mb-6">
                <i class="fas fa-hand-pointer"></i>
            </div>
            <h2 class="text-2xl font-black text-slate-900 uppercase">Select an Event</h2>
            <p class="text-slate-500 font-medium">Choose an event from the dropdown to manage its posts.</p>
        </div>
    <?php endif; ?>
</main>

<!-- Create Post Modal -->
<div id="createPostModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[40px] w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-2xl font-black text-slate-900 uppercase">Create New Post</h3>
            <button onclick="closeCreateModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Select Event</label>
                    <select name="event_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all font-medium">
                        <?php foreach ($events as $e): ?>
                        <option value="<?php echo $e['id']; ?>" <?php echo $selectedEventId == $e['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($e['event_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Post Type</label>
                    <select name="post_type" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all font-medium">
                        <option value="update">Update</option>
                        <option value="photo">Photo Album</option>
                        <option value="video">Video</option>
                        <option value="announcement">Announcement</option>
                    </select>
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Post Title</label>
                    <input type="text" name="title" required maxlength="200" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Content</label>
                    <textarea name="content" rows="4" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all"></textarea>
                </div>
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Media Files</label>
                    <input type="file" name="media[]" multiple accept="image/*,video/*" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>
            </div>
            <div class="flex gap-4 pt-4">
                <button type="submit" name="create_post" class="flex-1 bg-primary hover:bg-primary-dark text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-primary/20 uppercase tracking-widest">PUBLISH POST</button>
                <button type="button" onclick="closeCreateModal()" class="px-8 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all uppercase tracking-widest">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createPostModal').classList.remove('hidden');
        document.getElementById('createPostModal').classList.add('flex');
    }
    function closeCreateModal() {
        document.getElementById('createPostModal').classList.add('hidden');
        document.getElementById('createPostModal').classList.remove('flex');
    }
</script>

<?php require_once 'includes/footer.php'; ?>