<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="hidden lg:flex fixed left-0 top-0 h-full w-20 hover:w-72 bg-[#1a1a2e] text-white transition-all duration-300 z-[100] overflow-hidden group flex-col border-r border-white/5 shadow-2xl">
    <div class="h-20 flex items-center px-6 mb-8 flex-shrink-0">
        <img src="../images/INVTS.png" alt="Logo" class="h-10 w-auto min-w-[40px] object-contain">
    </div>

    <div class="px-4 mb-8 flex-shrink-0">
        <div class="flex items-center gap-4 p-3 rounded-2xl bg-white/5 border border-white/10 overflow-hidden">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold text-lg flex-shrink-0">
                <?php echo strtoupper(substr($user['full_name'] ?? 'A', 0, 1)); ?>
            </div>
            <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                <h4 class="font-semibold text-sm truncate"><?php echo htmlspecialchars($user['full_name'] ?? 'Admin'); ?></h4>
                <p class="text-[10px] text-primary bg-primary/20 px-2 py-0.5 rounded-full inline-block uppercase tracking-tighter font-black">Administrator</p>
            </div>
        </div>
    </div>

    <nav class="flex-1 px-4 space-y-2 overflow-y-auto no-scrollbar">
        <a href="dashboard.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'dashboard.php' ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-home text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Dashboard</span>
        </a>

        <a href="events.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo ($current_page == 'events.php' || $current_page == 'view-event.php' || $current_page == 'create-event.php') ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-calendar-alt text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Events</span>
        </a>

        <a href="users.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo ($current_page == 'users.php' || $current_page == 'view-user.php' || $current_page == 'create-user.php') ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-users text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Users</span>
        </a>

        <a href="event-posts.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo ($current_page == 'event-posts.php' || $current_page == 'view-post.php') ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-newspaper text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Posts</span>
        </a>

        <a href="reports.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'reports.php' ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-chart-pie text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Reports</span>
        </a>

        <a href="settings.php" class="flex items-center gap-6 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'settings.php' ? 'bg-primary/15 text-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-cog text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Settings</span>
        </a>
    </nav>

    <div class="p-4 mt-auto border-t border-white/5 flex-shrink-0">
        <a href="../api/logout.php" class="flex items-center gap-6 px-4 py-3 rounded-xl text-gray-400 hover:text-red-500 hover:bg-red-500/10 transition-all">
            <i class="fas fa-sign-out-alt text-xl w-6 text-center"></i>
            <span class="font-bold uppercase tracking-widest text-xs opacity-0 group-hover:opacity-100 transition-opacity duration-300">Logout</span>
        </a>
    </div>
</aside>