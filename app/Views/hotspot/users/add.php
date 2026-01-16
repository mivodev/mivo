<?php
$title = "Add User";
require_once ROOT . '/app/Views/layouts/header_main.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight" data-i18n="hotspot_users.form.add_title">Add User</h1>
            <p class="text-accents-5" data-i18n="hotspot_users.form.subtitle" data-i18n-params='{"name": "<?= htmlspecialchars($session) ?>"}'>Generate a new voucher/user for session: <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
        </div>
        <a href="/<?= htmlspecialchars($session) ?>/hotspot/users" class="btn btn-secondary" data-i18n="common.back">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="card p-6 border-accents-2 shadow-sm">
                <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                    </div>
                    <span data-i18n="hotspot_users.form.subtitle">User Details</span>
                </h3>
                
                <form action="/<?= htmlspecialchars($session) ?>/hotspot/store" method="POST" class="space-y-6">
                    <input type="hidden" name="session" value="<?= htmlspecialchars($session) ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name & Password -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.username">Name (Username)</label>
                            <div class="relative group">
                                <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="name" required class="form-input pl-10 w-full focus:ring-2 focus:ring-primary/20 transition-all" data-i18n-placeholder="hotspot_users.form.username_placeholder" placeholder="e.g. voucher123">
                            </div>
                            <p class="text-xs text-accents-5 mt-1" data-i18n="hotspot_users.form.username_help">Unique username for login.</p>
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.password">Password</label>
                            <div class="relative group">
                                <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                    <i data-lucide="key" class="w-4 h-4"></i>
                                </span>
                                <input type="text" name="password" required class="form-input pl-10 w-full focus:ring-2 focus:ring-primary/20 transition-all" data-i18n-placeholder="hotspot_users.form.password_placeholder" placeholder="e.g. 123456">
                            </div>
                             <p class="text-xs text-accents-5 mt-1" data-i18n="hotspot_users.form.password_help">Strong password for security.</p>
                        </div>

                        <!-- Profile -->
                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.profile">Profile</label>
                             <!-- Searchable Dropdown -->
                            <select name="profile" class="custom-select w-full" data-search="true">
                                <?php foreach ($profiles as $profile): ?>
                                    <?php if(!empty($profile['name'])): ?>
                                        <option value="<?= htmlspecialchars($profile['name']) ?>"><?= htmlspecialchars($profile['name']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <p class="text-xs text-accents-4 mt-1" data-i18n="hotspot_users.form.profile_help">Profile determines speed limit and shared user policy.</p>
                        </div>

                        <!-- Time Limit (Split) -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.time_limit">Time Limit</label>
                            <div class="flex w-full">
                                <!-- Day -->
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">D</span>
                                    <input type="number" name="timelimit_d" min="0" class="form-input w-full pr-8 rounded-r-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                                <!-- Hour -->
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">H</span>
                                    <input type="number" name="timelimit_h" min="0" max="23" class="form-input w-full pr-8 rounded-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                                <!-- Minute -->
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">M</span>
                                    <input type="number" name="timelimit_m" min="0" max="59" class="form-input w-full pr-8 rounded-l-none focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                            </div>
                            <p class="text-xs text-accents-5 mt-1" data-i18n="hotspot_users.form.time_limit_help">Total allowed uptime (Days, Hours, Minutes).</p>
                        </div>

                        <!-- Data Limit (Unit) -->
                        <div class="space-y-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.data_limit">Data Limit</label>
                            <div class="flex relative w-full">
                                <div class="relative flex-grow z-0 focus-within:z-10">
                                    <span class="absolute left-3 top-2.5 text-accents-6 z-10 transition-colors pointer-events-none">
                                        <i data-lucide="database" class="w-4 h-4"></i>
                                    </span>
                                    <input type="number" name="datalimit_val" min="0" class="form-input w-full pl-10 rounded-r-none focus:ring-2 focus:ring-primary/20 transition-all" placeholder="0">
                                </div>
                                <div class="relative -ml-px w-24 z-0 focus-within:z-10">
                                    <select name="datalimit_unit" class="custom-select form-input w-full rounded-l-none bg-accents-1 focus:ring-2 focus:ring-primary/20 cursor-pointer font-medium text-accents-6 text-center">
                                        <option value="MB" selected>MB</option>
                                        <option value="GB">GB</option>
                                    </select>
                                </div>
                            </div>
                            <p class="text-xs text-accents-5 mt-1" data-i18n="hotspot_users.form.data_limit_help">Limit data usage (0 for unlimited).</p>
                        </div>

                        <!-- Comment -->
                         <div class="space-y-2 col-span-1 md:col-span-2">
                            <label class="text-sm font-medium text-accents-6" data-i18n="hotspot_users.form.comment">Comment</label>
                             <div class="relative group">
                                <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                    <i data-lucide="message-square" class="w-4 h-4"></i>
                                </span>
                                 <input type="text" name="comment" class="form-input pl-10 w-full focus:ring-2 focus:ring-primary/20 transition-all" data-i18n-placeholder="hotspot_users.form.comment_placeholder" placeholder="Optional note for this user">
                            </div>
                             <p class="text-xs text-accents-5 mt-1" data-i18n="hotspot_users.form.comment_help">Additional notes or contact info.</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-accents-2 flex justify-end gap-3">
                        <a href="/<?= htmlspecialchars($session) ?>/hotspot/users" class="btn btn-secondary" data-i18n="common.cancel">Cancel</a>
                        <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-shadow">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            <span data-i18n="hotspot_users.form.save">Save User</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Help / Info -->
        <div class="space-y-6">
            <div class="card p-6 bg-accents-1/50 border-accents-2 border-dashed">
                <h3 class="font-semibold mb-4 flex items-center gap-2 text-foreground" data-i18n="hotspot_users.form.quick_tips">
                    <i data-lucide="lightbulb" class="w-4 h-4 text-yellow-500"></i> 
                    Quick Tips
                </h3>
                <ul class="text-sm text-accents-5 space-y-3">
                    <li class="flex gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                        <span data-i18n="hotspot_users.form.tip_profiles"><strong>Profiles</strong> define the default speed limits, session timeout, and shared users policy.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                        <span data-i18n="hotspot_users.form.tip_time_limit"><strong>Time Limit</strong> is the total accumulated uptime allowed for this user.</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                        <span data-i18n="hotspot_users.form.tip_data_limit"><strong>Data Limit</strong> will override the profile's data limit settings if specified here. Set to 0 to use profile default.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Custom Selects with Search
        if (typeof CustomSelect !== 'undefined') {
            document.querySelectorAll('.custom-select').forEach(select => {
                new CustomSelect(select);
            });
        }
    });
</script>
