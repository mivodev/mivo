<?php
$title = "User Profiles";
require_once ROOT . '/app/Views/layouts/header_main.php';

// Prepare Filters Data
$uniqueModes = [];
if (!empty($profiles)) {
    foreach ($profiles as $p) {
        $m = $p['meta']['expired_mode_formatted'] ?? '';
        if(!empty($m)) $uniqueModes[$m] = $m;
    }
}
sort($uniqueModes);
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight" data-i18n="hotspot_profiles.title">User Profiles</h1>
        <p class="text-accents-5"><span data-i18n="hotspot_profiles.subtitle">Manage hotspot rate limits and pricing for session</span> <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
    </div>
    <div class="flex gap-2">
        <a href="/<?= htmlspecialchars($session) ?>/dashboard" class="btn btn-secondary">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> <span data-i18n="common.dashboard">Dashboard</span>
        </a>
        <a href="/<?= htmlspecialchars($session) ?>/hotspot/profile/add" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> <span data-i18n="hotspot_profiles.add_profile">Add Profile</span>
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center">
        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Filters & Table -->
<div class="space-y-4">
    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
        <!-- Search -->
        <div class="input-group md:w-64 z-10">
            <div class="input-icon">
                <i data-lucide="search" class="h-4 w-4"></i>
            </div>
            <input type="text" id="global-search" class="form-input-search w-full" placeholder="Search profile...">
        </div>

        <!-- Dropdowns -->
        <div class="flex gap-2 w-full md:w-auto">
            <div class="w-48">
                <select id="filter-mode" class="custom-select form-filter" data-search="true">
                    <option value="" data-i18n="hotspot_profiles.all_modes">All Expired Modes</option>
                    <?php foreach($uniqueModes as $m): ?>
                        <option value="<?= htmlspecialchars($m) ?>"><?= htmlspecialchars($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table-glass" id="profiles-table">
            <thead>
                <tr>
                    <th data-sort="name" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="hotspot_profiles.name">Name</th>
                    <th data-i18n="hotspot_profiles.shared_users">Shared Users</th>
                    <th data-i18n="hotspot_profiles.rate_limit">Rate Limit</th>
                    <th data-i18n="hotspot_profiles.parent_queue">Parent Queue</th>
                    <th data-sort="mode" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="hotspot_profiles.expired_mode">Expired Mode</th>
                    <th data-i18n="hotspot_profiles.validity">Validity</th>
                    <th data-i18n="hotspot_profiles.price">Price</th>
                    <th data-i18n="hotspot_profiles.selling_price">Selling Price</th>
                    <th data-i18n="hotspot_profiles.lock_user">Lock User</th>
                    <th class="text-right" data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php if (!empty($profiles)): ?>
                    <?php foreach ($profiles as $profile): ?>
                    <tr class="table-row-item" 
                        data-name="<?= strtolower($profile['name'] ?? '') ?>"
                        data-mode="<?= htmlspecialchars($profile['meta']['expired_mode_formatted'] ?? '') ?>">
                        
                        <td>
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center text-xs font-bold mr-3">
                                    <i data-lucide="ticket" class="w-4 h-4"></i>
                                </div>
                                <div class="text-sm font-medium text-foreground">
                                    <a href="/<?= htmlspecialchars($session) ?>/hotspot/profile/edit/<?= $profile['.id'] ?>" class="hover:underline hover:text-purple-600 dark:hover:text-purple-400">
                                        <?= htmlspecialchars($profile['name'] ?? '-') ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-sm font-semibold"><?= htmlspecialchars($profile['shared-users'] ?? '1') ?></span>
                            <span class="text-xs text-accents-5">dev</span>
                        </td>
                         <td>
                            <?php if(!empty($profile['rate-limit'])): ?>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                <?= htmlspecialchars($profile['rate-limit']) ?>
                            </span>
                            <?php else: ?>
                                <span class="text-xs text-accents-4">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['parent-queue'] ?? '-') ?>
                        </td>
                        <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['meta']['expired_mode_formatted'] ?? '') ?>
                        </td>
                        <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['meta']['validity'] ?? '') ?>
                        </td>
                        <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['meta']['price'] ?? '') ?>
                        </td>
                        <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['meta']['selling_price'] ?? '') ?>
                        </td>
                         <td class="text-sm text-accents-6">
                           <?= htmlspecialchars($profile['meta']['lock_user'] ?? '') ?>
                        </td>
                        
                        <td class="text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 table-actions-reveal">
                                <a href="/<?= htmlspecialchars($session) ?>/hotspot/profile/edit/<?= $profile['.id'] ?>" class="btn bg-blue-50 hover:bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:hover:bg-blue-900/40 border-transparent h-8 px-2 rounded transition-colors" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="/<?= htmlspecialchars($session) ?>/hotspot/profile/delete" method="POST" onsubmit="event.preventDefault(); Mivo.confirm(window.i18n ? window.i18n.t('hotspot_profiles.title') : 'Delete Profile?', window.i18n ? window.i18n.t('common.confirm_delete') : 'Are you sure you want to delete profile <?= $profile['name'] ?>?', window.i18n ? window.i18n.t('common.delete') : 'Delete', window.i18n ? window.i18n.t('common.cancel') : 'Cancel').then(res => { if(res) this.submit(); });" class="inline">
                                    <input type="hidden" name="session" value="<?= htmlspecialchars($session) ?>">
                                    <input type="hidden" name="id" value="<?= $profile['.id'] ?>">
                                    <button type="submit" class="btn bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20 dark:hover:bg-red-900/40 border-transparent h-8 px-2 rounded transition-colors" title="Delete">
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
                Showing <span id="start-idx" class="font-medium text-foreground">0</span> to <span id="end-idx" class="font-medium text-foreground">0</span> of <span id="total-count" class="font-medium text-foreground">0</span> profiles
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
                mode: ''
            };

            this.init();
        }

        init() {
            // Translate placeholder
            const searchInput = document.getElementById('global-search');
            if (searchInput && window.i18n) {
                searchInput.placeholder = window.i18n.t('common.table.search_placeholder');
            }
            this.setupListeners();
            this.update();

            // Listen for language change
            window.addEventListener('languageChanged', () => {
                const searchInput = document.getElementById('global-search');
                if (searchInput && window.i18n) {
                    searchInput.placeholder = window.i18n.t('common.table.search_placeholder');
                }
                this.render();
            });
        }

        setupListeners() {
            document.getElementById('global-search').addEventListener('input', (e) => {
                this.filters.search = e.target.value.toLowerCase();
                this.currentPage = 1;
                this.update();
            });

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
            
            document.getElementById('filter-mode').addEventListener('change', (e) => {
                this.filters.mode = e.target.value;
                this.currentPage = 1;
                this.update();
            });
        }

        update() {
            this.filteredRows = this.allRows.filter(row => {
                const name = row.dataset.name || '';
                const mode = row.dataset.mode || '';
                
                if (this.filters.search && !name.includes(this.filters.search)) return false;
                if (this.filters.mode && mode !== this.filters.mode) return false;
                
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
            
            this.elements.startIdx.textContent = total === 0 ? 0 : start + 1;
            this.elements.endIdx.textContent = end;
            this.elements.totalCount.textContent = total;
            
             // Update Text (Use Translation)
            if (window.i18n && document.getElementById('pagination-controls')) {
                 const text = window.i18n.t('common.table.showing', {
                    start: total === 0 ? 0 : start + 1,
                    end: end,
                    total: total
                });
                // Find and update the text node if possible
                const container = document.getElementById('pagination-controls').querySelector('.text-accents-5');
                 if(container) {
                      container.innerHTML = text.replace('{start}', `<span class="font-medium text-foreground">${total === 0 ? 0 : start + 1}</span>`)
                                                .replace('{end}', `<span class="font-medium text-foreground">${end}</span>`)
                                                .replace('{total}', `<span class="font-medium text-foreground">${total}</span>`);
                 }
            }
            
            this.elements.body.innerHTML = '';
            
            const pageRows = this.filteredRows.slice(start, end);
            pageRows.forEach(row => this.elements.body.appendChild(row));
            
            this.elements.prevBtn.disabled = this.currentPage === 1;
            this.elements.nextBtn.disabled = this.currentPage === maxPage || total === 0;

            if (this.elements.pageNumbers) {
                const pageText = window.i18n ? window.i18n.t('common.page_of', {current: this.currentPage, total: maxPage}) : `Page ${this.currentPage} of ${maxPage}`;
                this.elements.pageNumbers.innerHTML = `<span class="px-3 py-1 text-sm font-medium bg-accents-2 rounded text-accents-6">${pageText}</span>`;
            }

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        if (typeof CustomSelect !== 'undefined') {
            document.querySelectorAll('.custom-select').forEach(select => {
                new CustomSelect(select);
            });
        }
        
        const rows = document.querySelectorAll('.table-row-item');
        new TableManager(rows, 10);
    });
</script>
