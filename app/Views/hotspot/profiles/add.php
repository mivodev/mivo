<?php
$title = "Add User Profile";
require_once ROOT . '/app/Views/layouts/header_main.php';
?>

<div class="max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight" data-i18n="hotspot_profiles.form.add_title">Add Profile</h1>
            <p class="text-accents-5" data-i18n="hotspot_profiles.form.subtitle" data-i18n-params='{"name": "<?= htmlspecialchars($session) ?>"}'>Create a new hotspot user profile for: <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
        </div>
        <a href="/<?= htmlspecialchars($session) ?>/hotspot/profiles" class="btn btn-secondary w-full sm:w-auto justify-center" data-i18n="common.back">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Column -->
        <div class="lg:col-span-2">
            <div class="card p-6 border-accents-2 shadow-sm">
                <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                    </div>
                    <span data-i18n="hotspot_profiles.form.settings">New Profile Settings</span>
                </h3>

                <form action="/<?= htmlspecialchars($session) ?>/hotspot/profile/store" method="POST" class="space-y-6">
                    <input type="hidden" name="session" value="<?= htmlspecialchars($session) ?>">

                    <!-- General Settings Section -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-accents-5 uppercase tracking-wider border-b border-accents-2 pb-2" data-i18n="hotspot_profiles.form.general">General</h4>
                        
                        <!-- Name -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-accents-6"><span data-i18n="common.name">Name</span> <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="form-input w-full" data-i18n-placeholder="hotspot_profiles.form.name_placeholder" placeholder="e.g. 1Hour-Package">
                        </div>

                        <!-- Pools & Shared Users -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.address_pool">Address Pool</label>
                                <select name="address-pool" class="custom-select w-full" data-search="true">
                                    <option value="none" data-i18n="common.forms.none">none</option>
                                    <?php foreach ($pools as $pool): ?>
                                        <?php if(isset($pool['name'])): ?>
                                        <option value="<?= htmlspecialchars($pool['name']) ?>"><?= htmlspecialchars($pool['name']) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.shared_users">Shared Users</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                        <i data-lucide="users" class="w-4 h-4"></i>
                                    </span>
                                    <input type="number" name="shared-users" value="1" min="1" class="form-input pl-10 w-full" placeholder="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits Section -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-accents-5 uppercase tracking-wider border-b border-accents-2 pb-2" data-i18n="hotspot_profiles.form.limits_queues">Limits & Queues</h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Rate Limit -->
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.rate_limit">Rate Limit (Rx/Tx)</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                        <i data-lucide="activity" class="w-4 h-4"></i>
                                    </span>
                                    <input type="text" name="rate-limit" class="form-input pl-10 w-full" data-i18n-placeholder="hotspot_profiles.form.rate_limit_help" placeholder="e.g. 512k/1M">
                                </div>
                            </div>

                            <!-- Parent Queue -->
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.parent_queue">Parent Queue</label>
                                <select name="parent-queue" class="custom-select w-full" data-search="true">
                                    <option value="none" data-i18n="common.forms.none">none</option>
                                    <?php foreach ($queues as $q): ?>
                                        <option value="<?= htmlspecialchars($q) ?>"><?= htmlspecialchars($q) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Validity -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-accents-5 uppercase tracking-wider border-b border-accents-2 pb-2" data-i18n="hotspot_profiles.form.pricing_validity">Pricing & Validity</h4>

                        <!-- Expired Mode -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.expired_mode">Expired Mode</label>
                            <select name="expired_mode" id="expired-mode" class="custom-select w-full">
                                <option value="none" data-i18n="common.forms.none" selected>none</option>
                                <option value="rem">Remove</option>
                                <option value="ntf">Notice</option>
                                <option value="remc">Remove & Record</option>
                                <option value="ntfc">Notice & Record</option>
                            </select>
                            <p class="text-xs text-accents-5" data-i18n="hotspot_profiles.form.expired_mode_help">Action when validity expires.</p>
                        </div>

                        <!-- Validity (Hidden by default unless mode selected) -->
                        <div id="validity-group" class="hidden space-y-1 transition-all duration-300">
                            <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.validity">Validity</label>
                            <div class="flex w-full">
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">D</span>
                                    <input type="number" name="validity_d" min="0" class="form-input w-full pr-8 rounded-r-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">H</span>
                                    <input type="number" name="validity_h" min="0" class="form-input w-full pr-8 rounded-none border-r-0 focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                                <div class="relative flex-1 group">
                                    <span class="absolute right-3 top-2.5 text-accents-6 z-10 text-xs font-bold pointer-events-none">M</span>
                                    <input type="number" name="validity_m" min="0" class="form-input w-full pr-8 rounded-l-none focus:ring-2 focus:ring-primary/20 focus:z-10 transition-all font-mono text-center" placeholder="0">
                                </div>
                            </div>
                            <p class="text-xs text-accents-5" data-i18n="hotspot_profiles.form.validity_help">Days / Hours / Minutes</p>
                        </div>

                        <!-- Prices -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.price">Price (Rp)</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                        <i data-lucide="tag" class="w-4 h-4"></i>
                                    </span>
                                    <input type="number" name="price" class="form-input pl-10 w-full" placeholder="e.g. 5000">
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.selling_price">Selling Price (Rp)</label>
                                <div class="relative group">
                                    <span class="absolute left-3 top-2.5 text-accents-6 z-10 group-focus-within:text-primary transition-colors pointer-events-none">
                                        <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                                    </span>
                                    <input type="number" name="selling_price" class="form-input pl-10 w-full" placeholder="e.g. 7000">
                                </div>
                            </div>
                        </div>

                        <!-- Lock User -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-accents-6" data-i18n="hotspot_profiles.form.lock_user">Lock User</label>
                            <select name="lock_user" class="custom-select w-full">
                                <option value="Disable" data-i18n="common.forms.disabled">Disable</option>
                                <option value="Enable" data-i18n="common.forms.enabled">Enable</option>
                            </select>
                            <p class="text-xs text-accents-5" data-i18n="hotspot_profiles.form.lock_user_help">Lock user to one specific MAC address.</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-accents-2 flex justify-end gap-3">
                        <a href="/<?= htmlspecialchars($session) ?>/hotspot/profiles" class="btn btn-secondary" data-i18n="common.cancel">Cancel</a>
                        <button type="submit" class="btn btn-primary px-8 shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-shadow">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            <span data-i18n="hotspot_profiles.form.save">Save Profile</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sticky Quick Tips Column -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                <div class="card p-6 bg-accents-1/50 border-accents-2 border-dashed">
                    <h3 class="font-semibold mb-4 flex items-center gap-2 text-foreground" data-i18n="hotspot_profiles.form.quick_tips">
                        <i data-lucide="lightbulb" class="w-4 h-4 text-yellow-500"></i> 
                        Quick Tips
                    </h3>
                    <ul class="text-sm text-accents-5 space-y-3">
                        <li class="flex gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                            <span data-i18n="hotspot_profiles.form.tip_rate_limit"><strong>Rate Limit</strong>: Rx/Tx (Upload/Download). Example: <code>512k/1M</code></span>
                        </li>
                        <li class="flex gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                            <span data-i18n="hotspot_profiles.form.tip_expired_mode"><strong>Expired Mode</strong>: Select 'Remove' or 'Notice' to enable Validity.</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
                            <span data-i18n="hotspot_profiles.form.tip_parent_queue"><strong>Parent Queue</strong>: Assigns users to a specific parent queue for bandwidth management.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Custom Select Init
    if (typeof CustomSelect !== 'undefined') {
        document.querySelectorAll('.custom-select').forEach(select => {
            new CustomSelect(select);
        });
    }

    // Validity Toggle Logic
    const modeSelect = document.getElementById('expired-mode');
    const validityGroup = document.getElementById('validity-group');

    function toggleValidity() {
        if (!modeSelect || !validityGroup) return;
        
        // Show validity ONLY if mode != none
        if (modeSelect.value === 'none') {
            validityGroup.classList.add('hidden');
        } else {
            validityGroup.classList.remove('hidden');
        }
    }

    if (modeSelect) {
        // Initial check
        toggleValidity();
        // Listen for changes
        modeSelect.addEventListener('change', toggleValidity);
    }
});
</script>
