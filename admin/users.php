<?php
$pageTitle = 'Users Management';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['toggle_user'])) {
        $userId = intval($_POST['user_id']);
        $stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $_SESSION['message'] = 'User status updated successfully';
        header('Location: users.php');
        exit();
    }
    
    if (isset($_POST['delete_user'])) {
        $userId = intval($_POST['user_id']);
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            $_SESSION['message'] = 'User deleted successfully';
        } else {
            $_SESSION['error'] = 'Cannot delete admin user';
        }
        header('Location: users.php');
        exit();
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex flex-col lg:flex-row lg:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Users Management</h1>
            <p class="text-slate-500 font-medium">Manage all system users and permissions</p>
        </div>
        <div class="flex gap-3">
            <a href="create-user.php" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-primary/20">
                <i class="fas fa-user-plus"></i>
                <span>ADD USER</span>
            </a>
        </div>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
    <?php endif; ?>

    <div class="glass p-6 rounded-[32px] mb-8 flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" id="searchInput" placeholder="Search users..." class="w-full bg-white/50 border border-slate-200 rounded-xl py-3 pl-12 pr-4 focus:outline-none focus:border-primary transition-all">
        </div>
        <select id="roleFilter" class="bg-white/50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="event_owner">Event Owner</option>
        </select>
    </div>

    <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left" id="usersTable">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                        <th class="px-6 py-4">User Details</th>
                        <th class="px-6 py-4">Contact Information</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php while($u = $users->fetch_assoc()):
                        $initials = strtoupper(substr($u['full_name'], 0, 2));
                    ?>
                    <tr class="user-row hover:bg-slate-50/50 transition-colors" data-role="<?php echo $u['role']; ?>" data-search="<?php echo strtolower($u['full_name'] . ' ' . $u['email']); ?>">
                        <td class="px-6 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-lg">
                                    <?php echo $initials; ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900"><?php echo htmlspecialchars($u['full_name']); ?></div>
                                    <div class="text-xs text-slate-500 font-medium">@<?php echo htmlspecialchars($u['username']); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-sm">
                            <div class="text-slate-700 font-medium"><?php echo htmlspecialchars($u['email']); ?></div>
                            <div class="text-xs text-slate-500"><?php echo htmlspecialchars($u['phone'] ?? 'No phone'); ?></div>
                        </td>
                        <td class="px-6 py-6">
                            <?php if($u['role'] == 'admin'): ?>
                                <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase">Admin</span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-blue-500/10 text-blue-500 text-[10px] font-black uppercase">Owner</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-6">
                            <?php if($u['is_active']): ?>
                                <span class="px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-[10px] font-black uppercase">Active</span>
                            <?php else: ?>
                                <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-[10px] font-black uppercase">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-6 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="view-user.php?id=<?php echo $u['id']; ?>" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-blue-500 hover:bg-blue-50 transition-all" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" name="toggle_user" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-amber-500 hover:bg-amber-50 transition-all" title="Toggle Status">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                                <?php if($u['role'] != 'admin'): ?>
                                <form method="POST" class="inline" onsubmit="return confirm('Delete user?')">
                                    <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" name="delete_user" class="p-2 rounded-xl bg-slate-100 text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    const searchInput = document.getElementById('searchInput');
    const roleFilter = document.getElementById('roleFilter');
    const rows = document.querySelectorAll('.user-row');

    function filterUsers() {
        const term = searchInput.value.toLowerCase();
        const role = roleFilter.value;

        rows.forEach(row => {
            const matchesSearch = row.getAttribute('data-search').includes(term);
            const matchesRole = role === "" || row.getAttribute('data-role') === role;
            row.style.display = (matchesSearch && matchesRole) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterUsers);
    roleFilter.addEventListener('change', filterUsers);
</script>

<?php require_once 'includes/footer.php'; ?>