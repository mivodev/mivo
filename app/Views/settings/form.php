<?php
// Use $router variable instead of $session to avoid conflict with header.php logic
$router = $router ?? null;
$title = $router ? "Edit Router" : "Add Router";
require_once ROOT . '/app/Views/layouts/header_main.php';

// Safe access helper
$val = function($key) use ($router) {
    return isset($router) && isset($router[$key]) ? htmlspecialchars($router[$key]) : '';
};
?>

<div class="w-full max-w-5xl mx-auto mb-16">
    <div class="mb-8">
        <a href="/settings/routers" class="inline-flex items-center text-sm text-accents-5 hover:text-foreground transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Back to Settings
        </a>
        <h1 class="text-2xl font-bold tracking-tight"><?= $title ?></h1>
        <p class="text-accents-5">Connect Mikhmon to your RouterOS device.</p>
    </div>

    <form autocomplete="off" method="post" action="<?= isset($router) ? '/settings/update' : '/settings/store' ?>">
        <?php if(isset($router)): ?>
            <input type="hidden" name="id" value="<?= $router['id'] ?>">
        <?php endif; ?>
        
            <div class="card p-6 md:p-8 space-y-6">
                <div>
                    <h2 class="text-base font-semibold mb-4">Session Settings</h2>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Session Name</label>
                            <input class="form-control w-full" type="text" name="sessname" id="sessname" placeholder="e.g. router-jakarta-1" value="<?= $val('session_name') ?>" required/>
                            <p class="text-xs text-accents-4">Unique ID. Preview: <span id="sessname-preview" class="font-mono text-primary font-bold">...</span></p>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="quick_access" name="quick_access" class="checkbox" <?= (isset($router['quick_access']) && $router['quick_access'] == 1) ? 'checked' : '' ?> value="1">
                            <label for="quick_access" class="text-sm font-medium cursor-pointer select-none">Show in Quick Access (Home Page)</label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-accents-2 pt-6">
                    <h2 class="text-base font-semibold mb-4">Connection Details</h2>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">IP Address</label>
                            <input class="form-control w-full" type="text" name="ipmik" placeholder="192.168.88.1" value="<?= $val('ip_address') ?>" required/>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Username</label>
                                <input class="form-control w-full" type="text" name="usermik" placeholder="admin" value="<?= $val('username') ?>" required/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Password</label>
                                <input class="form-control w-full" type="password" name="passmik" <?= isset($router) ? '' : 'required' ?> />
                                <?php if(isset($router)): ?>
                                <p class="text-xs text-accents-4">Leave empty to keep existing password.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-accents-2 pt-6">
                    <h2 class="text-base font-semibold mb-4">Hotspot Information</h2>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium">Hotspot Name</label>
                            <input class="form-control w-full" type="text" name="hotspotname" placeholder="My Hotspot ID" value="<?= $val('hotspot_name') ?>" required/>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-sm font-medium">DNS Name</label>
                                <input class="form-control w-full" type="text" name="dnsname" placeholder="hotspot.net" value="<?= $val('dns_name') ?>" required/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Traffic Interface</label>
                                <div class="flex w-full gap-2">
                                    <div class="flex-grow">
                                        <select class="custom-select w-full" name="iface" id="iface" data-search="true" required>
                                            <option value="<?= $val('interface') ?: 'ether1' ?>"><?= $val('interface') ?: 'ether1' ?></option>
                                        </select>
                                    </div>
                                    <button type="button" id="check-interface-btn" class="btn btn-secondary whitespace-nowrap" title="Check connection and fetch interfaces">
                                        <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> Check
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <div class="space-y-2">
                                <label class="text-sm font-medium">Currency</label>
                                <input class="form-control w-full" type="text" name="currency" value="<?= $val('currency') ?: 'Rp' ?>" required/>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-medium">Auto Reload (Sec)</label>
                                <input class="form-control w-full" type="number" min="10" name="areload" value="<?= $val('reload_interval') ?: 10 ?>" required/>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="pt-6 flex justify-end gap-3">
                <a href="/settings/routers" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-secondary" name="action" value="save">
                    Save
                </button>
                <button type="submit" class="btn btn-primary" name="action" value="connect">
                    Save & Connect
                </button>
            </div>
        </div>
    </form>
</div>

<script src="/assets/js/router-form.js"></script>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
