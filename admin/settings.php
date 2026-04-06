<?php
$pageTitle = 'Account Settings';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($newPassword !== $confirmPassword) {
        $_SESSION['error'] = 'New passwords do not match';
    } elseif (strlen($newPassword) < 6) {
        $_SESSION['error'] = 'Password must be at least 6 characters long';
    } else {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
        $dbUser = $stmt->get_result()->fetch_assoc();
        
        if (password_verify($currentPassword, $dbUser['password'])) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $updateStmt->bind_param("si", $hashedPassword, $user['id']);
            if ($updateStmt->execute()) {
                $_SESSION['message'] = 'Password changed successfully';
            } else {
                $_SESSION['error'] = 'Failed to change password';
            }
        } else {
            $_SESSION['error'] = 'Current password is incorrect';
        }
    }
    header('Location: settings.php');
    exit();
}
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="mb-8">
        <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Account Settings</h1>
        <p class="text-slate-500 font-medium">Manage your profile and security preferences</p>
    </header>

    <?php if (isset($_SESSION['message'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-500 flex items-center gap-3">
        <i class="fas fa-check-circle"></i>
        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 rounded-3xl bg-primary/10 text-primary flex items-center justify-center text-2xl font-black">
                    <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                </div>
                <div>
                    <h3 class="text-xl font-black text-slate-900"><?php echo htmlspecialchars($user['full_name']); ?></h3>
                    <p class="text-slate-500 text-sm font-medium">System Administrator</p>
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Email Address</label>
                    <div class="text-slate-700 font-bold"><?php echo htmlspecialchars($user['email']); ?></div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Username</label>
                    <div class="text-slate-700 font-bold">@<?php echo htmlspecialchars($user['username']); ?></div>
                </div>
                <div>
                    <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Account Type</label>
                    <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[10px] font-black uppercase inline-block mt-1">Admin Access</span>
                </div>
            </div>
        </div>

        <!-- Security Card -->
        <div class="lg:col-span-2 bg-white rounded-[40px] p-8 border border-slate-200 shadow-sm">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center text-xl">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="text-2xl font-black text-slate-900 uppercase tracking-tight">Security</h3>
            </div>

            <form method="POST" class="space-y-6 max-w-lg">
                <div class="space-y-2">
                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Current Password</label>
                    <input type="password" name="current_password" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">New Password</label>
                        <input type="password" name="new_password" required minlength="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Confirm New</label>
                        <input type="password" name="confirm_password" required minlength="6" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 focus:outline-none focus:border-primary transition-all">
                    </div>
                </div>
                <button type="submit" name="change_password" class="bg-slate-900 hover:bg-black text-white px-8 py-4 rounded-2xl font-bold transition-all shadow-lg">
                    UPDATE PASSWORD
                </button>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>