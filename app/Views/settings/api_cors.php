<?php
$title = "API CORS";
$no_main_container = true;
require_once ROOT . '/app/Views/layouts/header_main.php';
?>

<!-- Sub-Navbar Navigation -->
<?php include ROOT . '/app/Views/layouts/sidebar_settings.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full flex flex-col">

    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight" data-i18n="settings.api_cors_title">API CORS</h1>
        <p class="text-accents-5 mt-2" data-i18n="settings.api_cors_subtitle">Manage Cross-Origin Resource Sharing for API access.</p>
    </div>

    <!-- Content Area -->
    <div class="mt-8 flex-1 min-w-0" id="settings-content-area">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div class="hidden md:block">
                 <!-- Spacer -->
            </div>
            <div class="flex gap-2 w-full md:w-auto">
                <button onclick="openModal('addModal')" class="btn btn-primary w-full md:w-auto">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> <span data-i18n="settings.add_rule">Add CORS Rule</span>
                </button>
            </div>
        </div>

        <div class="table-container">
            <table class="table-glass" id="cors-table">
                <thead>
                    <tr>
                        <th data-i18n="settings.origin">Origin</th>
                        <th data-i18n="settings.methods">Allowed Methods</th>
                        <th data-i18n="settings.headers">Allowed Headers</th>
                        <th class="text-right" data-i18n="common.actions">Actions</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <?php if (!empty($rules)): ?>
                        <?php foreach ($rules as $rule): ?>
                        <tr class="table-row-item">
                            <td>
                                <div class="text-sm font-medium text-foreground"><?= htmlspecialchars($rule['origin']) ?></div>
                                <div class="text-xs text-accents-4">Max Age: <?= $rule['max_age'] ?>s</div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    <?php foreach ($rule['methods_arr'] as $method): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400"><?= htmlspecialchars($method) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td>
                                <div class="text-sm text-accents-5 truncate max-w-[200px]"><?= htmlspecialchars(implode(', ', $rule['headers_arr'])) ?></div>
                            </td>
                            <td class="text-right text-sm font-medium">
                                <div class="flex items-center justify-end gap-2 table-actions-reveal">
                                    <button onclick="editRule(<?= htmlspecialchars(json_encode($rule)) ?>)" class="btn-icon" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <form action="/settings/api-cors/delete" method="POST" onsubmit="event.preventDefault(); Mivo.confirm(window.i18n ? window.i18n.t('settings.cors_rule_deleted') : 'Delete CORS Rule?', 'Are you sure you want to delete the CORS rule for <?= htmlspecialchars($rule['origin']) ?>?', 'Delete', 'Cancel').then(res => { if(res) this.submit(); });" class="inline">
                                        <input type="hidden" name="id" value="<?= $rule['id'] ?>">
                                        <button type="submit" class="btn-icon-danger" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="shield" class="w-12 h-12 text-accents-2 mb-4"></i>
                                    <p class="text-accents-5">No CORS rules found. Add your first origin to allow external API access.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg transition-all duration-300 scale-95 opacity-0 modal-content">
        <div class="card shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold" data-i18n="settings.add_rule">Add CORS Rule</h3>
                <button onclick="closeModal('addModal')" class="text-accents-5 hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="/settings/api-cors/store" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.origin">Origin</label>
                    <input type="text" name="origin" class="form-control" placeholder="https://example.com or *" required>
                    <p class="text-xs text-orange-500 dark:text-orange-400 mt-1 font-medium">Use * for all origins (not recommended for production).</p>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" data-i18n="settings.methods">Allowed Methods</label>
                    <div class="grid grid-cols-3 gap-2">
                        <?php foreach(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD'] as $m): ?>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="methods[]" value="<?= $m ?>" class="form-checkbox" <?= in_array($m, ['GET', 'POST']) ? 'checked' : '' ?>>
                            <span class="text-sm"><?= $m ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.headers">Allowed Headers</label>
                    <input type="text" name="headers" class="form-control" value="*" placeholder="Content-Type, Authorization, *">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.max_age">Max Age (seconds)</label>
                    <input type="number" name="max_age" class="form-control" value="3600">
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary mr-2" data-i18n="common.cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="common.save">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('editModal')"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg transition-all duration-300 scale-95 opacity-0 modal-content">
        <div class="card shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold" data-i18n="settings.edit_rule">Edit CORS Rule</h3>
                <button onclick="closeModal('editModal')" class="text-accents-5 hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="/settings/api-cors/update" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.origin">Origin</label>
                    <input type="text" name="origin" id="edit_origin" class="form-control" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2" data-i18n="settings.methods">Allowed Methods</label>
                    <div class="grid grid-cols-3 gap-2" id="edit_methods_container">
                        <?php foreach(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'HEAD'] as $m): ?>
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="methods[]" value="<?= $m ?>" class="form-checkbox edit-method-check" data-method="<?= $m ?>">
                            <span class="text-sm"><?= $m ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.headers">Allowed Headers</label>
                    <input type="text" name="headers" id="edit_headers" class="form-control">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="settings.max_age">Max Age (seconds)</label>
                    <input type="number" name="max_age" id="edit_max_age" class="form-control">
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary mr-2" data-i18n="common.cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="common.save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('.modal-content');
    modal.classList.remove('hidden');
    
    // Use double requestAnimationFrame to ensure the browser has painted the hidden->block change
    // before we trigger the opacity/transform transitions.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0');
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        });
    });
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('.modal-content');
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => { modal.classList.add('hidden'); }, 300);
}

function editRule(rule) {
    document.getElementById('edit_id').value = rule.id;
    document.getElementById('edit_origin').value = rule.origin;
    document.getElementById('edit_headers').value = rule.headers_arr.join(', ');
    document.getElementById('edit_max_age').value = rule.max_age;
    
    // Clear and check checkboxes
    const methods = rule.methods_arr;
    document.querySelectorAll('.edit-method-check').forEach(cb => {
        cb.checked = methods.includes(cb.dataset.method);
    });
    
    openModal('editModal');
}
</script>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
