<?php
$pageTitle = 'Admin Dashboard';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Recent events
$recentEvents = $conn->query("
    SELECT e.*, u.full_name as owner_name 
    FROM events e 
    JOIN users u ON e.event_owner_id = u.id 
    ORDER BY e.created_at DESC 
    LIMIT 5
");

// Upcoming events
$upcomingEvents = $conn->query("
    SELECT e.*, u.full_name as owner_name 
    FROM events e 
    JOIN users u ON e.event_owner_id = u.id 
    WHERE e.event_date >= CURDATE() 
    ORDER BY e.event_date ASC 
    LIMIT 5
");

// Recent posts
$recentPosts = $conn->query("
    SELECT ep.*, e.event_name, u.full_name as posted_by_name 
    FROM event_posts ep
    JOIN events e ON ep.event_id = e.id
    JOIN users u ON ep.posted_by = u.id
    ORDER BY ep.created_at DESC 
    LIMIT 5
");
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">System Overview</h1>
            <p class="text-slate-500 font-medium">Welcome back, <span class="text-primary font-bold"><?php echo htmlspecialchars($user['full_name']); ?></span>!</p>
        </div>
        <div class="flex gap-3">
            <a href="create-event.php" class="flex-1 md:flex-none bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-plus"></i>
                <span>NEW EVENT</span>
            </a>
            <a href="reports.php" class="flex-1 md:flex-none bg-white hover:bg-slate-50 text-slate-700 px-6 py-3 rounded-2xl font-bold transition-all flex items-center justify-center gap-2 border border-slate-200 shadow-sm">
                <i class="fas fa-file-pdf"></i>
                <span>REPORTS</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-primary shadow-sm group hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-4 mb-4 text-primary">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="font-bold text-[10px] uppercase tracking-widest text-slate-400">Total Events</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['total_events']; ?></div>
        </div>

        <div class="glass p-6 rounded-[32px] border-b-4 border-b-blue-500 shadow-sm group hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-4 mb-4 text-blue-500">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-users"></i>
                </div>
                <span class="font-bold text-[10px] uppercase tracking-widest text-slate-400">Event Owners</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['total_owners']; ?></div>
        </div>

        <div class="glass p-6 rounded-[32px] border-b-4 border-b-green-500 shadow-sm group hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-4 mb-4 text-green-500">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-handshake"></i>
                </div>
                <span class="font-bold text-[10px] uppercase tracking-widest text-slate-400">Promised (TZS)</span>
            </div>
            <div class="text-2xl xl:text-3xl font-black text-slate-900 truncate"><?php echo number_format($stats['total_promised']); ?></div>
        </div>

        <div class="glass p-6 rounded-[32px] border-b-4 border-b-amber-500 shadow-sm group hover:scale-[1.02] transition-transform">
            <div class="flex items-center gap-4 mb-4 text-amber-500">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-credit-card"></i>
                </div>
                <span class="font-bold text-[10px] uppercase tracking-widest text-slate-400">Paid (TZS)</span>
            </div>
            <div class="text-2xl xl:text-3xl font-black text-slate-900 truncate"><?php echo number_format($stats['total_paid']); ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        <!-- Left Column -->
        <div class="space-y-8">
            <!-- Recent Events -->
            <div class="bg-white rounded-[40px] p-6 md:p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tight">Recent Events</h2>
                    <a href="events.php" class="text-primary font-bold text-xs hover:underline uppercase tracking-widest">VIEW ALL</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left min-w-[400px]">
                        <thead class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="pb-4">Event Name</th>
                                <th class="pb-4">Type</th>
                                <th class="pb-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php while($event = $recentEvents->fetch_assoc()): ?>
                            <tr class="group">
                                <td class="py-4">
                                    <div class="font-bold text-slate-900 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo date('M d, Y', strtotime($event['event_date'])); ?></div>
                                </td>
                                <td class="py-4 text-sm font-medium text-slate-600">
                                    <?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?>
                                </td>
                                <td class="py-4 text-center">
                                    <?php if($event['event_date'] < date('Y-m-d')): ?>
                                        <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase">Past</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase">Live</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="bg-white rounded-[40px] p-6 md:p-8 border border-slate-200 shadow-sm">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tight">Recent Posts</h2>
                    <a href="event-posts.php" class="text-primary font-bold text-xs hover:underline uppercase tracking-widest">VIEW ALL</a>
                </div>
                <div class="space-y-4">
                    <?php if ($recentPosts->num_rows > 0): ?>
                        <?php while($post = $recentPosts->fetch_assoc()): ?>
                        <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 group hover:border-primary/20 transition-all">
                            <div class="overflow-hidden pr-4">
                                <div class="font-bold text-slate-900 truncate"><?php echo htmlspecialchars($post['title']); ?></div>
                                <div class="text-[10px] text-slate-500 font-bold uppercase truncate"><?php echo htmlspecialchars($post['event_name']); ?> • <?php echo htmlspecialchars($post['posted_by_name']); ?></div>
                            </div>
                            <a href="view-post.php?id=<?php echo $post['id']; ?>" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors shadow-sm flex-shrink-0">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center py-8 text-slate-400 italic font-medium">No recent posts found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="bg-white rounded-[40px] p-6 md:p-8 border border-slate-200 shadow-sm h-fit">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tight">Countdown</h2>
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Schedule</span>
            </div>
            <div class="space-y-6">
                <?php while($event = $upcomingEvents->fetch_assoc()):
                    $daysLeft = floor((strtotime($event['event_date']) - time()) / (60 * 60 * 24));
                    $daysText = $daysLeft <= 0 ? 'Today' : $daysLeft . ' days';
                    $colorClass = $daysLeft <= 3 ? 'text-red-500 bg-red-50' : 'text-primary bg-primary/10';
                ?>
                <div class="flex items-center gap-6 p-4 md:p-6 rounded-[32px] border border-slate-100 bg-slate-50/50 group hover:border-primary/20 transition-all">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-[24px] flex flex-col items-center justify-center <?php echo $colorClass; ?> flex-shrink-0 transition-transform group-hover:scale-110">
                        <div class="text-xl md:text-2xl font-black leading-none"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                        <div class="text-[8px] md:text-[10px] font-black uppercase tracking-widest mt-1"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                    </div>
                    <div class="flex-1 overflow-hidden">
                        <h4 class="font-black text-slate-900 truncate md:text-lg"><?php echo htmlspecialchars($event['event_name']); ?></h4>
                        <div class="text-xs md:text-sm text-slate-500 font-medium mb-2"><?php echo htmlspecialchars($event['owner_name']); ?></div>
                        <div class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-widest <?php echo $daysLeft <= 3 ? 'text-red-500' : 'text-primary'; ?>">
                            <i class="fas fa-clock"></i>
                            <span>In <?php echo $daysText; ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php if ($upcomingEvents->num_rows == 0): ?>
                    <p class="text-center py-8 text-slate-400 italic font-medium">No upcoming events scheduled.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>