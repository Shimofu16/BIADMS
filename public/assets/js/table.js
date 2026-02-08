(function () {
    let currentPage = 1;
    let config = {};

    function collectFilters() {
        const filters = {};

        if (!Array.isArray(config.filters)) return filters;

        config.filters.forEach(filterId => {
            const el = document.getElementById(filterId);
            if (el) {
                filters[filterId] = el.value;
            }
        });

        return filters;
    }

    function loadData(page = 1) {
        currentPage = page;

        const params = new URLSearchParams({
            action: config.action,
            page,
            ...collectFilters()
        });

        fetch(`${config.endpoint}?${params}`)
            .then(res => res.json())
            .then(res => {
                if (!res.success) return;

                config.renderTable(res.data);
                renderPagination(res.pagination);
            })
            .catch(err => console.error('PaginatedTable error:', err));
    }

    function renderPagination(pagination) {
        const container = document.getElementById(config.paginationContainer);
        if (!container || !pagination) return;

        container.innerHTML = '';

        for (let i = 1; i <= pagination.pages; i++) {
            const btn = document.createElement('button');
            btn.textContent = i;
            btn.className =
                `px-3 py-1 rounded ${
                    i === pagination.page
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 hover:bg-gray-300'
                }`;

            btn.addEventListener('click', () => loadData(i));
            container.appendChild(btn);
        }
    }

    function bindFilters() {
        if (!Array.isArray(config.filters)) return;

        config.filters.forEach(filterId => {
            const el = document.getElementById(filterId);
            if (!el) return;

            // SELECT → instant reload
            if (el.tagName === 'SELECT') {
                el.addEventListener('change', () => {
                    currentPage = 1;
                    loadData();
                    console.log('Filter applied:', filterId, el.value);
                });
                return;
            }

            // INPUT → debounce search
            let debounceTimer;
            el.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    currentPage = 1;
                    loadData();
                    console.log('Filter applied:', filterId, el.value);
                }, 300);
            });
        });
    }

    function init(userConfig) {
        config = userConfig;

        document.addEventListener('DOMContentLoaded', () => {
            bindFilters();
            loadData();
        });
    }

    window.PaginatedTable = { init };
})();
