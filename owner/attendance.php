<?php
$pageTitle = 'Attendance';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get user's events
$eventsStmt = $conn->prepare("SELECT id, event_name FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$eventsStmt->bind_param("i", $user['id']);
$eventsStmt->execute();
$allEvents = $eventsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;
if ($selectedEventId === 0 && count($allEvents) > 0) $selectedEventId = $allEvents[0]['id'];

// Handle check-in
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_in'])) {
    $attendanceId = intval($_POST['attendance_id']);
    $stmt = $conn->prepare("UPDATE attendance SET status = 'approved', attended_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $attendanceId);
    $stmt->execute();
}
?>

<main class="flex-1 lg:ml-72 p-4 lg:p-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <button id="mobileMenuBtn" class="lg:hidden text-2xl p-2 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-3xl lg:text-4xl font-black mb-1">Attendance</h1>
                <p class="text-gray-400">Track guest arrival and check-ins</p>
            </div>
        </div>

        <select onchange="window.location.href='attendance.php?event_id=' + this.value" class="w-full lg:w-64 bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white">
            <?php foreach($allEvents as $e): ?>
            <option value="<?php echo $e['id']; ?>" <?php echo $selectedEventId == $e['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($e['event_name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($selectedEventId > 0): ?>
        <?php
        $attendanceStmt = $conn->prepare("SELECT * FROM attendance WHERE event_id = ? ORDER BY guest_name ASC");
        $attendanceStmt->bind_param("i", $selectedEventId);
        $attendanceStmt->execute();
        $records = $attendanceStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        ?>

        <div class="glass rounded-[32px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-white/5 border-b border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Guest Name</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Check-in Time</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach($records as $r): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($r['guest_name']); ?></td>
                        <td class="px-6 py-4">
                            <?php if($r['status'] == 'approved'): ?>
                                <span class="text-green-500 flex items-center gap-2">
                                    <i class="fas fa-check-circle"></i> Attended
                                </span>
                            <?php else: ?>
                                <span class="text-gray-500 italic">Not Checked-in</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-400">
                            <?php echo $r['attended_at'] ? date('H:i, M d', strtotime($r['attended_at'])) : '-'; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if($r['status'] != 'approved'): ?>
                                <form method="POST">
                                    <input type="hidden" name="attendance_id" value="<?php echo $r['id']; ?>">
                                    <button type="submit" name="check_in" class="px-4 py-2 rounded-lg bg-primary text-white text-xs font-bold uppercase transition-colors hover:bg-primary-dark">Check-in</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once 'includes/footer.php'; ?>