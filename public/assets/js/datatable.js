class SimpleDataTable {
        constructor(tableSelector, options = {}) {
        this.table = document.querySelector(tableSelector);
        if (!this.table) return;

        this.tbody = this.table.querySelector('tbody');
        this.rows = Array.from(this.tbody.querySelectorAll('tr'));
        this.originalRows = [...this.rows]; // Keep copy
        
        this.options = {
            itemsPerPage: 10,
            searchable: true,
            pagination: true,
            filters: [], // Array of { index: number, label: string }
            ...options
        };

        this.currentPage = 1;
        this.searchQuery = '';
        this.activeFilters = {}; // { columnIndex: value }
        this.filteredRows = [...this.originalRows];
        
        // Wait for translations to load if i18n is used
        if (window.i18n && window.i18n.ready) {
            window.i18n.ready.then(() => this.init());
        } else {
            this.init();
        }
        
        // Listen for language change
        window.addEventListener('languageChanged', () => {
            this.reTranslate();
            this.render();
        });
    }

    reTranslate() {
        // Update perPage label
        const labels = this.wrapper.querySelectorAll('span.text-accents-5');
        labels.forEach(label => {
            if (label.textContent.includes('entries per page') || (window.i18n && label.textContent === window.i18n.t('common.table.entries_per_page'))) {
                label.textContent = window.i18n ? window.i18n.t('common.table.entries_per_page') : 'entries per page';
            }
        });

        // Update search placeholder
        const searchInput = this.wrapper.querySelector('input[type="text"]');
        if (searchInput) {
            searchInput.placeholder = window.i18n ? window.i18n.t('common.table.search_placeholder') : 'Search...';
        }

        // Update All option
        const perPageSelect = this.wrapper.querySelector('select');
        if (perPageSelect) {
            const allOption = Array.from(perPageSelect.options).find(opt => opt.value === "-1");
            if (allOption) {
                allOption.text = window.i18n ? window.i18n.t('common.table.all') : 'All';
            }
        }
    }

    init() {
        // Create Wrapper
        this.wrapper = document.createElement('div');
        this.wrapper.className = 'datatable-wrapper space-y-4';
        this.table.parentNode.insertBefore(this.wrapper, this.table);
        
        // Create Controls Header
        const header = document.createElement('div');
        header.className = 'flex flex-col sm:flex-row justify-between items-center gap-4 mb-4';
        
        // Show Entries Wrapper
        const controlsLeft = document.createElement('div');
        controlsLeft.className = 'flex items-center gap-3 w-full sm:w-auto flex-wrap';
        
        const perPageSelect = document.createElement('select');
        perPageSelect.className = 'form-filter w-20'; 
        
        [5, 10, 25, 50, 100].forEach(num => {
            const option = document.createElement('option');
            option.value = num;
            option.text = num;
            if (num === this.options.itemsPerPage) option.selected = true;
            perPageSelect.appendChild(option);
        });
        
        // All option
        const allOption = document.createElement('option');
        allOption.value = -1;
        allOption.text = window.i18n ? window.i18n.t('common.table.all') : 'All';
        perPageSelect.appendChild(allOption);

        perPageSelect.addEventListener('change', (e) => {
            const val = parseInt(e.target.value);
            this.options.itemsPerPage = val === -1 ? this.originalRows.length : val;
            this.currentPage = 1;
            this.render();
        });

        // Label
        const label = document.createElement('span');
        label.className = 'text-sm text-accents-5 whitespace-nowrap';
        label.textContent = window.i18n ? window.i18n.t('common.table.entries_per_page') : 'entries per page';

        controlsLeft.appendChild(perPageSelect);
        controlsLeft.appendChild(label);
        
        // Initialize Filters if provided
        if (this.options.filters && this.options.filters.length > 0) {
            this.options.filters.forEach(filterConfig => {
                this.initFilter(filterConfig, controlsLeft); // Append to Left Controls
            });
        }

        header.appendChild(controlsLeft);

        // Initialize CustomSelect if available (for perPage)
        if (typeof CustomSelect !== 'undefined') {
             new CustomSelect(perPageSelect);
        }

        // Search Input
        if (this.options.searchable) {
            const searchWrapper = document.createElement('div');
            searchWrapper.className = 'input-group sm:w-64 z-10';
            const placeholder = window.i18n ? window.i18n.t('common.table.search_placeholder') : 'Search...';
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
        
        // Move Table into Wrapper
        // Move Table into Wrapper
        this.tableWrapper = document.createElement('div');
        this.tableWrapper.className = 'rounded-md border border-accents-2 overflow-x-auto bg-white/30 dark:bg-black/30 backdrop-blur-sm'; // overflow-x-auto for responsiveness
        this.tableWrapper.appendChild(this.table);
        this.wrapper.appendChild(this.tableWrapper);
        
        // Render Icons for Header Controls
        if (typeof lucide !== 'undefined') {
            lucide.createIcons({
                root: header
            }); 
        }

        // Pagination Controls
        if (this.options.pagination) {
            this.paginationContainer = document.createElement('div');
            this.paginationContainer.className = 'flex items-center justify-between px-2';
            this.wrapper.appendChild(this.paginationContainer);
        }

        this.render();
    }

    initFilter(config, container) {
        // config = { index: number, label: string }
        const colIndex = config.index;
        
        // Get unique values
        const values = new Set();
        this.originalRows.forEach(row => {
            const cell = row.cells[colIndex];
            if (cell) {
                const text = cell.textContent.trim();
                // Basic cleanup: remove extra whitespace
                if(text && text !== '-' && text !== '') values.add(text);
            }
        });

        // Create Select
        const select = document.createElement('select');
        select.className = 'form-filter datatable-select'; // Use a different class to avoid auto-init by custom-select.js
        
        // Default Option
        const defaultOption = document.createElement('option');
        defaultOption.value = '';
        defaultOption.text = config.label;
        select.appendChild(defaultOption);

        Array.from(values).sort().forEach(val => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.text = val;
            select.appendChild(opt);
        });

        // Event Listener
        select.addEventListener('change', (e) => {
            const val = e.target.value;
            if (val === '') {
                delete this.activeFilters[colIndex];
            } else {
                this.activeFilters[colIndex] = val;
            }
            this.currentPage = 1;
            this.filterRows();
            this.render();
        });

        container.appendChild(select);
        
        if (typeof CustomSelect !== 'undefined') {
             new CustomSelect(select);
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
            // 1. Text Search
            let matchesSearch = true;
            if (this.searchQuery) {
                const text = row.textContent.toLowerCase();
                matchesSearch = text.includes(this.searchQuery);
            }

            // 2. Column Filters
            let matchesFilters = true;
            for (const [colIndex, filterValue] of Object.entries(this.activeFilters)) {
                const cell = row.cells[colIndex];
                if (!cell) {
                     matchesFilters = false;
                     break;
                }
                // Exact match (trimmed)
                if (cell.textContent.trim() !== filterValue) {
                    matchesFilters = false;
                    break;
                }
            }

            return matchesSearch && matchesFilters;
        });
    }

    render() {
        // Calculate pagination
        const totalItems = this.filteredRows.length;
        const totalPages = Math.ceil(totalItems / this.options.itemsPerPage);
        
        // Ensure current page is valid
        if (this.currentPage > totalPages) this.currentPage = totalPages || 1;
        if (this.currentPage < 1) this.currentPage = 1;

        const start = (this.currentPage - 1) * this.options.itemsPerPage;
        const end = start + this.options.itemsPerPage;
        const currentItems = this.filteredRows.slice(start, end);

        // Clear and Re-append rows
        this.tbody.innerHTML = '';
        if (currentItems.length > 0) {
            currentItems.forEach(row => this.tbody.appendChild(row));
        } else {
            // Empty State
            const emptyRow = document.createElement('tr');
            const noMatchText = window.i18n ? window.i18n.t('common.table.no_match') : 'No match found.';
            emptyRow.innerHTML = `
                <td colspan="100%" class="px-6 py-12 text-center text-accents-5">
                    <span class="text-sm">${noMatchText}</span>
                </td>
            `;
            this.tbody.appendChild(emptyRow);
        }

        // Render Pagination
        if (this.options.pagination) {
            this.renderPagination(totalItems, totalPages, start + 1, Math.min(end, totalItems));
        }

        // Re-initialize icons if Lucide is available
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    renderPagination(totalItems, totalPages, start, end) {
        if (totalItems === 0) {
             this.paginationContainer.innerHTML = '';
             return;
        }

        const showingText = window.i18n ? window.i18n.t('common.table.showing', {start, end, total: totalItems}) : `Showing ${start} to ${end} of ${totalItems}`;
        const previousText = window.i18n ? window.i18n.t('common.previous') : 'Previous';
        const nextText = window.i18n ? window.i18n.t('common.next') : 'Next';
        const pageText = window.i18n ? window.i18n.t('common.page_of', {current: this.currentPage, total: totalPages}) : `Page ${this.currentPage} of ${totalPages}`;

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

// Export if using modules, otherwise it's global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SimpleDataTable;
}
