<?php
$pageTitle = 'Invitations';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get user's events for the selector
$eventsStmt = $conn->prepare("SELECT id, event_name FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$eventsStmt->bind_param("i", $user['id']);
$eventsStmt->execute();
$allEvents = $eventsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get selected event
$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Security check
if ($selectedEventId > 0) {
    $checkStmt = $conn->prepare("SELECT id FROM events WHERE id = ? AND event_owner_id = ?");
    $checkStmt->bind_param("ii", $selectedEventId, $user['id']);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows === 0) {
        $selectedEventId = 0;
    }
}

if ($selectedEventId === 0 && count($allEvents) > 0) {
    $selectedEventId = $allEvents[0]['id'];
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $invitationId = intval($_POST['invitation_id']);
    $newStatus = sanitize($_POST['status'], $conn);

    // Verify ownership
    $stmt = $conn->prepare("
        SELECT i.id FROM invitations i
        JOIN events e ON i.event_id = e.id
        WHERE i.id = ? AND e.event_owner_id = ?
    ");
    $stmt->bind_param("ii", $invitationId, $user['id']);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $updateStmt = $conn->prepare("UPDATE invitations SET status = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newStatus, $invitationId);
        $updateStmt->execute();
        $successMsg = "Invitation status updated.";
    }
}
?>

<main class="flex-1 lg:ml-72 p-4 lg:p-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <button id="mobileMenuBtn" class="lg:hidden text-2xl p-2 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-3xl lg:text-4xl font-black mb-1">Invitations</h1>
                <p class="text-gray-400">Manage guest list and RSVPs</p>
            </div>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto">
            <select onchange="window.location.href='invitations.php?event_id=' + this.value" class="w-full lg:w-64 bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white font-medium">
                <?php foreach($allEvents as $e): ?>
                <option value="<?php echo $e['id']; ?>" <?php echo $selectedEventId == $e['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($e['event_name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <?php if ($selectedEventId > 0): ?>
        <?php
        $invitesStmt = $conn->prepare("SELECT * FROM invitations WHERE event_id = ? ORDER BY guest_name ASC");
        $invitesStmt->bind_param("i", $selectedEventId);
        $invitesStmt->execute();
        $invites = $invitesStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $statsStmt = $conn->prepare("
            SELECT
                COUNT(*) as total,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'disapproved' THEN 1 ELSE 0 END) as declined
            FROM invitations WHERE event_id = ?
        ");
        $statsStmt->bind_param("i", $selectedEventId);
        $statsStmt->execute();
        $stats = $statsStmt->get_result()->fetch_assoc();
        ?>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div class="glass p-4 rounded-2xl text-center">
                <div class="text-gray-400 text-xs font-bold uppercase mb-1 tracking-widest">Total</div>
                <div class="text-2xl font-black"><?php echo $stats['total'] ?? 0; ?></div>
            </div>
            <div class="glass p-4 rounded-2xl text-center">
                <div class="text-green-500 text-xs font-bold uppercase mb-1 tracking-widest">Approved</div>
                <div class="text-2xl font-black"><?php echo $stats['approved'] ?? 0; ?></div>
            </div>
            <div class="glass p-4 rounded-2xl text-center">
                <div class="text-yellow-500 text-xs font-bold uppercase mb-1 tracking-widest">Pending</div>
                <div class="text-2xl font-black"><?php echo $stats['pending'] ?? 0; ?></div>
            </div>
            <div class="glass p-4 rounded-2xl text-center">
                <div class="text-red-500 text-xs font-bold uppercase mb-1 tracking-widest">Declined</div>
                <div class="text-2xl font-black"><?php echo $stats['declined'] ?? 0; ?></div>
            </div>
        </div>

        <div class="glass rounded-[32px] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                    <tr class="bg-white/5 border-b border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Guest Name</th>
                        <th class="px-6 py-4">Phone Number</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php foreach($invites as $invite): ?>
                    <tr class="hover:bg-white/[0.02] transition-colors">
                        <td class="px-6 py-4 font-bold"><?php echo htmlspecialchars($invite['guest_name']); ?></td>
                        <td class="px-6 py-4 text-gray-400"><?php echo htmlspecialchars($invite['phone_number']); ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if($invite['status'] == 'approved'): ?>
                                <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-tighter border border-green-500/20">Approved</span>
                            <?php elseif($invite['status'] == 'disapproved'): ?>
                                <span class="px-3 py-1 rounded-full bg-red-500/10 text-red-500 text-[10px] font-black uppercase tracking-tighter border border-red-500/20">Declined</span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-[10px] font-black uppercase tracking-tighter border border-yellow-500/20">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form method="POST" class="inline-flex gap-1">
                                <input type="hidden" name="invitation_id" value="<?php echo $invite['id']; ?>">
                                <button type="submit" name="update_status" value="1" onclick="this.form.status.value='approved'" class="p-2 rounded-lg bg-white/5 hover:bg-green-500/20 text-green-500 transition-colors">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="submit" name="update_status" value="1" onclick="this.form.status.value='disapproved'" class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 text-red-500 transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                                <input type="hidden" name="status" value="">
                            </form>
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