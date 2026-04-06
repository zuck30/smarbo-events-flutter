<?php
$pageTitle = 'Contributions';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get user's events for the selector
$eventsStmt = $conn->prepare("SELECT id, event_name FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$eventsStmt->bind_param("i", $user['id']);
$eventsStmt->execute();
$allEvents = $eventsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get selected event
$selectedEventId = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

// Security check for selected event
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

// Handle contribution actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_contribution'])) {
        $contributorName = sanitize($_POST['contributor_name'], $conn);
        $phoneNumber = sanitize($_POST['phone_number'], $conn);
        $promisedAmount = floatval($_POST['promised_amount']);
        $paidAmount = floatval($_POST['paid_amount']);
        $status = ($paidAmount >= $promisedAmount) ? 'approved' : 'pending';

        $stmt = $conn->prepare("INSERT INTO contributions (event_id, contributor_name, phone_number, promised_amount, paid_amount, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdds", $selectedEventId, $contributorName, $phoneNumber, $promisedAmount, $paidAmount, $status);

        if ($stmt->execute()) {
            $successMsg = 'Contribution added successfully';
        } else {
            $errorMsg = 'Failed to add contribution';
        }
    }

    if (isset($_POST['update_contribution'])) {
        $contributionId = intval($_POST['contribution_id']);
        $contributorName = sanitize($_POST['contributor_name'], $conn);
        $phoneNumber = sanitize($_POST['phone_number'], $conn);
        $promisedAmount = floatval($_POST['promised_amount']);
        $paidAmount = floatval($_POST['paid_amount']);
        $status = ($paidAmount >= $promisedAmount) ? 'approved' : 'pending';

        // Verify event ownership
        $stmt = $conn->prepare("
            SELECT c.id FROM contributions c
            JOIN events e ON c.event_id = e.id
            WHERE c.id = ? AND e.event_owner_id = ?
        ");
        $stmt->bind_param("ii", $contributionId, $user['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $updateStmt = $conn->prepare("UPDATE contributions SET contributor_name = ?, phone_number = ?, promised_amount = ?, paid_amount = ?, status = ? WHERE id = ?");
            $updateStmt->bind_param("ssddsi", $contributorName, $phoneNumber, $promisedAmount, $paidAmount, $status, $contributionId);
            if ($updateStmt->execute()) {
                $successMsg = 'Contribution updated successfully';
            } else {
                $errorMsg = 'Failed to update contribution';
            }
        }
    }

    if (isset($_POST['delete_contribution'])) {
        $contributionId = intval($_POST['contribution_id']);
        // Verify event ownership
        $stmt = $conn->prepare("
            SELECT c.id FROM contributions c
            JOIN events e ON c.event_id = e.id
            WHERE c.id = ? AND e.event_owner_id = ?
        ");
        $stmt->bind_param("ii", $contributionId, $user['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $deleteStmt = $conn->prepare("DELETE FROM contributions WHERE id = ?");
            $deleteStmt->bind_param("i", $contributionId);
            if ($deleteStmt->execute()) {
                $successMsg = 'Contribution deleted successfully';
            } else {
                $errorMsg = 'Failed to delete contribution';
            }
        }
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
                <h1 class="text-3xl lg:text-4xl font-black mb-1">Contributions</h1>
                <p class="text-gray-400">Track and manage event contributions</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <select onchange="window.location.href='contributions.php?event_id=' + this.value" class="w-full sm:w-64 bg-white/5 border border-white/10 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white font-medium">
                <option value="" disabled <?php echo !$selectedEventId ? 'selected' : ''; ?>>Select an event</option>
                <?php foreach($allEvents as $e): ?>
                <option value="<?php echo $e['id']; ?>" <?php echo $selectedEventId == $e['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($e['event_name']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if ($selectedEventId > 0): ?>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="../api/reports.php?event_id=<?php echo $selectedEventId; ?>&type=event" class="flex-1 sm:flex-none bg-white/5 hover:bg-white/10 border border-white/10 text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-file-pdf text-red-500"></i>
                    <span>REPORT</span>
                </a>
                <button onclick="openModal()" class="flex-1 sm:flex-none bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>ADD NEW</span>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($successMsg)): ?>
        <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 flex items-center gap-3">
            <i class="fas fa-check-circle"></i>
            <?php echo $successMsg; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($errorMsg)): ?>
        <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $errorMsg; ?>
        </div>
    <?php endif; ?>

    <?php if ($selectedEventId > 0): ?>
        <?php
        $contributionsStmt = $conn->prepare("SELECT * FROM contributions WHERE event_id = ? ORDER BY created_at DESC");
        $contributionsStmt->bind_param("i", $selectedEventId);
        $contributionsStmt->execute();
        $contributions = $contributionsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        $summaryStmt = $conn->prepare("SELECT SUM(promised_amount) as total_promised, SUM(paid_amount) as total_paid, COUNT(*) as total_count, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as completed FROM contributions WHERE event_id = ?");
        $summaryStmt->bind_param("i", $selectedEventId);
        $summaryStmt->execute();
        $summary = $summaryStmt->get_result()->fetch_assoc();

        $totalPromised = $summary['total_promised'] ?? 0;
        $totalPaid = $summary['total_paid'] ?? 0;
        $balance = $totalPromised - $totalPaid;
        ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="glass p-6 rounded-3xl">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Promised</div>
                <div class="text-2xl font-black">TZS <?php echo number_format($totalPromised); ?></div>
            </div>
            <div class="glass p-6 rounded-3xl">
                <div class="text-gray-400 text-sm font-medium mb-1">Total Paid</div>
                <div class="text-2xl font-black text-green-500">TZS <?php echo number_format($totalPaid); ?></div>
            </div>
            <div class="glass p-6 rounded-3xl">
                <div class="text-gray-400 text-sm font-medium mb-1">Balance</div>
                <div class="text-2xl font-black text-red-500">TZS <?php echo number_format($balance); ?></div>
            </div>
            <div class="glass p-6 rounded-3xl">
                <div class="text-gray-400 text-sm font-medium mb-1">Completion</div>
                <div class="text-2xl font-black"><?php echo $summary['completed'] ?? 0; ?> / <?php echo $summary['total_count'] ?? 0; ?></div>
            </div>
        </div>

        <div class="glass rounded-[32px] overflow-hidden">
            <div class="overflow-x-auto min-w-full">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-xs font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4 whitespace-nowrap">Contributor</th>
                            <th class="px-6 py-4 whitespace-nowrap">Contact</th>
                            <th class="px-6 py-4 whitespace-nowrap">Promised</th>
                            <th class="px-6 py-4 whitespace-nowrap">Paid</th>
                            <th class="px-6 py-4 whitespace-nowrap">Balance</th>
                            <th class="px-6 py-4 whitespace-nowrap">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        <?php foreach($contributions as $c):
                            $cBalance = $c['promised_amount'] - $c['paid_amount'];
                        ?>
                        <tr class="hover:bg-white/[0.02] transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold"><?php echo htmlspecialchars($c['contributor_name']); ?></div>
                                <div class="text-[10px] text-gray-500 lg:hidden"><?php echo htmlspecialchars($c['phone_number']); ?></div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-400 hidden lg:table-cell"><?php echo htmlspecialchars($c['phone_number']); ?></td>
                            <td class="px-6 py-4 text-sm font-bold whitespace-nowrap">TZS <?php echo number_format($c['promised_amount']); ?></td>
                            <td class="px-6 py-4 text-sm font-bold text-green-500 whitespace-nowrap">TZS <?php echo number_format($c['paid_amount']); ?></td>
                            <td class="px-6 py-4 text-sm font-bold <?php echo $cBalance > 0 ? 'text-red-500' : 'text-green-500'; ?> whitespace-nowrap">
                                TZS <?php echo number_format($cBalance); ?>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($c['status'] == 'approved'): ?>
                                    <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-wider border border-green-500/20">Cleared</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-[10px] font-black uppercase tracking-wider border border-yellow-500/20">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex justify-end gap-2">
                                    <button onclick="editContribution(<?php echo htmlspecialchars(json_encode($c)); ?>)" class="p-2 rounded-lg bg-white/5 hover:bg-primary/20 text-primary transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteContribution(<?php echo $c['id']; ?>)" class="p-2 rounded-lg bg-white/5 hover:bg-red-500/20 text-red-500 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($contributions)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">No contributions found for this event.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="glass p-20 rounded-[40px] text-center">
            <div class="text-4xl text-gray-600 mb-4 italic">Please select an event to view contributions.</div>
        </div>
    <?php endif; ?>
</main>

<!-- Contribution Modal -->
<div id="contributionModal" class="fixed inset-0 bg-dark/80 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="glass rounded-[40px] w-full max-w-lg overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 border-b border-white/10 flex items-center justify-between">
            <h3 id="modalTitle" class="text-2xl font-black uppercase tracking-tight">Add Contribution</h3>
            <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center text-gray-400 hover:text-white transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" id="contributionForm" class="p-8">
            <input type="hidden" name="contribution_id" id="contribution_id">
            <div class="space-y-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Contributor Name</label>
                    <input type="text" name="contributor_name" id="contributor_name" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Phone Number</label>
                    <input type="text" name="phone_number" id="phone_number" class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Promised Amount</label>
                        <input type="number" name="promised_amount" id="promised_amount" step="0.01" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-gray-400 uppercase tracking-widest">Paid Amount</label>
                        <input type="number" name="paid_amount" id="paid_amount" step="0.01" value="0" required class="w-full bg-white/5 border border-white/10 rounded-2xl px-4 py-3 focus:outline-none focus:border-primary transition-all text-white">
                    </div>
                </div>
            </div>
            <div class="flex gap-4 mt-8">
                <button type="submit" id="submitBtn" name="add_contribution" class="flex-1 bg-primary hover:bg-primary-dark text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-primary/20">SAVE CONTRIBUTION</button>
                <button type="button" onclick="closeModal()" class="px-8 bg-white/5 text-gray-400 rounded-2xl font-bold hover:bg-white/10 transition-all">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('contributionForm').reset();
        document.getElementById('modalTitle').innerText = 'Add Contribution';
        document.getElementById('submitBtn').name = 'add_contribution';
        document.getElementById('contribution_id').value = '';
        document.getElementById('contributionModal').classList.remove('hidden');
        document.getElementById('contributionModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('contributionModal').classList.add('hidden');
        document.getElementById('contributionModal').classList.remove('flex');
    }

    function editContribution(c) {
        document.getElementById('contribution_id').value = c.id;
        document.getElementById('contributor_name').value = c.contributor_name;
        document.getElementById('phone_number').value = c.phone_number;
        document.getElementById('promised_amount').value = c.promised_amount;
        document.getElementById('paid_amount').value = c.paid_amount;

        document.getElementById('modalTitle').innerText = 'Edit Contribution';
        document.getElementById('submitBtn').name = 'update_contribution';
        document.getElementById('contributionModal').classList.remove('hidden');
        document.getElementById('contributionModal').classList.add('flex');
    }

    function deleteContribution(id) {
        if(confirm('Are you sure you want to delete this contribution?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="contribution_id" value="${id}">
                <input type="hidden" name="delete_contribution" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>