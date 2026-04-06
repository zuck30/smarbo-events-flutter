<?php
// This is included in all owner pages via the header/dashboard
$user = $auth->getCurrentUser();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="fixed left-0 top-0 h-full w-72 bg-dark border-r border-white/10 transition-transform duration-300 transform -translate-x-full lg:translate-x-0 z-50">
    <div class="p-8">
        <a href="dashboard.php" class="flex items-center gap-3">
            <img src="../images/INVTS.png" alt="SmarboEvent" class="h-10">
        </a>
    </div>
    
    <div class="px-6 mb-8">
        <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center font-bold text-lg">
                <?php echo strtoupper(substr($user['full_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="overflow-hidden">
                <h4 class="font-semibold text-sm truncate"><?php echo htmlspecialchars($user['full_name'] ?? 'Event Owner'); ?></h4>
                <p class="text-xs text-gray-400 truncate"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            </div>
        </div>
    </div>
    
    <nav class="px-4 space-y-2">
        <a href="dashboard.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'dashboard.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-home w-5"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="events.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'events.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-calendar-alt w-5"></i>
            <span class="font-medium">My Events</span>
        </a>

        <a href="contributions.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'contributions.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-money-bill-wave w-5"></i>
            <span class="font-medium">Contributions</span>
        </a>

        <a href="invitations.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'invitations.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-users w-5"></i>
            <span class="font-medium">Invitations</span>
        </a>

        <a href="attendance.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'attendance.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-user-check w-5"></i>
            <span class="font-medium">Attendance</span>
        </a>

        <a href="reports.php" class="sidebar-link flex items-center gap-4 px-4 py-3 rounded-xl transition-all <?php echo $current_page == 'reports.php' ? 'active bg-primary/10 text-primary border-l-4 border-primary' : 'text-gray-400 hover:text-white hover:bg-white/5'; ?>">
            <i class="fas fa-chart-bar w-5"></i>
            <span class="font-medium">Reports</span>
        </a>
    </nav>
    
    <div class="absolute bottom-8 left-0 w-full px-6">
        <a href="../api/logout.php" class="flex items-center gap-4 px-4 py-3 rounded-xl text-gray-400 hover:text-white hover:bg-red-500/10 hover:text-red-500 transition-all">
            <i class="fas fa-sign-out-alt w-5"></i>
            <span class="font-medium">Logout</span>
        </a>
    </div>
</aside>