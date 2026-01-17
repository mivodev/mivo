/**
 * Mivo Component: Datatable
 * A simple, lightweight, client-side datatable.
 */
class DataTable {
    constructor(tableSelector, options = {}) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) return;

        this.tbody = this.table.querySelector('tbody');
        this.rows = Array.from(this.tbody.querySelectorAll('tr'));
        this.originalRows = [...this.rows]; 
        
        this.options = {
            itemsPerPage: 10,
            searchable: true,
            pagination: true,
            filters: [], 
            ...options
        };

        this.currentPage = 1;
        this.searchQuery = '';
        this.activeFilters = {}; 
        this.filteredRows = [...this.originalRows];
        
        // Listen for language changes via Mivo
        if (window.Mivo) {
            window.Mivo.on('languageChanged', () => {
                this.reTranslate();
                this.render();
            });
        }
        
        // Wait for I18n readiness if available
        if (window.i18n && window.i18n.ready) {
             window.i18n.ready.then(() => this.init());
        } else {
             this.init();
        }
    }

    reTranslate() {
        const i18n = window.Mivo?.modules?.I18n || window.i18n;
        if (!i18n) return;

        // Labels
        const labels = this.wrapper.querySelectorAll('.datatable-label');
        labels.forEach(l => l.textContent = i18n.t('common.table.entries_per_page'));

        // Placeholder
        const searchInput = this.wrapper.querySelector('input.form-input-search');
        if (searchInput) searchInput.placeholder = i18n.t('common.table.search_placeholder');

        // "All" option
        const perPageSelect = this.wrapper.querySelector('select.form-filter');
        if (perPageSelect) {
            const allOption = Array.from(perPageSelect.options).find(opt => opt.value === "-1");
            if (allOption) {
                allOption.text = i18n.t('common.table.all');
                // Refresh custom select UI if needed
                if (window.Mivo?.components?.Select) {
                     const instance = window.Mivo.components.Select.get(perPageSelect.id || '');
                     if (instance) instance.refresh();
                }
            }
        }
    }

    init() {
        const i18n = window.Mivo?.modules?.I18n || window.i18n;

        // Wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'datatable-wrapper space-y-4';
        this.table.parentNode.insertBefore(this.wrapper, this.table);
        
        // Header Controls
        const header = document.createElement('div');
        header.className = 'flex flex-col sm:flex-row justify-between items-center gap-4 mb-4';
        
        // Left Controls
        const controlsLeft = document.createElement('div');
        controlsLeft.className = 'flex items-center gap-3 w-full sm:w-auto flex-wrap';
        
        // Per Page Select
        const perPageSelect = document.createElement('select');
        perPageSelect.className = 'form-filter w-20';
        // Add ID for CustomSelect registry if needed
        perPageSelect.id = 'dt-perpage-' + Math.random().toString(36).substr(2, 9);
        
        [5, 10, 25, 50, 100].forEach(num => {
            const opt = document.createElement('option');
            opt.value = num;
            opt.text = num;
            if (num === this.options.itemsPerPage) opt.selected = true;
            perPageSelect.appendChild(opt);
        });
        
        // All Option
        const allOpt = document.createElement('option');
        allOpt.value = -1;
        allOpt.text = i18n ? i18n.t('common.table.all') : 'All';
        perPageSelect.appendChild(allOpt);

        perPageSelect.addEventListener('change', (e) => {
            const val = parseInt(e.target.value);
            this.options.itemsPerPage = val === -1 ? this.originalRows.length : val;
            this.currentPage = 1;
            this.render();
        });

        // Label
        const label = document.createElement('span');
        label.className = 'text-sm text-accents-5 whitespace-nowrap datatable-label';
        label.textContent = i18n ? i18n.t('common.table.entries_per_page') : 'entries per page';

        controlsLeft.appendChild(perPageSelect);
        controlsLeft.appendChild(label);
        
        // Init Custom Select using Mivo Component
        if (window.Mivo?.components?.Select) {
             new window.Mivo.components.Select(perPageSelect);
        }

        // Filters
        if (this.options.filters && this.options.filters.length > 0) {
            this.options.filters.forEach(config => this.initFilter(config, controlsLeft));
        }

        header.appendChild(controlsLeft);

        // Search
        if (this.options.searchable) {
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'input-group sm:w-64 z-10';
            const placeholder = i18n ? i18n.t('common.table.search_placeholder') : 'Search...';
            
            searchWrapper.innerHTML = `
                <div class="input-icon">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" class="form-input-search w-full" placeholder="${placeholder}">
            `;
            const input = searchWrapper.querySelector('input');
            input.addEventListener('input', (e) => this.handleSearch(e.target.value));
            header.appendChild(searchWrapper);
        }

        this.wrapper.appendChild(header);
        
        // Table Container
        this.tableWrapper = document.createElement('div');
        this.tableWrapper.className = 'rounded-md border border-accents-2 overflow-x-auto bg-white/30 dark:bg-black/30 backdrop-blur-sm';
        this.tableWrapper.appendChild(this.table);
        this.wrapper.appendChild(this.tableWrapper);
        
        if (typeof lucide !== 'undefined') lucide.createIcons({ root: header });

        // Pagination
        if (this.options.pagination) {
            this.paginationContainer = document.createElement('div');
            this.paginationContainer.className = 'flex items-center justify-between px-2';
            this.wrapper.appendChild(this.paginationContainer);
        }

        this.render();
    }

    initFilter(config, container) {
        const colIndex = config.index;
        const values = new Set();
        this.originalRows.forEach(row => {
            const cell = row.cells[colIndex];
            if (cell) {
                const text = cell.innerText.trim();
                if(text && text !== '-' && text !== '') values.add(text);
            }
        });

        const select = document.createElement('select');
        select.className = 'form-filter datatable-select';
        
        const defOpt = document.createElement('option');
        defOpt.value = '';
        defOpt.text = config.label;
        select.appendChild(defOpt);

        Array.from(values).sort().forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.text = val;
            select.appendChild(opt);
        });

        select.addEventListener('change', (e) => {
            const val = e.target.value;
            if (val === '') delete this.activeFilters[colIndex];
            else this.activeFilters[colIndex] = val;
            
            this.currentPage = 1;
            this.filterRows();
            this.render();
        });

        container.appendChild(select);
        
        if (window.Mivo?.components?.Select) {
             new window.Mivo.components.Select(select);
        }
    }

    handleSearch(query) {
        this.searchQuery = query.toLowerCase();
        this.currentPage = 1;
        this.filterRows();
        this.render();
    }

    filterRows() {
        this.filteredRows = this.originalRows.filter(row => {
            let matchesSearch = true;
            if (this.searchQuery) {
                matchesSearch = row.innerText.toLowerCase().includes(this.searchQuery);
            }

            let matchesFilters = true;
            for (const [colIndex, filterValue] of Object.entries(this.activeFilters)) {
                const cell = row.cells[colIndex];
                if (!cell || cell.innerText.trim() !== filterValue) {
                    matchesFilters = false;
                    break;
                }
            }

            return matchesSearch && matchesFilters;
        });
    }

    render() {
        const i18n = window.Mivo?.modules?.I18n || window.i18n;
        const totalItems = this.filteredRows.length;
        const totalPages = Math.ceil(totalItems / this.options.itemsPerPage);
        
        if (this.currentPage > totalPages) this.currentPage = totalPages || 1;
        if (this.currentPage < 1) this.currentPage = 1;

        const start = (this.currentPage - 1) * this.options.itemsPerPage;
        const end = start + this.options.itemsPerPage;
        const currentItems = this.filteredRows.slice(start, end);

        this.tbody.innerHTML = '';
        if (currentItems.length > 0) {
            currentItems.forEach(row => this.tbody.appendChild(row));
        } else {
            const emptyRow = document.createElement('tr');
            const noMatchText = i18n ? i18n.t('common.table.no_match') : 'No match found.';
            emptyRow.innerHTML = `
                <td colspan="100%" class="px-6 py-12 text-center text-accents-5">
                    <span class="text-sm">${noMatchText}</span>
                </td>
            `;
            this.tbody.appendChild(emptyRow);
        }

        if (this.options.pagination) {
            this.renderPagination(totalItems, totalPages, start + 1, Math.min(end, totalItems), i18n);
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    renderPagination(totalItems, totalPages, start, end, i18n) {
        if (totalItems === 0) {
             this.paginationContainer.innerHTML = '';
             return;
        }

        const showingText = i18n ? i18n.t('common.table.showing', {start, end, total: totalItems}) : `Showing ${start} to ${end} of ${totalItems}`;
        const previousText = i18n ? i18n.t('common.previous') : 'Previous';
        const nextText = i18n ? i18n.t('common.next') : 'Next';
        const pageText = i18n ? i18n.t('common.page_of', {current: this.currentPage, total: totalPages}) : `Page ${this.currentPage} of ${totalPages}`;

        this.paginationContainer.innerHTML = `
            <div class="text-sm text-accents-5">
                ${showingText}
            </div>
            <div class="flex items-center gap-2">
                <button class="btn-prev btn btn-secondary py-1 px-3 text-xs disabled:opacity-50 disabled:cursor-not-allowed" ${this.currentPage === 1 ? 'disabled' : ''}>
                    ${previousText}
                </button>
                <div class="text-sm font-medium">${pageText}</div>
                <button class="btn-next btn btn-secondary py-1 px-3 text-xs disabled:opacity-50 disabled:cursor-not-allowed" ${this.currentPage === totalPages ? 'disabled' : ''}>
                    ${nextText}
                </button>
            </div>
        `;

        this.paginationContainer.querySelector('.btn-prev').addEventListener('click', () => {
            if (this.currentPage > 1) {
                this.currentPage--;
                this.render();
            }
        });

        this.paginationContainer.querySelector('.btn-next').addEventListener('click', () => {
            if (this.currentPage < totalPages) {
                this.currentPage++;
                this.render();
            }
        });
    }
}

// Register as Mivo Component
if (window.Mivo) {
    window.Mivo.registerComponent('Datatable', DataTable);
    // Expose as window global for simpler backward compatibility if typically invoked via new SimpleDataTable()
    window.SimpleDataTable = DataTable;
} else {
    window.SimpleDataTable = DataTable;
}
