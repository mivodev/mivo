<?php 
$title = "Selling Report";
require_once ROOT . '/app/Views/layouts/header_main.php'; 
?>

<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
    <div>
        <h1 class="text-2xl font-bold tracking-tight" data-i18n="reports.selling_title">Selling Report</h1>
        <p class="text-accents-5"><span data-i18n="reports.selling_subtitle">Sales summary and details for:</span> <span class="text-foreground font-medium"><?= htmlspecialchars($session) ?></span></p>
    </div>
    <div class="flex gap-2">
         <button onclick="location.reload()" class="btn btn-secondary">
            <i data-lucide="refresh-cw" class="w-4 h-4 mr-2"></i> <span data-i18n="reports.refresh">Refresh</span>
        </button>
        <button onclick="window.print()" class="btn btn-primary">
            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> <span data-i18n="reports.print_report">Print Report</span>
        </button>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="card bg-accents-1 border-accents-2">
        <div class="text-sm text-accents-5 uppercase font-bold tracking-wide" data-i18n="reports.total_income">Total Income</div>
        <div class="text-3xl font-bold text-green-500 mt-2">
            <?= \App\Helpers\FormatHelper::formatCurrency($totalIncome, $currency) ?>
        </div>
    </div>
    <div class="card bg-accents-1 border-accents-2">
        <div class="text-sm text-accents-5 uppercase font-bold tracking-wide" data-i18n="reports.total_vouchers">Total Vouchers Sold</div>
        <div class="text-3xl font-bold text-blue-500 mt-2">
            <?= number_format($totalVouchers, 0, ',', '.') ?>
        </div>
    </div>
</div>

<div class="space-y-4">
    <!-- Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-center no-print">
        <!-- Search -->
        <div class="relative w-full md:w-64">
             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i data-lucide="search" class="h-4 w-4 text-accents-5"></i>
            </div>
            <input type="text" id="global-search" class="form-input pl-10 w-full" placeholder="Search date..." data-i18n-placeholder="common.table.search_placeholder">
        </div>
    </div>

    <!-- Detailed Table -->
    <div class="table-container">
        <table class="table-glass" id="report-table">
            <thead>
                <tr>
                    <th data-sort="date" class="sortable cursor-pointer hover:text-foreground select-none" data-i18n="reports.date_batch">Date / Batch (Comment)</th>
                    <th class="text-right" data-i18n="reports.qty">Qty</th>
                    <th data-sort="total" class="sortable text-right cursor-pointer hover:text-foreground select-none" data-i18n="reports.total">Total</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php if (empty($report)): ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-accents-5" data-i18n="reports.no_data">No sales data found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($report as $row): ?>
                        <tr class="table-row-item"
                            data-date="<?= strtolower($row['date']) ?>"
                            data-total="<?= $row['total'] ?>">
                            <td class="font-medium"><?= htmlspecialchars($row['date']) ?></td>
                            <td class="text-right font-mono"><?= number_format($row['count']) ?></td>
                            <td class="text-right font-mono font-bold text-foreground">
                                <?= \App\Helpers\FormatHelper::formatCurrency($row['total'], $currency) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-white/10 flex items-center justify-between no-print" id="pagination-controls">
            <div class="text-sm text-accents-5">
                Showing <span id="start-idx" class="font-medium text-foreground">0</span> to <span id="end-idx" class="font-medium text-foreground">0</span> of <span id="total-count" class="font-medium text-foreground">0</span> rows
            </div>
            <div class="flex gap-2">
                <button id="prev-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.previous">Previous</button>
                <div id="page-numbers" class="flex gap-1"></div>
                <button id="next-btn" class="btn btn-sm btn-secondary" disabled data-i18n="common.next">Next</button>
            </div>
        </div>
    </div>
</div>

<script>
    class TableManager {
        constructor(rows, itemsPerPage = 15) {
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
                const date = row.dataset.date || '';
                
                if (this.filters.search && !date.includes(this.filters.search)) return false;
                
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
    
    document.addEventListener('DOMContentLoaded', () => {
        new TableManager(document.querySelectorAll('.table-row-item'), 15);
    });
</script>

<?php require_once ROOT . '/app/Views/layouts/footer_main.php'; ?>
