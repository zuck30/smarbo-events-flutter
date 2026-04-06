<?php
$pageTitle = 'Reports';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get user's events
$eventsStmt = $conn->prepare("SELECT id, event_name, event_date, event_type FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$eventsStmt->bind_param("i", $user['id']);
$eventsStmt->execute();
$events = $eventsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="flex-1 lg:ml-72 p-4 lg:p-8">
    <div class="mb-8">
        <h1 class="text-3xl lg:text-4xl font-black mb-1">Reports</h1>
        <p class="text-gray-400">Download and view summaries of your events</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($events as $e): ?>
            <?php
            // Get stats
            $stmt = $conn->prepare("
                SELECT
                    (SELECT SUM(promised_amount) FROM contributions WHERE event_id = ?) as promised,
                    (SELECT SUM(paid_amount) FROM contributions WHERE event_id = ?) as paid,
                    (SELECT COUNT(*) FROM invitations WHERE event_id = ?) as guests,
                    (SELECT COUNT(*) FROM invitations WHERE event_id = ? AND status = 'approved') as confirmed
            ");
            $stmt->bind_param("iiii", $e['id'], $e['id'], $e['id'], $e['id']);
            $stmt->execute();
            $stats = $stmt->get_result()->fetch_assoc();
            ?>
            <div class="glass p-6 rounded-[32px] flex flex-col">
                <div class="mb-4">
                    <h3 class="text-xl font-bold mb-1"><?php echo htmlspecialchars($e['event_name']); ?></h3>
                    <p class="text-xs text-primary font-bold uppercase tracking-widest"><?php echo date('M d, Y', strtotime($e['event_date'])); ?></p>
                </div>

                <div class="space-y-3 mb-6 flex-1">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Total Promised</span>
                        <span class="font-bold">TZS <?php echo number_format($stats['promised'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between text-sm text-green-500">
                        <span>Total Paid</span>
                        <span class="font-bold">TZS <?php echo number_format($stats['paid'] ?? 0); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">RSVP Rate</span>
                        <span class="font-bold"><?php echo ($stats['guests'] > 0) ? round(($stats['confirmed']/$stats['guests'])*100) : 0; ?>%</span>
                    </div>
                </div>

                <a href="../api/reports.php?event_id=<?php echo $e['id']; ?>&type=event" class="w-full py-4 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 flex items-center justify-center gap-3 transition-all font-bold">
                    <i class="fas fa-file-pdf text-red-500"></i>
                    <span>Download PDF Report</span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>