<?php
$pageTitle = 'Events Management';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_event'])) {
        $eventName = sanitize($_POST['event_name'], $conn);
        $eventType = sanitize($_POST['event_type'], $conn);
        $eventDate = sanitize($_POST['event_date'], $conn);
        $location = sanitize($_POST['location'], $conn);
        $description = sanitize($_POST['description'], $conn);
        $eventOwnerId = intval($_POST['event_owner_id']);
        
        $stmt = $conn->prepare("INSERT INTO events (event_name, event_type, event_date, location, description, event_owner_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssii", $eventName, $eventType, $eventDate, $location, $description, $eventOwnerId, $user['id']);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Event created successfully!';
            header('Location: events.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to create event: ' . $stmt->error;
        }
    }
    
    if (isset($_POST['update_event'])) {
        $eventId = intval($_POST['event_id']);
        $eventName = sanitize($_POST['event_name'], $conn);
        $eventType = sanitize($_POST['event_type'], $conn);
        $eventDate = sanitize($_POST['event_date'], $conn);
        $location = sanitize($_POST['location'], $conn);
        $description = sanitize($_POST['description'], $conn);
        $eventOwnerId = intval($_POST['event_owner_id']);
        
        $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_type = ?, event_date = ?, location = ?, description = ?, event_owner_id = ? WHERE id = ?");
        $stmt->bind_param("sssssii", $eventName, $eventType, $eventDate, $location, $description, $eventOwnerId, $eventId);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Event updated successfully!';
            header('Location: events.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to update event: ' . $stmt->error;
        }
    }
    
    if (isset($_POST['delete_event'])) {
        $eventId = intval($_POST['event_id']);
        $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
        $stmt->bind_param("i", $eventId);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Event deleted successfully!';
            header('Location: events.php');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to delete event: ' . $stmt->error;
        }
    }
}

// Get all events
$eventsQuery = $conn->query("
    SELECT e.*, u.full_name as owner_name, creator.full_name as creator_name 
    FROM events e 
    JOIN users u ON e.event_owner_id = u.id 
    JOIN users creator ON e.created_by = creator.id 
    ORDER BY e.event_date DESC
");
$events = $eventsQuery->fetch_all(MYSQLI_ASSOC);

// Get event owners for dropdown
$ownersQuery = $conn->query("SELECT id, full_name FROM users WHERE role = 'event_owner' AND is_active = 1");
$owners = $ownersQuery->fetch_all(MYSQLI_ASSOC);

// Get event statistics
$statsQuery = $conn->query("
    SELECT 
        COUNT(*) as total_events,
        SUM(event_date >= CURDATE()) as upcoming,
        SUM(event_date < CURDATE()) as completed,
        COUNT(DISTINCT event_owner_id) as unique_owners
    FROM events
");
$stats = $statsQuery->fetch_assoc();
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Events Management</h1>
            <p class="text-slate-500 font-medium">Create, manage, and track all system events</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openCreateModal()" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-plus-circle"></i>
                <span>CREATE EVENT</span>
            </button>
        </div>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <!-- Event Statistics -->
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
                    <i class="fas fa-clock"></i>
                </div>
                <span class="font-bold text-sm uppercase tracking-widest text-slate-400">Upcoming</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['upcoming'] ?? 0; ?></div>
        </div>
        <div class="glass p-6 rounded-[32px] border-b-4 border-b-green-500 shadow-sm">
            <div class="flex items-center gap-4 mb-4 text-green-500">
                <div class="w-12 h-12 rounded-2xl bg-green-500/10 flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="font-bold text-sm uppercase tracking-widest text-slate-400">Completed</span>
            </div>
            <div class="text-4xl font-black text-slate-900"><?php echo $stats['completed'] ?? 0; ?></div>
        </div>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-4">Event Name</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Date & Location</th>
                        <th class="px-6 py-4">Owner</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($events as $event):
                        $eventDate = strtotime($event['event_date']);
                    ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-6 py-6">
                            <div class="font-bold text-slate-900 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($event['event_name']); ?></div>
                        </td>
                        <td class="px-6 py-6">
                            <?php
                            $badgeClass = match($event['event_type']) {
                                'harusi' => 'bg-primary/10 text-primary',
                                'sendoff' => 'bg-blue-500/10 text-blue-500',
                                'kitchen_party' => 'bg-green-500/10 text-green-500',
                                default => 'bg-slate-100 text-slate-500'
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full <?php echo $badgeClass; ?> text-[10px] font-black uppercase"><?php echo ucfirst(str_replace('_', ' ', $event['event_type'])); ?></span>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-bold text-slate-700"><?php echo date('M d, Y', $eventDate); ?></div>
                            <div class="text-xs text-slate-500"><?php echo htmlspecialchars($event['location']); ?></div>
                        </td>
                        <td class="px-6 py-6">
                            <div class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($event['owner_name']); ?></div>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="view-event.php?id=<?php echo $event['id']; ?>" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-all">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button onclick="editEvent(<?php echo $event['id']; ?>)" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-all">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteEvent(<?php echo $event['id']; ?>)" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Event Modal -->
<div id="eventModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4">
    <div class="bg-white rounded-[40px] w-full max-w-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
        <div class="p-8 border-b border-slate-100 flex items-center justify-between">
            <h3 id="modalTitle" class="text-2xl font-black text-slate-900 uppercase">Create New Event</h3>
            <button onclick="closeModal()" class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form method="POST" id="eventForm" class="p-8">
            <input type="hidden" name="event_id" id="event_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Name</label>
                    <input type="text" name="event_name" id="event_name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Type</label>
                    <select name="event_type" id="event_type" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                        <option value="harusi">Harusi</option>
                        <option value="sendoff">Send Off</option>
                        <option value="kitchen_party">Kitchen Party</option>
                        <option value="nyingine">Nyingine</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Date</label>
                    <input type="date" name="event_date" id="event_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Location</label>
                    <input type="text" name="location" id="location" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Owner</label>
                    <select name="event_owner_id" id="event_owner_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                        <?php foreach ($owners as $owner): ?>
                        <option value="<?php echo $owner['id']; ?>"><?php echo htmlspecialchars($owner['full_name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="space-y-2 md:col-span-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all"></textarea>
                </div>
            </div>
            <div class="flex gap-4">
                <button type="submit" id="submitBtn" name="create_event" class="flex-1 bg-primary hover:bg-primary-dark text-white py-4 rounded-2xl font-bold transition-all shadow-lg shadow-primary/20">SAVE EVENT</button>
                <button type="button" onclick="closeModal()" class="px-8 bg-slate-100 text-slate-600 rounded-2xl font-bold hover:bg-slate-200 transition-all">CANCEL</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('eventForm').reset();
        document.getElementById('modalTitle').innerText = 'Create New Event';
        document.getElementById('submitBtn').name = 'create_event';
        document.getElementById('eventModal').classList.remove('hidden');
        document.getElementById('eventModal').classList.add('flex');
    }
    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
        document.getElementById('eventModal').classList.remove('flex');
    }
    function editEvent(id) {
        fetch(`../api/events.php?id=${id}`)
            .then(r => r.json())
            .then(d => {
                if(d.success) {
                    const e = d.data;
                    document.getElementById('event_id').value = e.id;
                    document.getElementById('event_name').value = e.event_name;
                    document.getElementById('event_type').value = e.event_type;
                    document.getElementById('event_date').value = e.event_date;
                    document.getElementById('location').value = e.location;
                    document.getElementById('description').value = e.description || '';
                    document.getElementById('event_owner_id').value = e.event_owner_id;
                    document.getElementById('modalTitle').innerText = 'Edit Event';
                    document.getElementById('submitBtn').name = 'update_event';
                    document.getElementById('eventModal').classList.remove('hidden');
                    document.getElementById('eventModal').classList.add('flex');
                }
            });
    }
    function deleteEvent(id) {
        if(confirm('Delete this event?')) {
            const f = document.createElement('form');
            f.method = 'POST';
            f.innerHTML = `<input type="hidden" name="event_id" value="${id}"><input type="hidden" name="delete_event" value="1">`;
            document.body.appendChild(f);
            f.submit();
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>