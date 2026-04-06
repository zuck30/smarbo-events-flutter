<?php
$pageTitle = 'Reports & Analytics';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle PDF export
if (isset($_GET['export']) && $_GET['export'] === 'pdf') {
    $pdf = $functions->generatePDFReport('admin');
    $pdf->Output('SmarboPlusevent_report_' . date('Ymd_His') . '.pdf', 'D');
    exit();
}

// Get events for dropdown
$events = $conn->query("SELECT id, event_name FROM events ORDER BY event_date DESC");

// Get report statistics
$statsQuery = $conn->query("
    SELECT 
        COUNT(*) as total_events,
        SUM(event_date >= CURDATE()) as upcoming_events,
        SUM(event_date < CURDATE()) as completed_events,
        (SELECT COUNT(*) FROM users) as total_users,
        (SELECT SUM(paid_amount) FROM contributions) as total_paid,
        (SELECT SUM(promised_amount) FROM contributions) as total_promised
    FROM events
");
$stats = $statsQuery->fetch_assoc();
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Reports & Analytics</h1>
            <p class="text-slate-500 font-medium">Generate detailed reports and export data</p>
        </div>
        <div class="flex gap-3">
            <a href="../api/reports.php?type=admin" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-file-pdf"></i>
                <span>EXPORT FULL REPORT</span>
            </a>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-primary shadow-sm">
            <div class="flex items-center gap-4 mb-4 text-primary">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="font-bold text-sm uppercase tracking-widest text-slate-400">Total Events</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['total_events']; ?></div>
        </div>
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-blue-500 shadow-sm">
            <div class="flex items-center gap-4 mb-4 text-blue-500">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-users"></i>
                </div>
                <span class="font-bold text-sm uppercase tracking-widest text-slate-400">Total Users</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['total_users']; ?></div>
        </div>
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-green-500 shadow-sm">
            <div class="flex items-center gap-4 mb-4 text-green-500">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <span class="font-bold text-sm uppercase tracking-widest text-slate-400">Paid (TZS)</span>
            </div>
            <div class="text-3xl font-black text-slate-900"><?php echo number_format($stats['total_paid'] ?? 0); ?></div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Event Report</h3>
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-xl">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <form method="GET" action="../api/reports.php" class="space-y-6">
                <input type="hidden" name="type" value="event">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Select Event</label>
                    <select name="event_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all font-medium">
                        <?php while($e = $events->fetch_assoc()): ?>
                        <option value="<?php echo $e['id']; ?>"><?php echo htmlspecialchars($e['event_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    GENERATE PDF
                </button>
            </form>
        </div>

        <div class="bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">System Export</h3>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fas fa-database"></i>
                </div>
            </div>
            <p class="text-slate-500 mb-8 font-medium">Export all system data including users, events, and financial summaries into a single comprehensive PDF document.</p>
            <a href="../api/reports.php?type=admin" class="w-full bg-slate-900 hover:bg-black text-white py-4 rounded-2xl font-bold transition-all shadow-lg flex items-center justify-center gap-2">
                <i class="fas fa-download"></i>
                DOWNLOAD MASTER DATA
            </a>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>