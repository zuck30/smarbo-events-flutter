<?php
$pageTitle = 'Create Event';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Get event owners for dropdown
$ownersQuery = $conn->query("SELECT id, full_name FROM users WHERE role = 'event_owner' AND is_active = 1");
$owners = $ownersQuery->fetch_all(MYSQLI_ASSOC);
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Create New Event</h1>
            <p class="text-slate-500 font-medium">Configure all details for a new managed event</p>
        </div>
        <a href="events.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>BACK TO EVENTS</span>
        </a>
    </header>

    <div class="max-w-4xl">
        <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
            <form action="events.php" method="POST" id="createEventForm" class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Name</label>
                        <input type="text" name="event_name" required placeholder="e.g. Grand Wedding Ceremony" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Type</label>
                        <select name="event_type" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium appearance-none">
                            <option value="harusi">Harusi</option>
                            <option value="sendoff">Send Off</option>
                            <option value="kitchen_party">Kitchen Party</option>
                            <option value="nyingine">Nyingine</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Event Date</label>
                        <input type="date" name="event_date" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Location</label>
                        <input type="text" name="location" required placeholder="City, Venue Name" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Assigned Event Owner</label>
                        <select name="event_owner_id" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium appearance-none">
                            <option value="">Select a registered owner...</option>
                            <?php foreach ($owners as $owner): ?>
                            <option value="<?php echo $owner['id']; ?>"><?php echo htmlspecialchars($owner['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="space-y-2 md:col-span-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Detailed Description</label>
                        <textarea name="description" rows="5" placeholder="Provide event details, special notes, etc..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" name="create_event" class="flex-1 bg-primary hover:bg-primary-dark text-white py-5 rounded-[24px] font-black uppercase tracking-widest transition-all shadow-xl shadow-primary/30">
                        LAUNCH NEW EVENT
                    </button>
                    <a href="events.php" class="px-12 py-5 bg-slate-100 text-slate-600 rounded-[24px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                        DISCARD
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>