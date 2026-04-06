<?php
$pageTitle = 'View User';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

if (!isset($_GET['id'])) { header("Location: users.php"); exit(); }
$userId = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$viewUser = $stmt->get_result()->fetch_assoc();
if (!$viewUser) { header("Location: users.php"); exit(); }

$eventsStmt = $conn->prepare("SELECT * FROM events WHERE event_owner_id = ? ORDER BY event_date DESC");
$eventsStmt->bind_param("i", $userId);
$eventsStmt->execute();
$userEvents = $eventsStmt->get_result();
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">User Profile</h1>
            <p class="text-slate-500 font-medium">Viewing account details for <span class="text-primary font-bold"><?php echo htmlspecialchars($viewUser['full_name']); ?></span></p>
        </div>
        <a href="users.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>BACK TO USERS</span>
        </a>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Header Card -->
        <div class="lg:col-span-3 bg-white rounded-[40px] p-8 md:p-12 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center gap-8 md:gap-12">
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-[48px] bg-primary/10 text-primary flex items-center justify-center text-5xl md:text-6xl font-black shadow-inner">
                <?php echo strtoupper(substr($viewUser['full_name'], 0, 1)); ?>
            </div>
            <div class="text-center md:text-left flex-1">
                <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 mb-4">
                    <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight"><?php echo htmlspecialchars($viewUser['full_name']); ?></h2>
                    <span class="px-4 py-2 rounded-2xl bg-blue-500/10 text-blue-500 text-xs font-black uppercase tracking-widest border border-blue-500/10"><?php echo ucfirst($viewUser['role']); ?></span>
                    <?php if($viewUser['is_active']): ?>
                        <span class="px-4 py-2 rounded-2xl bg-green-500/10 text-green-500 text-xs font-black uppercase tracking-widest border border-green-500/10">Active Account</span>
                    <?php else: ?>
                        <span class="px-4 py-2 rounded-2xl bg-slate-100 text-slate-400 text-xs font-black uppercase tracking-widest border border-slate-100">Deactivated</span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-wrap justify-center md:justify-start gap-8">
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Username</div>
                        <div class="text-slate-700 font-bold text-lg">@<?php echo htmlspecialchars($viewUser['username']); ?></div>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Member Since</div>
                        <div class="text-slate-700 font-bold text-lg"><?php echo date('M d, Y', strtotime($viewUser['created_at'])); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-8">Contact Information</h3>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Email Address</div>
                        <div class="text-slate-900 font-bold"><?php echo htmlspecialchars($viewUser['email']); ?></div>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Phone Number</div>
                        <div class="text-slate-900 font-bold"><?php echo htmlspecialchars($viewUser['phone'] ?: 'Not Provided'); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Events -->
        <div class="lg:col-span-2 bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Assigned Events</h3>
                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase"><?php echo $userEvents->num_rows; ?> Total</span>
            </div>

            <div class="space-y-4">
                <?php if($userEvents->num_rows > 0): ?>
                    <?php while($e = $userEvents->fetch_assoc()): ?>
                        <div class="flex items-center justify-between p-6 rounded-3xl bg-slate-50 border border-slate-100 group hover:border-primary/20 transition-all">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-primary font-black shadow-sm">
                                    <i class="fas fa-calendar-star"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900"><?php echo htmlspecialchars($e['event_name']); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo date('M d, Y', strtotime($e['event_date'])); ?> • <?php echo htmlspecialchars($e['location']); ?></div>
                                </div>
                            </div>
                            <a href="view-event.php?id=<?php echo $e['id']; ?>" class="w-10 h-10 rounded-full bg-white flex items-center justify-center text-slate-400 group-hover:text-primary transition-colors shadow-sm">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="py-12 text-center">
                        <p class="text-slate-400 font-medium italic">This user hasn't been assigned to any events yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>