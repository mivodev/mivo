<?php
$title = "Hotspot Users";
require_once ROOT . '/app/Views/layouts/header_main.php';

// Prepare Filters Data
$uniqueProfiles = [];
$uniqueComments = [];
if (!empty($users)) {
    foreach ($users as $u) {
        $p = $u['profile'] ?? 'default';
        $c = $u['comment'] ?? '';
        
        $uniqueProfiles[$p] = $p; // Key-Value distinct
        if(!empty($c)) $uniqueComments[$c] = $c;
    }
}
sort($uniqueProfiles);
sort($uniqueComments);
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight" data-i18n="hotspot_users.title">Hotspot Users</h1>
        <p class="text-accents-5"><span data-i18n="hotspot_users.subtitle">Manage vouchers and user accounts for session</span>: <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
    </div>
    <div class="flex gap-2">
        <a href="/<?= htmlspecialchars($session) ?>/dashboard" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> <span data-i18n="common.dashboard">Dashboard</span>
        </a>
        <a href="/<?= htmlspecialchars($session) ?>/hotspot/add" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> <span data-i18n="hotspot_users.add_user">Add User</span>
        </a>
    </div>
</div>

<?php if ($error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center">
        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Batch Action Toolbar -->
<div id="batch-toolbar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-foreground text-background px-6 py-3 rounded-full shadow-lg z-50 flex items-center gap-4 transition-all duration-300 translate-y-20 opacity-0">
    <span class="text-sm font-medium"><span id="selected-count">0</span> <span data-i18n="common.selected">Selected</span></span>
    <div class="h-4 w-px bg-background/20"></div>
    <button onclick="printSelected()" class="flex items-center gap-2 hover:text-accents-2 transition-colors font-bold text-sm">
        <i data-lucide="printer" class="w-4 h-4"></i> <span data-i18n="common.print">Print</span>
    </button>
    <button onclick="deleteSelected()" class="flex items-center gap-2 text-red-400 hover:text-red-300 transition-colors font-bold text-sm">
        <i data-lucide="trash-2" class="w-4 h-4"></i> <span data-i18n="common.delete">Delete</span>
    </button>
</div>

<!-- Filters & Table -->
<div class="space-y-4">
    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
        <!-- Search -->
        <div class="input-group md:w-64 z-10">
            <div class="input-icon">
                <i data-lucide="search" class="h-4 w-4"></i>
            </div>
            <input type="text" id="global-search" class="form-input-search w-full" placeholder="Search user..." data-i18n="common.table.search_placeholder">
        </div>

        <!-- Dropdowns -->
        <div class="flex gap-2 w-full md:w-auto">
            <div class="w-40">
                <select id="filter-profile" class="custom-select form-filter" data-search="true">
                    <option value="" data-i18n="common.all_profiles">All Profiles</option>
                    <?php foreach($uniqueProfiles as $p): ?>
                        <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="w-40">
                <select id="filter-comment" class="custom-select form-filter" data-search="true">
                    <option value="" data-i18n="common.all_comments">All Comments</option>
                    <?php foreach($uniqueComments as $c): ?>
                        <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <!-- Table Container -->
    <div class="table-container">
        <table class="table-glass" id="users-table">
            <thead>
                <tr>
                    <th scope="col" class="px-4 py-3 w-10">
                        <input type="checkbox" id="select-all" class="checkbox">
                    </th>
                    <th data-sort="name" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="hotspot_users.name">Name</th>
                    <th data-sort="profile" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="hotspot_users.profile">Profile</th>
                    <th data-i18n="hotspot_users.uptime_limit">Uptime / Limit</th>
                    <th data-i18n="hotspot_users.bytes_in_out">Bytes In/Out</th>
                    <th data-sort="comment" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="hotspot_users.comment">Comment</th>
                    <th class="relative text-right" data-i18n="common.actions">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $user): ?>
                    <tr class="table-row-item" 
                        data-name="<?= strtolower($user['name'] ?? '') ?>" 
                        data-profile="<?= $user['profile'] ?? 'default' ?>" 
                        data-comment="<?= htmlspecialchars($user['comment'] ?? '') ?>">
                        
                        <td class="px-4 py-4">
                            <input type="checkbox" name="selected_users[]" value="<?= htmlspecialchars($user['.id']) ?>" class="user-checkbox checkbox">
                        </td>
                        <td>
                            <div class="flex items-center w-full">
                                <div class="h-8 w-8 rounded bg-accents-2 flex items-center justify-center text-xs font-bold mr-3 text-accents-6 flex-shrink-0">
                                    <i data-lucide="user" class="w-4 h-4"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-4">
                                        <div class="text-sm font-medium text-foreground truncate"><?= htmlspecialchars($user['name'] ?? '-') ?></div>
                                        <?php 
                                            $status = \App\Helpers\HotspotHelper::getUserStatus($user);
                                            echo \App\Helpers\ViewHelper::badge($status);
                                        ?>
                                    </div>
                                    <div class="text-xs text-accents-5"><?= htmlspecialchars($user['password'] ?? '******') ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                <?= htmlspecialchars($user['profile'] ?? 'default') ?>
                            </span>
                        </td>
                        <td>
                            <div class="text-sm text-foreground"><?= \App\Helpers\FormatHelper::elapsedTime($user['uptime'] ?? '0s') ?></div>
                            <div class="text-xs text-accents-5">Limit: <?= \App\Helpers\FormatHelper::elapsedTime($user['limit-uptime'] ?? 'unlimited') ?></div>
                        </td>
                        <td>
                            <div class="text-xs text-accents-5 flex flex-col gap-1">
                                <span class="flex items-center"><i data-lucide="arrow-down" class="w-3 h-3 mr-1 text-green-500"></i> <?= \App\Helpers\FormatHelper::formatBytes($user['bytes-in'] ?? 0) ?></span>
                                <span class="flex items-center"><i data-lucide="arrow-up" class="w-3 h-3 mr-1 text-blue-500"></i> <?= \App\Helpers\FormatHelper::formatBytes($user['bytes-out'] ?? 0) ?></span>
                            </div>
                        </td>
                        <td>
                            <div class="text-sm text-accents-5 italic"><?= htmlspecialchars($user['comment'] ?? '-') ?></div>
                        </td>
                        <td class="text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 table-actions-reveal">
                                <button onclick="printUser('<?= htmlspecialchars($user['.id']) ?>')" class="btn-icon" title="Print">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </button>
                                <a href="/<?= htmlspecialchars($session) ?>/hotspot/user/edit/<?= urlencode($user['.id']) ?>" class="btn-icon inline-flex items-center justify-center" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="/<?= htmlspecialchars($session) ?>/hotspot/delete" method="POST" onsubmit="event.preventDefault(); Mivo.confirm('Delete User?', 'Are you sure you want to delete user <?= htmlspecialchars($user['name'] ?? '') ?>?', 'Delete', 'Cancel').then(res => { if(res) this.submit(); });" class="inline">
                                    <input type="hidden" name="session" value="<?= htmlspecialchars($session) ?>">
                                    <input type="hidden" name="id" value="<?= $user['.id'] ?>">
                                    <button type="submit" class="btn-icon-danger" title="Delete">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-white/10 flex items-center justify-between" id="pagination-controls">
            <div class="text-sm text-accents-5">
                 <span id="pagination-text">Showing <span id="start-idx" class="font-medium text-foreground">0</span> to <span id="end-idx" class="font-medium text-foreground">0</span> of <span id="total-count" class="font-medium text-foreground">0</span> users</span>
            </div>
            <div class="flex gap-2">
                <button id="prev-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.previous">Previous</button>
                <div id="page-numbers" class="flex gap-1"></div>
                <button id="next-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.next">Next</button>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
<script>
    class TableManager {
        constructor(rows, itemsPerPage = 10) {
            this.allRows = Array.from(rows);
            this.filteredRows = this.allRows;
            this.itemsPerPage = itemsPerPage;
            this.currentPage = 1;

            this.elements = {
                body: document.getElementById('table-body'),
                startIdx: document.getElementById('start-idx'),
                endIdx: document.getElementById('end-idx'),
                totalCount: document.getElementById('total-count'),
                prevBtn: document.getElementById('prev-btn'),
                nextBtn: document.getElementById('next-btn'),
                pageNumbers: document.getElementById('page-numbers')
            };

            this.filters = {
                search: '',
                profile: '',
                comment: ''
            };

            this.init();
        }

        init() {
            this.setupListeners();
            this.update();
        }

        setupListeners() {
            // Search Input
            document.getElementById('global-search').addEventListener('input', (e) => {
                this.filters.search = e.target.value.toLowerCase();
                this.currentPage = 1;
                this.update();
            });

            // Prev/Next
            this.elements.prevBtn.addEventListener('click', () => {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    this.render();
                }
            });

            this.elements.nextBtn.addEventListener('click', () => {
                const maxPage = Math.ceil(this.filteredRows.length / this.itemsPerPage);
                if (this.currentPage < maxPage) {
                    this.currentPage++;
                    this.render();
                }
            });
            
            // Custom Select Listener (Mutation Observer or custom event if we emitted one, 
            // but for now relying on underlying SELECT change or custom-select class behavior)
            // Since CustomSelect updates the original Select, we listen to change on original select
            document.getElementById('filter-profile').addEventListener('change', (e) => {
                this.filters.profile = e.target.value;
                this.currentPage = 1;
                this.update();
            });
            
            document.getElementById('filter-comment').addEventListener('change', (e) => {
                this.filters.comment = e.target.value;
                this.currentPage = 1;
                this.update();
            });
            
            // Re-bind actions when external CustomSelect updates the select value
            // CustomSelect triggers 'change' event on original select, so standard listener works!

             // Listen for language change to update pagination text
             window.addEventListener('languageChanged', () => {
                this.render();
            });
        }

        update() {
            // Apply Filters
            this.filteredRows = this.allRows.filter(row => {
                const name = row.dataset.name || '';
                const comment = (row.dataset.comment || '').toLowerCase(); // dataset comment value
                const profile = row.dataset.profile || '';
                
                // 1. Search (Name or Comment)
                if (this.filters.search) {
                     const matchName = name.includes(this.filters.search);
                     const matchComment = comment.includes(this.filters.search);
                     if (!matchName && !matchComment) return false;
                }
                
                // 2. Profile
                if (this.filters.profile && profile !== this.filters.profile) return false;
                
                // 3. Comment (Exact match for dropdown)
                if (this.filters.comment && row.dataset.comment !== this.filters.comment) return false;
                
                return true;
            });

            this.render();
        }

        render() {
            const total = this.filteredRows.length;
            const maxPage = Math.ceil(total / this.itemsPerPage) || 1;
            
            if (this.currentPage > maxPage) this.currentPage = maxPage;
            
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = Math.min(start + this.itemsPerPage, total);
            
            // Update Text (Use Translation)
            if (window.i18n) {
                const text = window.i18n.t('common.table.showing', {
                    start: total === 0 ? 0 : start + 1,
                    end: end,
                    total: total
                });
                document.getElementById('pagination-text').textContent = text;
            } else {
                 this.elements.startIdx.textContent = total === 0 ? 0 : start + 1;
                 this.elements.endIdx.textContent = end;
                 this.elements.totalCount.textContent = total;
            }
            
            // Clear & Append Rows
            this.elements.body.innerHTML = '';
            
            const pageRows = this.filteredRows.slice(start, end);
            pageRows.forEach(row => this.elements.body.appendChild(row));
            
            // Update Buttons
            this.elements.prevBtn.disabled = this.currentPage === 1;
            this.elements.nextBtn.disabled = this.currentPage === maxPage || total === 0;

            if (this.elements.pageNumbers) {
                 const pageText = window.i18n ? window.i18n.t('common.page_of', {current: this.currentPage, total: maxPage}) : `Page ${this.currentPage} of ${maxPage}`;
                this.elements.pageNumbers.innerHTML = `<span class="px-3 py-1 text-sm font-medium bg-accents-2 rounded text-accents-6">${pageText}</span>`;
            }

            // Re-init Icons for new rows
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Update Checkbox Logic (Select All should act on visible?)
            // We usually reset "Select All" check when page changes
            document.getElementById('select-all').checked = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        // Init Custom Selects
        if (typeof CustomSelect !== 'undefined') {
            document.querySelectorAll('.custom-select').forEach(select => {
                new CustomSelect(select);
            });
        }
        
        // Init Table
        const rows = document.querySelectorAll('.table-row-item');
        const manager = new TableManager(rows, 10);
        
        // --- Toolbar Logic (Copied/Adapted) ---
        const selectAll = document.getElementById('select-all');
        const toolbar = document.getElementById('batch-toolbar');
        const countSpan = document.getElementById('selected-count');
        const tableBody = document.getElementById('table-body'); // Dynamic body

        function updateToolbar() {
            const checked = document.querySelectorAll('.user-checkbox:checked');
            countSpan.textContent = checked.length;
            
            if (checked.length > 0) {
                toolbar.classList.remove('translate-y-20', 'opacity-0');
            } else {
                toolbar.classList.add('translate-y-20', 'opacity-0');
            }
        }

        selectAll.addEventListener('change', (e) => {
            const isChecked = e.target.checked;
            // Only select visible rows on current page
            const visibleCheckboxes = tableBody.querySelectorAll('.user-checkbox');
            visibleCheckboxes.forEach(cb => cb.checked = isChecked);
            updateToolbar();
        });

        // Event Delegation for dynamic rows
        tableBody.addEventListener('change', (e) => {
            if (e.target.classList.contains('user-checkbox')) {
                updateToolbar();
                if (!e.target.checked) selectAll.checked = false;
            }
        });
    });
    
    // Actions
    function printUser(id) {
        const width = 400;
        const height = 600;
        const left = (window.innerWidth - width) / 2;
        const top = (window.innerHeight - height) / 2;
        const session = '<?= htmlspecialchars($session) ?>';
        const url = `/${session}/hotspot/print/${encodeURIComponent(id)}`;
        window.open(url, `PrintUser`, `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
    }
    
    function printSelected() {
        const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return alert(window.i18n ? window.i18n.t('hotspot_users.no_users_selected') : "No users selected.");
        
        const width = 800;
        const height = 600;
        const left = (window.innerWidth - width) / 2;
        const top = (window.innerHeight - height) / 2;
        const session = '<?= htmlspecialchars($session) ?>';
        const ids = selected.map(id => encodeURIComponent(id)).join(',');
        const url = `/${session}/hotspot/print-batch?ids=${ids}`;
        window.open(url, `PrintBatch`, `width=${width},height=${height},top=${top},left=${left},scrollbars=yes`);
    }
    
    function deleteSelected() {
        const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
        if (selected.length === 0) return alert(window.i18n ? window.i18n.t('hotspot_users.no_users_selected') : "Please select at least one user.");
        
        const title = window.i18n ? window.i18n.t('common.delete') : 'Delete Users?';
        const msg = window.i18n ? window.i18n.t('common.confirm_delete') : `Are you sure you want to delete ${selected.length} users?`;
        
        Mivo.confirm(title, msg, window.i18n.t('common.delete'), window.i18n.t('common.cancel')).then(res => {
            if (!res) return;

            // Create a form to submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/<?= htmlspecialchars($session) ?>/hotspot/delete'; // Re-uses the delete endpoint
            
            const sessionInput = document.createElement('input');
            sessionInput.type = 'hidden';
            sessionInput.name = 'session';
            sessionInput.value = '<?= htmlspecialchars($session) ?>';
            form.appendChild(sessionInput);
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = selected.join(','); // Comma separated IDs
            form.appendChild(idInput);
            
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    }
</script>
