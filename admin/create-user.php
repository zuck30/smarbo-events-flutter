<?php
$pageTitle = 'Create User';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'], $conn);
    $email = sanitize($_POST['email'], $conn);
    $password = $_POST['password'];
    $fullName = sanitize($_POST['full_name'], $conn);
    $phone = sanitize($_POST['phone'], $conn);
    $role = sanitize($_POST['role'], $conn);

    $result = $auth->register($username, $email, $password, $fullName, $phone, $role);

    if ($result['success']) {
        $_SESSION['message'] = 'User created successfully';
        header('Location: users.php');
        exit();
    } else {
        $error = $result['message'];
    }
}
?>

<main class="flex-1 lg:ml-20 p-4 md:p-8 transition-all duration-300 min-w-0 pb-24 lg:pb-8">
    <header class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-4xl font-black text-slate-900 mb-2 uppercase tracking-tight">Add New User</h1>
            <p class="text-slate-500 font-medium">Create a new system administrator or event owner</p>
        </div>
        <a href="users.php" class="bg-slate-100 hover:bg-slate-200 text-slate-600 px-6 py-3 rounded-2xl font-bold transition-all flex items-center gap-2 border border-slate-200 shadow-sm">
            <i class="fas fa-arrow-left"></i>
            <span>BACK TO USERS</span>
        </a>
    </header>

    <div class="max-w-4xl">
        <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
            <?php if (isset($error)): ?>
                <div class="m-8 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-500 flex items-center gap-3 font-bold">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="p-8 md:p-12">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Full Legal Name</label>
                        <input type="text" name="full_name" required placeholder="John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Email Address</label>
                        <input type="email" name="email" required placeholder="john@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Username</label>
                        <input type="text" name="username" required placeholder="johndoe" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">System Role</label>
                        <select name="role" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium appearance-none">
                            <option value="event_owner">Event Owner</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Phone Number</label>
                        <input type="tel" name="phone" placeholder="+255..." class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest">Initial Password</label>
                        <input type="password" name="password" required minlength="6" placeholder="••••••••" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-6 py-4 focus:outline-none focus:border-primary transition-all font-medium">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-primary hover:bg-primary-dark text-white py-5 rounded-[24px] font-black uppercase tracking-widest transition-all shadow-xl shadow-primary/30">
                        CREATE SYSTEM USER
                    </button>
                    <a href="users.php" class="px-12 py-5 bg-slate-100 text-slate-600 rounded-[24px] font-black uppercase tracking-widest hover:bg-slate-200 transition-all text-center">
                        CANCEL
                    </a>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>