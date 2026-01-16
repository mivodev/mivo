<?php
$title = "Settings";
$no_main_container = true;
require_once ROOT . '/app/Views/layouts/header_main.php';
?>

<!-- Sub-Navbar Navigation -->
<?php include ROOT . '/app/Views/layouts/sidebar_settings.php'; ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full flex flex-col">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Router Sessions</h1>
            <p class="text-accents-5 mt-2">Manage your stored MikroTik connections.</p>
        </div>
    </div>

    <!-- Content Area -->
    <div class="mt-8 flex-1 min-w-0" id="settings-content-area">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
                <div class="hidden md:block">
                     <!-- Spacer or Breadcrumbs if needed -->
                </div>
                <a href="/settings/add" class="btn btn-primary w-full md:w-auto">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add Router
                </a>
            </div>

            <?php if (empty($routers)): ?>
                <div class="card flex flex-col items-center justify-center py-16 text-center border-dashed">
                    <div class="rounded-full bg-accents-1 p-4 mb-4">
                        <i data-lucide="server-off" class="w-8 h-8 text-accents-4"></i>
                    </div>
                    <h3 class="text-lg font-medium mb-2">No routers configured</h3>
                    <p class="text-accents-5 mb-6 max-w-sm mx-auto">Connect your first MikroTik router to start managing hotspots and vouchers.</p>
                    <a href="/settings/add" class="btn btn-primary">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Connect Router
                    </a>
                </div>
            <?php else: ?>
                <div class="table-container">
                    <table class="table-glass">
                        <thead>
                            <tr>
                                <th scope="col">Session Name</th>
                                <th scope="col">Hotspot Name</th>
                                <th scope="col">IP Address</th>
                                <th scope="col" class="relative text-right">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($routers as $router): ?>
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded bg-accents-2 flex items-center justify-center text-xs font-bold mr-3">
                                            <?= strtoupper(substr($router['session_name'], 0, 2)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-foreground flex items-center gap-2">
                                                <?= htmlspecialchars($router['session_name']) ?>
                                                <?php if(isset($router['quick_access']) && $router['quick_access'] == 1): ?>
                                                    <i data-lucide="star" class="w-3 h-3 text-yellow-500 fill-current" title="Quick Access Enabled"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-xs text-accents-5">ID: <?= $router['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-sm text-foreground"><?= htmlspecialchars($router['hotspot_name']) ?></div>
                                </td>
                                <td>
                                    <div class="text-sm text-accents-5 font-mono"><?= htmlspecialchars($router['ip_address']) ?></div>
                                </td>
                                <td class="text-right text-sm font-medium flex justify-end gap-2">
                                    <a href="/<?= htmlspecialchars($router['session_name']) ?>/dashboard" class="btn btn-secondary btn-sm h-8 px-3">
                                        Open
                                    </a>
                                    <a href="/settings/edit/<?= $router['id'] ?>" class="btn btn-secondary btn-sm h-8 px-3" title="Edit">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="/settings/delete" method="POST" onsubmit="event.preventDefault(); Mivo.confirm('Disconnect Router?', 'Are you sure you want to disconnect <?= htmlspecialchars($router['session_name']) ?>?', 'Disconnect', 'Cancel').then(res => { if(res) this.submit(); });" class="inline">
                                        <input type="hidden" name="id" value="<?= $router['id'] ?>">
                                        <button type="submit" class="btn hover:bg-red-100 dark:hover:bg-red-900/30 text-red-600 border border-transparent h-8 px-2" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="bg-accents-1 px-4 py-3 border-t border-accents-2 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-0 sm:px-6">
                         <div class="text-sm text-accents-5">
                            Showing all <?= count($routers) ?> stored sessions
                         </div>
                         <a href="/settings/add" class="btn btn-primary btn-sm w-full sm:w-auto justify-center">
                            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Add New
                         </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
