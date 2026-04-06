<?php
$pageTitle = 'View Event';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!isset($_GET['id'])) { header("Location: events.php"); exit(); }
$eventId = intval($_GET['id']);

$stmt = $conn->prepare("
    SELECT e.*, u.full_name as owner_name
    FROM events e
    JOIN users u ON e.event_owner_id = u.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $eventId);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) { header("Location: events.php"); exit(); }

// Get statistics
$stats = $functions->getEventStats($eventId);

// Get guest list
$guestsStmt = $conn->prepare("SELECT * FROM invitations WHERE event_id = ? ORDER BY guest_name ASC");
$guestsStmt->bind_param("i", $eventId);
$guestsStmt->execute();
$guests = $guestsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get contributions list
$contribStmt = $conn->prepare("SELECT * FROM contributions WHERE event_id = ? ORDER BY created_at DESC");
$contribStmt->bind_param("i", $eventId);
$contribStmt->execute();
$contributions = $contribStmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight"><?php echo htmlspecialchars($event['event_name']); ?></h1>
            <p class="text-slate-500 font-medium">Event detailed overview and management</p>
        </div>
        <div class="flex gap-3">
            <a href="../api/reports.php?event_id=<?php echo $eventId; ?>&type=event" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-file-pdf"></i>
                <span>EVENT REPORT</span>
            </a>
            <a href="events.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>BACK</span>
            </a>
        </div>
    </header>

    <!-- Event Header Info -->
    <div class="bg-white rounded-[40px] p-8 md:p-12 border border-slate-200 shadow-sm mb-8">
        <div class="flex flex-col md:flex-row justify-between gap-8">
            <div class="flex-1 space-y-6">
                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 rounded-2xl bg-primary/10 text-primary text-xs font-black uppercase tracking-widest border border-primary/10">
                        <i class="fas fa-tag mr-2"></i><?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?>
                    </span>
                    <span class="px-4 py-2 rounded-2xl bg-blue-500/10 text-blue-500 text-xs font-black uppercase tracking-widest border border-blue-500/10">
                        <i class="fas fa-calendar mr-2"></i><?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                    </span>
                    <span class="px-4 py-2 rounded-2xl bg-green-500/10 text-green-500 text-xs font-black uppercase tracking-widest border border-green-500/10">
                        <i class="fas fa-map-marker-alt mr-2"></i><?php echo htmlspecialchars($event['location']); ?>
                    </span>
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Description</h4>
                    <p class="text-slate-600 font-medium leading-relaxed"><?php echo nl2br(htmlspecialchars($event['description'] ?: 'No description provided.')); ?></p>
                </div>
                <div class="pt-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Event Owner</div>
                        <div class="text-slate-900 font-bold"><?php echo htmlspecialchars($event['owner_name']); ?></div>
                    </div>
                </div>
            </div>
            <div class="md:w-64">
                <div class="p-6 rounded-3xl bg-slate-50 border border-slate-100 text-center">
                    <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Status</div>
                    <?php if(strtotime($event['event_date']) >= time()): ?>
                        <div class="text-green-500 font-black text-2xl uppercase tracking-tight">Upcoming</div>
                        <p class="text-slate-400 text-xs font-bold mt-2">Active Planning</p>
                    <?php else: ?>
                        <div class="text-slate-400 font-black text-2xl uppercase tracking-tight">Completed</div>
                        <p class="text-slate-400 text-xs font-bold mt-2">Past Event</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-primary shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Promised</div>
            <div class="text-3xl font-black text-slate-900">TZS <?php echo number_format($stats['contributions']['total_promised'] ?? 0); ?></div>
            <div class="text-xs font-bold text-green-500 mt-2 flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Paid: TZS <?php echo number_format($stats['contributions']['total_paid'] ?? 0); ?>
            </div>
        </div>
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-blue-500 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Confirmed Guests</div>
            <div class="text-3xl font-black text-slate-900"><?php echo $stats['invitations']['confirmed'] ?? 0; ?></div>
            <div class="text-xs font-bold text-slate-400 mt-2">Total Invited: <?php echo $stats['invitations']['total_invites'] ?? 0; ?></div>
        </div>
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-amber-500 shadow-sm">
            <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Attendance</div>
            <div class="text-3xl font-black text-slate-900"><?php echo $stats['attendance']['attended'] ?? 0; ?></div>
            <?php $rate = ($stats['invitations']['confirmed'] > 0) ? round(($stats['attendance']['attended'] / $stats['invitations']['confirmed']) * 100) : 0; ?>
            <div class="text-xs font-bold text-amber-500 mt-2 flex items-center gap-1">
                <i class="fas fa-users"></i> Check-in rate: <?php echo $rate; ?>%
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="flex border-b border-slate-100">
            <button onclick="switchTab(event, 'guests')" class="tab-btn px-8 py-6 font-black text-xs uppercase tracking-widest transition-all border-b-2 border-primary text-primary" id="defaultTab">Guest List</button>
            <button onclick="switchTab(event, 'contributions')" class="tab-btn px-8 py-6 font-black text-xs uppercase tracking-widest transition-all border-b-2 border-transparent text-slate-400 hover:text-slate-600">Contributions</button>
        </div>

        <div id="guests" class="tab-content p-8">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="pb-4">Guest Name</th>
                            <th class="pb-4">Phone</th>
                            <th class="pb-4 text-center">RSVP</th>
                            <th class="pb-4 text-center">Attendance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach($guests as $g):
                            $stmtAtt = $conn->prepare("SELECT status FROM attendance WHERE event_id = ? AND guest_name = ?");
                            $stmtAtt->bind_param("is", $eventId, $g['guest_name']);
                            $stmtAtt->execute();
                            $attStatus = $stmtAtt->get_result()->fetch_assoc()['status'] ?? 'N/A';
                        ?>
                        <tr>
                            <td class="py-4 font-bold text-slate-900"><?php echo htmlspecialchars($g['guest_name']); ?></td>
                            <td class="py-4 text-sm text-slate-500"><?php echo htmlspecialchars($g['phone_number']); ?></td>
                            <td class="py-4 text-center">
                                <span class="px-3 py-1 rounded-full <?php echo $g['status'] == 'approved' ? 'bg-green-500/10 text-green-500' : 'bg-amber-500/10 text-amber-500'; ?> text-[10px] font-black uppercase"><?php echo ucfirst($g['status']); ?></span>
                            </td>
                            <td class="py-4 text-center">
                                <span class="px-3 py-1 rounded-full <?php echo $attStatus == 'approved' ? 'bg-blue-500/10 text-blue-500' : 'bg-slate-100 text-slate-400'; ?> text-[10px] font-black uppercase"><?php echo ($attStatus == 'approved' ? 'Attended' : 'Absent'); ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($guests)): ?><tr><td colspan="4" class="py-12 text-center text-slate-400 font-medium italic">No guests added yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="contributions" class="tab-content p-8 hidden">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Contributions Details</h3>
                <div class="flex gap-2">
                    <button onclick="copyPhoneNumbers()" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-slate-200">
                        <i class="fas fa-copy"></i>
                        <span>COPY ALL PHONES</span>
                    </button>
                    <a href="../api/reports.php?event_id=<?php echo $eventId; ?>&type=event" class="bg-primary/10 hover:bg-primary text-primary hover:text-white px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center gap-2 border border-primary/20">
                        <i class="fas fa-file-pdf"></i>
                        <span>PDF REPORT</span>
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="contributionsTable">
                    <thead>
                        <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                            <th class="pb-4">Contributor</th>
                            <th class="pb-4">Phone Number</th>
                            <th class="pb-4 text-right">Promised</th>
                            <th class="pb-4 text-right">Paid</th>
                            <th class="pb-4 text-right">Balance</th>
                            <th class="pb-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach($contributions as $c):
                            $cBalance = $c['promised_amount'] - $c['paid_amount'];
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="py-4 font-bold text-slate-900"><?php echo htmlspecialchars($c['contributor_name']); ?></td>
                            <td class="py-4 text-sm text-slate-500 font-mono">
                                <?php if($c['phone_number']): ?>
                                    <span class="phone-number"><?php echo htmlspecialchars($c['phone_number']); ?></span>
                                <?php else: ?>
                                    <span class="text-slate-300">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 text-sm font-bold text-slate-700 text-right">TZS <?php echo number_format($c['promised_amount']); ?></td>
                            <td class="py-4 text-sm font-bold text-green-600 text-right">TZS <?php echo number_format($c['paid_amount']); ?></td>
                            <td class="py-4 text-sm font-bold <?php echo $cBalance > 0 ? 'text-red-500' : 'text-green-500'; ?> text-right">TZS <?php echo number_format($cBalance); ?></td>
                            <td class="py-4 text-center">
                                <?php if($c['status'] == 'approved'): ?>
                                    <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-tight border border-green-500/10">Cleared</span>
                                <?php else: ?>
                                    <span class="px-3 py-1 rounded-full bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-tight border border-amber-500/10">Pending</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($contributions)): ?><tr><td colspan="6" class="py-12 text-center text-slate-400 font-medium italic">No contributions recorded yet.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    function switchTab(event, tabId) {
        document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('border-primary', 'text-primary');
            b.classList.add('border-transparent', 'text-slate-400');
        });

        document.getElementById(tabId).classList.remove('hidden');
        event.currentTarget.classList.remove('border-transparent', 'text-slate-400');
        event.currentTarget.classList.add('border-primary', 'text-primary');
    }

    function copyPhoneNumbers() {
        const phoneElements = document.querySelectorAll('.phone-number');
        const phones = Array.from(phoneElements).map(el => el.innerText.trim()).filter(p => p !== 'N/A');
        const phoneString = phones.join(', ');

        if (phones.length === 0) {
            alert('No phone numbers found to copy.');
            return;
        }

        navigator.clipboard.writeText(phoneString).then(() => {
            alert(phones.length + ' phone numbers copied to clipboard!');
        }).catch(err => {
            console.error('Could not copy text: ', err);
            // Fallback for older browsers or non-secure contexts
            const textArea = document.createElement("textarea");
            textArea.value = phoneString;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                alert(phones.length + ' phone numbers copied to clipboard!');
            } catch (err) {
                alert('Failed to copy phone numbers.');
            }
            document.body.removeChild(textArea);
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>