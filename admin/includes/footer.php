<?php
// admin/includes/footer.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
        <!-- Mobile Bottom Navigation -->
        <nav class="lg:hidden fixed bottom-0 left-0 right-0 h-20 bg-white border-t border-slate-200 flex items-center justify-around px-2 z-[100] shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
            <a href="dashboard.php" class="flex flex-col items-center justify-center gap-1 w-full h-full <?php echo $current_page == 'dashboard.php' ? 'text-primary' : 'text-slate-400'; ?>">
                <i class="fas fa-home text-xl"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Home</span>
            </a>
            <a href="events.php" class="flex flex-col items-center justify-center gap-1 w-full h-full <?php echo ($current_page == 'events.php' || $current_page == 'view-event.php' || $current_page == 'create-event.php') ? 'text-primary' : 'text-slate-400'; ?>">
                <i class="fas fa-calendar-alt text-xl"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Events</span>
            </a>
            <a href="users.php" class="flex flex-col items-center justify-center gap-1 w-full h-full <?php echo ($current_page == 'users.php' || $current_page == 'create-user.php') ? 'text-primary' : 'text-slate-400'; ?>">
                <i class="fas fa-users text-xl"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Users</span>
            </a>
            <a href="reports.php" class="flex flex-col items-center justify-center gap-1 w-full h-full <?php echo $current_page == 'reports.php' ? 'text-primary' : 'text-slate-400'; ?>">
                <i class="fas fa-chart-pie text-xl"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Reports</span>
            </a>
            <a href="settings.php" class="flex flex-col items-center justify-center gap-1 w-full h-full <?php echo $current_page == 'settings.php' ? 'text-primary' : 'text-slate-400'; ?>">
                <i class="fas fa-cog text-xl"></i>
                <span class="text-[9px] font-black uppercase tracking-tighter">Settings</span>
            </a>
        </nav>
    </div> <!-- End flex container -->

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.classList.add('opacity-0', '-translate-y-2');
                    alert.classList.add('transition-all', 'duration-500');
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
</body>
</html>
