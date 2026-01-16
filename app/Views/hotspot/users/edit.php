<?php require_once ROOT . '/app/Views/layouts/header_main.php'; ?>
<?php require_once ROOT . '/app/Views/layouts/sidebar_session.php'; ?>

<!-- Content Inside max-w-7xl -->
<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight text-foreground" data-i18n="hotspot_users.form.edit_title">Edit Hotspot User</h1>
        <p class="text-sm text-accents-5" data-i18n="hotspot_users.form.edit_subtitle" data-i18n-params='{"name": "<?= htmlspecialchars($user['name']) ?>"}'>Update user details for: <span class="font-medium text-foreground"><?= htmlspecialchars($user['name']) ?></span></p>
    </div>
    <a href="/<?= htmlspecialchars($session) ?>/hotspot/users" class="btn btn-secondary w-full sm:w-auto justify-center" data-i18n="common.back">
        <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
        Cancel
    </a>
</div>

<div class="card bg-background border border-accents-2 rounded-lg shadow-sm">
    <form action="/<?= htmlspecialchars($session) ?>/hotspot/update" method="POST" class="p-6 space-y-6">
        <input type="hidden" name="session" value="<?= htmlspecialchars($session) ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars($user['.id']) ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Username -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.username">Username</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="user" class="w-4 h-4 text-accents-4"></i>
                    </div>
                    <input type="text" name="name" class="form-input pl-10 w-full" 
                           value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>
                </div>
            </div>

            <!-- Password -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.password">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="lock" class="w-4 h-4 text-accents-4"></i>
                    </div>
                    <input type="text" name="password" class="form-input pl-10 w-full"
                           value="<?= htmlspecialchars($user['password'] ?? '') ?>">
                </div>
            </div>

            <!-- Profile -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.profile">Profile</label>
                <select name="profile" class="custom-select w-full">
                    <?php foreach ($profiles as $profile): ?>
                        <option value="<?= htmlspecialchars($profile['name']) ?>" 
                            <?= (isset($user['profile']) && $user['profile'] === $profile['name']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($profile['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Server -->
             <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.server">Server</label>
                <select name="server" class="custom-select w-full">
                    <option value="all" <?= (isset($user['server']) && $user['server'] === 'all') ? 'selected' : '' ?>>all</option>
                    <!-- Ideally fetch servers like in generate, but keeping it simple for now -->
                </select>
            </div>

            <!-- Time Limit -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.time_limit">Time Limit</label>
                <div class="flex w-full">
                    <!-- Day -->
                    <div class="relative flex-1 group">
                        <span class="absolute right-3 top-2.5 text-accents-4 text-xs font-bold pointer-events-none">D</span>
                        <input type="number" name="timelimit_d" value="<?= htmlspecialchars($user['time_d'] ?? '') ?>" min="0" class="form-input w-full pr-8 rounded-r-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                    </div>
                    <!-- Hour -->
                    <div class="relative flex-1 group">
                        <span class="absolute right-3 top-2.5 text-accents-4 text-xs font-bold pointer-events-none">H</span>
                        <input type="number" name="timelimit_h" value="<?= htmlspecialchars($user['time_h'] ?? '') ?>" min="0" max="23" class="form-input w-full pr-8 rounded-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                    </div>
                    <!-- Minute -->
                    <div class="relative flex-1 group">
                        <span class="absolute right-3 top-2.5 text-accents-4 text-xs font-bold pointer-events-none">M</span>
                        <input type="number" name="timelimit_m" value="<?= htmlspecialchars($user['time_m'] ?? '') ?>" min="0" max="59" class="form-input w-full pr-8 rounded-l-none focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                    </div>
                </div>
            </div>

            <!-- Data Limit -->
            <div class="space-y-1">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.data_limit">Data Limit</label>
                 <div class="flex relative w-full">
                    <div class="relative flex-grow z-0 focus-within:z-10">
                        <span class="absolute left-3 top-2.5 text-accents-4 transition-colors pointer-events-none">
                            <i data-lucide="database" class="w-4 h-4"></i>
                        </span>
                        <input type="number" name="datalimit_val" value="<?= htmlspecialchars($user['data_val'] ?? '') ?>" min="0" class="form-input w-full pl-10 rounded-r-none focus:ring-2 focus:ring-primary/20 transition-all" placeholder="0">
                    </div>
                    <div class="relative -ml-px w-24 z-0 focus-within:z-10">
                        <select name="datalimit_unit" class="custom-select form-input w-full rounded-l-none bg-accents-1 focus:ring-2 focus:ring-primary/20 cursor-pointer font-medium text-accents-6 text-center">
                            <option value="MB" <?= ($user['data_unit'] ?? 'MB') === 'MB' ? 'selected' : '' ?>>MB</option>
                            <option value="GB" <?= ($user['data_unit'] ?? 'MB') === 'GB' ? 'selected' : '' ?>>GB</option>
                        </select>
                    </div>
                </div>
            </div>
            
             <!-- Comment -->
            <div class="space-y-1 col-span-2">
                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.comment">Comment</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="message-square" class="w-4 h-4 text-accents-4"></i>
                    </div>
                    <input type="text" name="comment" class="form-input pl-10 w-full"
                          value="<?= htmlspecialchars($user['comment'] ?? '') ?>">
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-6 border-t border-accents-2 flex justify-end gap-3">
             <a href="/<?= htmlspecialchars($session) ?>/hotspot/users" class="btn btn-secondary" data-i18n="common.cancel">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                <span data-i18n="common.forms.save_changes">Save Changes</span>
            </button>
        </div>
    </form>
</div>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
