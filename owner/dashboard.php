<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';
require_once 'sidebar.php';

// Get event owner's events
$stmt = $conn->prepare("SELECT * FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$events = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get total statistics for all events
$totalContributions = 0;
$totalPaid = 0;
$totalGuests = 0;
$totalConfirmed = 0;

foreach ($events as $event) {
    // Get contributions for this event
    $stmt = $conn->prepare("SELECT SUM(promised_amount) as total_promised, SUM(paid_amount) as total_paid FROM contributions WHERE event_id = ?");
    $stmt->bind_param("i", $event['id']);
    $stmt->execute();
    $contrib = $stmt->get_result()->fetch_assoc();
    $totalContributions += $contrib['total_promised'] ?? 0;
    $totalPaid += $contrib['total_paid'] ?? 0;
    
    // Get guests for this event
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM invitations WHERE event_id = ?");
    $stmt->bind_param("i", $event['id']);
    $stmt->execute();
    $guests = $stmt->get_result()->fetch_assoc();
    $totalGuests += $guests['total'] ?? 0;
    
    // Get confirmed guests
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM invitations WHERE event_id = ? AND status = 'approved'");
    $stmt->bind_param("i", $event['id']);
    $stmt->execute();
    $confirmed = $stmt->get_result()->fetch_assoc();
    $totalConfirmed += $confirmed['total'] ?? 0;
}

$totalBalance = $totalContributions - $totalPaid;
?>

<main class="flex-1 lg:ml-72 p-4 lg:p-8">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div class="flex items-center gap-4">
            <button id="mobileMenuBtn" class="lg:hidden text-2xl p-2 rounded-xl bg-white/5 hover:bg-white/10 transition-colors">
                <i class="fas fa-bars"></i>
            </button>
            <div>
                <h1 class="text-3xl lg:text-4xl font-black mb-1">Dashboard</h1>
                <p class="text-gray-400">Welcome back, <?php echo htmlspecialchars($user['full_name'] ?? 'Event Owner'); ?>!</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="reports.php" class="flex-1 lg:flex-none flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 transition-all font-semibold">
                <i class="fas fa-chart-line"></i>
                <span>View Reports</span>
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-12">
        <div class="glass p-6 rounded-[32px] relative overflow-hidden group hover:border-primary/50 transition-all duration-300">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-orange-400"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar"></i>
                </div>
                <span class="text-gray-400 font-medium">Total Events</span>
            </div>
            <div class="text-4xl font-black mb-1"><?php echo count($events); ?></div>
            <div class="text-sm text-gray-500">Active management</div>
        </div>

        <div class="glass p-6 rounded-[32px] relative overflow-hidden group hover:border-primary/50 transition-all duration-300">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-emerald-400"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 text-green-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <span class="text-gray-400 font-medium">Total Paid</span>
            </div>
            <div class="text-4xl font-black mb-1">TZS <?php echo number_format($totalPaid); ?></div>
            <div class="flex items-center gap-2 text-sm text-green-500">
                <i class="fas fa-arrow-up"></i>
                <span>TZS <?php echo number_format($totalContributions); ?> total</span>
            </div>
        </div>

        <div class="glass p-6 rounded-[32px] relative overflow-hidden group hover:border-primary/50 transition-all duration-300">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-rose-400"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="text-gray-400 font-medium">Balance Due</span>
            </div>
            <div class="text-4xl font-black mb-1">TZS <?php echo number_format($totalBalance); ?></div>
            <div class="flex items-center gap-2 text-sm <?php echo $totalBalance > 0 ? 'text-red-500' : 'text-green-500'; ?>">
                <i class="fas fa-<?php echo $totalBalance > 0 ? 'exclamation-circle' : 'check-circle'; ?>"></i>
                <span><?php echo $totalBalance > 0 ? 'Pending collection' : 'Fully collected'; ?></span>
            </div>
        </div>

        <div class="glass p-6 rounded-[32px] relative overflow-hidden group hover:border-primary/50 transition-all duration-300">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-sky-400"></div>
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-gray-400 font-medium">Total Guests</span>
            </div>
            <div class="text-4xl font-black mb-1"><?php echo $totalGuests; ?></div>
            <div class="flex items-center gap-2 text-sm text-blue-500">
                <i class="fas fa-check-double"></i>
                <span><?php echo $totalConfirmed; ?> confirmed</span>
            </div>
        </div>
    </div>

    <!-- Events Section -->
    <div class="mb-12">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-bold">Recent Events</h2>
            <a href="events.php" class="text-primary hover:text-primary-dark font-semibold flex items-center gap-2 transition-colors">
                <span>View All</span>
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php if (count($events) > 0): ?>
                <?php foreach (array_slice($events, 0, 4) as $event): ?>
                    <?php
                    // Get event statistics
                    $stmt = $conn->prepare("
                        SELECT
                            SUM(promised_amount) as total_promised,
                            SUM(paid_amount) as total_paid,
                            COUNT(DISTINCT i.id) as guests_count,
                            COUNT(DISTINCT CASE WHEN i.status = 'approved' THEN i.id END) as confirmed_count
                        FROM events e
                        LEFT JOIN contributions c ON e.id = c.event_id
                        LEFT JOIN invitations i ON e.id = i.event_id
                        WHERE e.id = ?
                    ");
                    $stmt->bind_param("i", $event['id']);
                    $stmt->execute();
                    $stats = $stmt->get_result()->fetch_assoc();

                    $percentPaid = ($stats['total_promised'] ?? 0) > 0 ? round(($stats['total_paid'] / $stats['total_promised']) * 100) : 0;
                    ?>

                    <div class="glass p-8 rounded-[40px] group hover:border-primary/30 transition-all duration-300">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-400">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <?php echo date('M d, Y', strtotime($event['event_date'])); ?>
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                        <?php echo htmlspecialchars($event['location']); ?>
                                    </span>
                                </div>
                            </div>
                            <span class="px-4 py-1.5 rounded-full bg-primary/10 text-primary text-xs font-bold uppercase tracking-wider">
                                <?php echo htmlspecialchars($event['event_type']); ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div class="bg-white/5 p-4 rounded-2xl text-center">
                                <div class="text-xs text-gray-500 uppercase font-bold mb-1 tracking-wider">Promised</div>
                                <div class="font-bold text-sm lg:text-base">TZS <?php echo number_format($stats['total_promised'] ?? 0); ?></div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl text-center">
                                <div class="text-xs text-gray-500 uppercase font-bold mb-1 tracking-wider">Paid</div>
                                <div class="font-bold text-sm lg:text-base">TZS <?php echo number_format($stats['total_paid'] ?? 0); ?></div>
                            </div>
                            <div class="bg-white/5 p-4 rounded-2xl text-center">
                                <div class="text-xs text-gray-500 uppercase font-bold mb-1 tracking-wider">Guests</div>
                                <div class="font-bold text-sm lg:text-base"><?php echo $stats['confirmed_count'] ?? 0; ?>/<?php echo $stats['guests_count'] ?? 0; ?></div>
                            </div>
                        </div>

                        <div class="mb-8">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400 font-medium">Contribution Progress</span>
                                <span class="text-primary font-bold"><?php echo $percentPaid; ?>%</span>
                            </div>
                            <div class="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full bg-primary transition-all duration-1000" style="width: <?php echo $percentPaid; ?>%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-3">
                            <a href="contributions.php?event_id=<?php echo $event['id']; ?>" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-white transition-all text-xs font-bold">
                                <i class="fas fa-money-bill"></i>
                                <span>PAYMENTS</span>
                            </a>
                            <a href="invitations.php?event_id=<?php echo $event['id']; ?>" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all text-xs font-bold">
                                <i class="fas fa-users"></i>
                                <span>GUESTS</span>
                            </a>
                            <a href="attendance.php?event_id=<?php echo $event['id']; ?>" class="flex items-center justify-center gap-2 py-3 rounded-xl bg-white/5 hover:bg-white/10 transition-all text-xs font-bold">
                                <i class="fas fa-user-check"></i>
                                <span>RSVP</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-20 glass rounded-[40px] text-center">
                    <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-4xl text-gray-600 mx-auto mb-6">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-2 text-gray-300">No Events Assigned</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">You haven't been assigned to any events yet. Please contact the administrator for access.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>