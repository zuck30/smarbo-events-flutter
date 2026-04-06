<?php
$pageTitle = 'My Events';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get events with detailed statistics
$stmt = $conn->prepare("
    SELECT 
        e.*,
        (SELECT COUNT(*) FROM contributions c WHERE c.event_id = e.id) as contributions_count,
        (SELECT SUM(promised_amount) FROM contributions c WHERE c.event_id = e.id) as total_promised,
        (SELECT SUM(paid_amount) FROM contributions c WHERE c.event_id = e.id) as total_paid,
        (SELECT COUNT(*) FROM invitations i WHERE i.event_id = e.id) as guests_count,
        (SELECT COUNT(*) FROM invitations i WHERE i.event_id = e.id AND i.status = 'approved') as confirmed_count
    FROM events e
    WHERE e.event_owner_id = ?
    ORDER BY e.event_date DESC
");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<main class="flex-1 lg:ml-72 p-4 lg:p-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <button id="mobileMenuBtn" class="lg:hidden text-2xl p-2 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-3xl lg:text-4xl font-black mb-1">My Events</h1>
                <p class="text-gray-400">Manage and view all your assigned events</p>
            </div>
        </div>
    </div>

    <div class="glass rounded-[32px] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white/5 border-b border-white/10">
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Event Details</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Type & Location</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Contributions</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Guests</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-sm font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    <?php if (count($events) > 0): ?>
                        <?php foreach ($events as $event): ?>
                            <?php
                            $eventDate = new DateTime($event['event_date']);
                            $today = new DateTime();
                            $isUpcoming = $eventDate >= $today;
                            ?>
                            <tr class="hover:bg-white/[0.02] transition-colors group">
                                <td class="px-6 py-6">
                                    <div class="font-bold text-lg mb-1"><?php echo htmlspecialchars($event['event_name']); ?></div>
                                    <div class="text-sm text-primary flex items-center gap-2">
                                        <i class="fas fa-calendar-day"></i>
                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6">
                                    <div class="flex items-center gap-2 text-sm font-semibold mb-1">
                                        <span class="w-2 h-2 rounded-full bg-primary"></span>
                                        <?php echo ucfirst($event['event_type']); ?>
                                    </div>
                                    <div class="text-sm text-gray-400 flex items-center gap-2">
                                        <i class="fas fa-location-dot text-xs"></i>
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-6 text-sm">
                                    <div class="font-bold">TZS <?php echo number_format($event['total_promised'] ?? 0); ?></div>
                                    <div class="text-green-500 font-medium">Paid: TZS <?php echo number_format($event['total_paid'] ?? 0); ?></div>
                                </td>
                                <td class="px-6 py-6 text-sm">
                                    <div class="font-bold"><?php echo $event['guests_count']; ?> Invited</div>
                                    <div class="text-blue-500 font-medium"><?php echo $event['confirmed_count']; ?> Confirmed</div>
                                </td>
                                <td class="px-6 py-6">
                                    <?php if ($isUpcoming): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/10 text-blue-500 text-xs font-bold uppercase tracking-wider border border-blue-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                                            Upcoming
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-xs font-bold uppercase tracking-wider border border-green-500/20">
                                            <i class="fas fa-check-circle"></i>
                                            Completed
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="contributions.php?event_id=<?php echo $event['id']; ?>" class="p-2 rounded-xl bg-white/5 hover:bg-primary hover:text-white text-gray-400 transition-all" title="Contributions">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </a>
                                        <a href="invitations.php?event_id=<?php echo $event['id']; ?>" class="p-2 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 transition-all" title="Invitations">
                                            <i class="fas fa-users"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <div class="text-gray-500 italic">No events found.</div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>