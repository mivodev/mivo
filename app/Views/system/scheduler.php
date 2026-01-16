<?php
$title = "Scheduler";
require_once ROOT . '/app/Views/layouts/header_main.php';
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight" data-i18n="system_menu.scheduler">Scheduler</h1>
        <p class="text-accents-5"><span data-i18n="system_tools.scheduler_subtitle">Manage RouterOS automated tasks for:</span> <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
    </div>
    <div class="flex gap-2">
         <button onclick="location.reload()" class="btn btn-secondary">
            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> <span data-i18n="reports.refresh">Refresh</span>
        </button>
        <button onclick="openModal('addModal')" class="btn btn-primary">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i> <span data-i18n="system_tools.add_task">Add Task</span>
        </button>
    </div>
</div>

<?php if (isset($error) && $error): ?>
    <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 flex items-center">
        <i data-lucide="alert-circle" class="w-5 h-5 mr-3"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="space-y-4">
     <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
        <!-- Search -->
        <div class="input-group md:w-64 z-10">
             <div class="input-icon">
                <i data-lucide="search" class="h-4 w-4"></i>
            </div>
            <input type="text" id="global-search" class="form-input-search w-full" placeholder="Search task name..." data-i18n-placeholder="common.table.search_placeholder">
        </div>
    </div>

    <div class="table-container">
        <table class="table-glass" id="scheduler-table">
            <thead>
                <tr>
                    <th data-sort="name" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="system_tools.table_name">Name</th>
                    <th data-i18n="system_tools.interval">Interval</th>
                    <th data-i18n="system_tools.next_run">Next Run</th>
                    <th data-sort="status" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="system_tools.status">Status</th>
                    <th class="text-right" data-i18n="common.actions">Actions</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php if (!empty($schedulers) && is_array($schedulers)): ?>
                    <?php foreach ($schedulers as $task): 
                            $status = ($task['disabled'] === 'true') ? 'disabled' : 'enabled';
                    ?>
                    <tr class="table-row-item"
                            data-name="<?= strtolower($task['name']) ?>"
                            data-status="<?= $status ?>">
                        
                        <td>
                            <div class="text-sm font-medium text-foreground"><?= htmlspecialchars($task['name']) ?></div>
                            <div class="text-xs text-accents-5 truncate max-w-[200px]"><?= htmlspecialchars($task['on-event']) ?></div>
                        </td>
                        <td class="text-sm text-accents-5"><?= htmlspecialchars($task['interval']) ?></td>
                        <td class="text-sm text-accents-5"><?= htmlspecialchars($task['next-run'] ?? '-') ?></td>
                        <td>
                                <?php if ($task['disabled'] === 'true'): ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-accents-2 text-accents-5" data-i18n="system_tools.disabled">Disabled</span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400" data-i18n="system_tools.enabled">Enabled</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 table-actions-reveal">
                                <button onclick="editTask(<?= htmlspecialchars(json_encode($task)) ?>)" class="btn-icon" title="Edit">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <form action="/<?= $session ?>/system/scheduler/delete" method="POST" onsubmit="event.preventDefault(); Mivo.confirm(window.i18n ? window.i18n.t('system_tools.delete_task') : 'Delete Task?', window.i18n ? window.i18n.t('common.confirm_delete') : 'Are you sure you want to delete task <?= htmlspecialchars($task['name']) ?>?', window.i18n ? window.i18n.t('common.delete') : 'Delete', window.i18n ? window.i18n.t('common.cancel') : 'Cancel').then(res => { if(res) this.submit(); });" class="inline">
                                    <input type="hidden" name="id" value="<?= $task['.id'] ?>">
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
                Showing <span id="start-idx" class="font-medium text-foreground">0</span> to <span id="end-idx" class="font-medium text-foreground">0</span> of <span id="total-count" class="font-medium text-foreground">0</span> tasks
            </div>
            <div class="flex gap-2">
                <button id="prev-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.previous">Previous</button>
                <div id="page-numbers" class="flex gap-1"></div>
                <button id="next-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.next">Next</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 z-50 hidden opacity-0 transition-opacity duration-300" role="dialog" aria-modal="true">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity duration-300" onclick="closeModal('addModal')"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-lg transition-all duration-300 scale-95 opacity-0 modal-content">
        <div class="card shadow-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold" data-i18n="system_tools.add_title">Add Scheduler Task</h3>
                <button onclick="closeModal('addModal')" class="text-accents-5 hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="/<?= $session ?>/system/scheduler/store" method="POST" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.name">Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.interval">Interval</label>
                        <input type="text" name="interval" class="form-control" value="1d 00:00:00" placeholder="1d 00:00:00">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.start_date">Start Date</label>
                        <input type="text" name="start_date" class="form-control" value="Jan/01/1970">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.start_time">Start Time</label>
                        <input type="text" name="start_time" class="form-control" value="00:00:00">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="system_tools.on_event">On Event (Script)</label>
                    <textarea name="on_event" class="form-control font-mono text-xs h-24" placeholder="/system reboot"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="system_tools.comment">Comment</label>
                    <input type="text" name="comment" class="form-control">
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="closeModal('addModal')" class="btn btn-secondary mr-2" data-i18n="common.cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="system_tools.save_task">Save Task</button>
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
                <h3 class="text-lg font-bold" data-i18n="system_tools.edit_title">Edit Scheduler Task</h3>
                <button onclick="closeModal('editModal')" class="text-accents-5 hover:text-foreground">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <form action="/<?= $session ?>/system/scheduler/update" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.name">Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.interval">Interval</label>
                        <input type="text" name="interval" id="edit_interval" class="form-control">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.start_date">Start Date</label>
                        <input type="text" name="start_date" id="edit_start_date" class="form-control">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" data-i18n="system_tools.start_time">Start Time</label>
                        <input type="text" name="start_time" id="edit_start_time" class="form-control">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="system_tools.on_event">On Event (Script)</label>
                    <textarea name="on_event" id="edit_on_event" class="form-control font-mono text-xs h-24"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1" data-i18n="system_tools.comment">Comment</label>
                    <input type="text" name="comment" id="edit_comment" class="form-control">
                </div>
                <div class="flex justify-end pt-4">
                    <button type="button" onclick="closeModal('editModal')" class="btn btn-secondary mr-2" data-i18n="common.cancel">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-i18n="system_tools.update_task">Update Task</button>
                </div>
            </form>
        </div>
    </div>
</div>

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

            this.filters = { search: '' };
            this.init();
        }

        init() {
            // Translate placeholder
            const searchInput = document.getElementById('global-search');
            if (searchInput && window.i18n) {
                searchInput.placeholder = window.i18n.t('common.table.search_placeholder');
            }
            document.getElementById('global-search').addEventListener('input', (e) => {
                this.filters.search = e.target.value.toLowerCase();
                this.currentPage = 1;
                this.update();
            });
            
            this.elements.prevBtn.addEventListener('click', () => { if(this.currentPage > 1) { this.currentPage--; this.render(); } });
            this.elements.nextBtn.addEventListener('click', () => { 
                const max = Math.ceil(this.filteredRows.length / this.itemsPerPage);
                if(this.currentPage < max) { this.currentPage++; this.render(); } 
            });

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

        update() {
            this.filteredRows = this.allRows.filter(row => {
                const name = row.dataset.name || '';
                
                if (this.filters.search && !name.includes(this.filters.search)) return false;
                
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
            this.filteredRows.slice(start, end).forEach(row => this.elements.body.appendChild(row));
            
            this.elements.prevBtn.disabled = this.currentPage === 1;
            this.elements.nextBtn.disabled = this.currentPage === maxPage || total === 0;

            if (this.elements.pageNumbers) {
                 const pageText = window.i18n ? window.i18n.t('common.page_of', {current: this.currentPage, total: maxPage}) : `Page ${this.currentPage} of ${maxPage}`;
                this.elements.pageNumbers.innerHTML = `<span class="px-3 py-1 text-sm font-medium bg-accents-2 rounded text-accents-6">${pageText}</span>`;
            }

            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    }

function openModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('.modal-content');
    
    modal.classList.remove('hidden');
    // Force reflow
    void modal.offsetWidth; 
    
    modal.classList.remove('opacity-0');
    content.classList.remove('scale-95', 'opacity-0');
    content.classList.add('scale-100', 'opacity-100');
}

function closeModal(id) {
    const modal = document.getElementById(id);
    const content = modal.querySelector('.modal-content');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300); // Match duration-300
}

function editTask(task) {
    document.getElementById('edit_id').value = task['.id'];
    document.getElementById('edit_name').value = task['name'];
    document.getElementById('edit_interval').value = task['interval'];
    document.getElementById('edit_start_date').value = task['start-date'];
    document.getElementById('edit_start_time').value = task['start-time'];
    document.getElementById('edit_on_event').value = task['on-event'];
    document.getElementById('edit_comment').value = task['comment'] ?? '';
    
    openModal('editModal');
}

    document.addEventListener('DOMContentLoaded', () => {
        new TableManager(document.querySelectorAll('.table-row-item'), 10);
    });
</script>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
