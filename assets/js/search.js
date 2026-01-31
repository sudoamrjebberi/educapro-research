// ===== إدارة البحث المتقدم =====

const AdvancedSearch = {
    config: {
        apiBaseUrl: window.location.origin + '/api',
        debounceDelay: 300
    },

    init: function() {
        this.setupSearchListeners();
        this.setupFilters();
        this.setupExport();
    },

    setupSearchListeners: function() {
        // البحث العادي
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', 
                this.debounce(this.handleSearch.bind(this), this.config.debounceDelay)
            );
        }

        // البحث المتقدم
        const advancedSearchBtn = document.getElementById('advancedSearchBtn');
        if (advancedSearchBtn) {
            advancedSearchBtn.addEventListener('click', this.toggleAdvancedSearch.bind(this));
        }

        // تطبيق الفلاتر
        const applyFiltersBtn = document.getElementById('applyFilters');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', this.applyFilters.bind(this));
        }
    },

    setupFilters: function() {
        // مبدئي الفلاتر من localStorage
        this.loadFilters();
        
        // حدث تغيير الفلاتر
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', this.updateFilters.bind(this));
        });

        // حدث تغيير النطاق
        document.querySelectorAll('.filter-range').forEach(range => {
            range.addEventListener('change', this.updateRangeFilters.bind(this));
        });
    },

    setupExport: function() {
        document.querySelectorAll('.export-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const format = e.target.dataset.format;
                this.exportResults(format);
            });
        });
    },

    handleSearch: async function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            this.clearResults();
            return;
        }

        try {
            const response = await fetch(`${this.config.apiBaseUrl}/search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displaySearchResults(data.results);
            }
        } catch (error) {
            console.error('خطأ في البحث:', error);
            this.showError('حدث خطأ أثناء البحث');
        }
    },

    displaySearchResults: function(results) {
        const container = document.getElementById('searchResults');
        if (!container) return;

        if (results.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    لا توجد نتائج للبحث
                </div>
            `;
            return;
        }

        let html = '<div class="list-group">';
        
        results.forEach(result => {
            html += `
                <a href="${result.link}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${result.title}</h6>
                        <small class="text-muted">${result.type}</small>
                    </div>
                    <p class="mb-1">${result.description}</p>
                </a>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
    },

    clearResults: function() {
        const container = document.getElementById('searchResults');
        if (container) {
            container.innerHTML = '';
        }
    },

    toggleAdvancedSearch: function() {
        const panel = document.getElementById('advancedSearchPanel');
        if (!panel) return;

        panel.classList.toggle('d-none');
        
        const btn = document.getElementById('advancedSearchBtn');
        if (btn) {
            const icon = btn.querySelector('i');
            if (panel.classList.contains('d-none')) {
                icon.className = 'bi bi-chevron-down';
                btn.innerHTML = '<i class="bi bi-chevron-down"></i> بحث متقدم';
            } else {
                icon.className = 'bi bi-chevron-up';
                btn.innerHTML = '<i class="bi bi-chevron-up"></i> إخفاء البحث المتقدم';
            }
        }
    },

    applyFilters: async function() {
        const filters = this.getActiveFilters();
        
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/search.php?advanced=1`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(filters)
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.displayFilteredResults(data.results, data.metadata);
                this.saveFilters(filters);
            }
        } catch (error) {
            console.error('خطأ في تطبيق الفلاتر:', error);
            this.showError('حدث خطأ أثناء التصفية');
        }
    },

    getActiveFilters: function() {
        const filters = {};
        
        // فلاتر الفئات
        const categories = [];
        document.querySelectorAll('.filter-checkbox:checked').forEach(checkbox => {
            categories.push(checkbox.value);
        });
        if (categories.length > 0) {
            filters.categories = categories;
        }

        // فلاتر النطاق
        const experienceMin = document.getElementById('experienceMin')?.value;
        const experienceMax = document.getElementById('experienceMax')?.value;
        if (experienceMin || experienceMax) {
            filters.experience = {
                min: experienceMin || 0,
                max: experienceMax || 50
            };
        }

        // فلاتر الجنس
        const genderFilter = document.querySelector('input[name="gender"]:checked');
        if (genderFilter) {
            filters.gender = genderFilter.value;
        }

        // فلاتر التخصص
        const specialization = document.getElementById('specializationFilter')?.value;
        if (specialization) {
            filters.specialization = specialization;
        }

        return filters;
    },

    updateFilters: function() {
        // تحديث عدد الفلاتر النشطة
        const activeCount = document.querySelectorAll('.filter-checkbox:checked').length;
        const filterCount = document.getElementById('activeFilterCount');
        if (filterCount) {
            filterCount.textContent = activeCount;
            filterCount.style.display = activeCount > 0 ? 'inline-block' : 'none';
        }
    },

    updateRangeFilters: function() {
        const min = document.getElementById('experienceMin')?.value || 0;
        const max = document.getElementById('experienceMax')?.value || 50;
        
        const rangeValue = document.getElementById('experienceRangeValue');
        if (rangeValue) {
            rangeValue.textContent = `${min} - ${max} سنة`;
        }
    },

    displayFilteredResults: function(results, metadata) {
        const container = document.getElementById('filteredResults');
        if (!container) return;

        let html = `
            <div class="alert alert-info">
                <i class="bi bi-filter"></i>
                ${metadata.total} نتيجة ${metadata.query ? `لـ "${metadata.query}"` : ''}
            </div>
        `;

        if (results.length === 0) {
            html += `
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    لا توجد نتائج تطابق الفلاتر المحددة
                </div>
            `;
        } else {
            html += '<div class="table-responsive"><table class="table table-hover"><thead><tr>';
            
            // رؤوس الجدول
            if (results[0]) {
                Object.keys(results[0]).forEach(key => {
                    if (key !== 'id' && key !== 'link') {
                        html += `<th>${this.getColumnName(key)}</th>`;
                    }
                });
                html += '<th>الإجراءات</th></tr></thead><tbody>';
                
                // بيانات الجدول
                results.forEach(result => {
                    html += '<tr>';
                    Object.entries(result).forEach(([key, value]) => {
                        if (key !== 'id' && key !== 'link') {
                            html += `<td>${this.formatCellValue(key, value)}</td>`;
                        }
                    });
                    html += `
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="AdvancedSearch.viewDetails(${result.id})">
                                <i class="bi bi-eye"></i>
                            </button>
                        </td>
                    </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
            }
        }

        container.innerHTML = html;
    },

    getColumnName: function(key) {
        const columns = {
            'teacher_code': 'كود المعلم',
            'gender': 'الجنس',
            'experience_years': 'سنوات الخبرة',
            'specialization': 'التخصص',
            'question_category': 'فئة السؤال',
            'question_text': 'نص السؤال',
            'answer_value': 'الإجابة',
            'created_at': 'التاريخ'
        };
        
        return columns[key] || key;
    },

    formatCellValue: function(key, value) {
        if (key === 'gender') {
            return `<span class="badge ${value === 'أنثى' ? 'bg-pink' : 'bg-blue'}">${value}</span>`;
        }
        
        if (key === 'answer_value') {
            if (value.includes('موافق بشدة')) return `<span class="badge bg-success">${value}</span>`;
            if (value.includes('موافق')) return `<span class="badge bg-primary">${value}</span>`;
            if (value.includes('محايد')) return `<span class="badge bg-warning">${value}</span>`;
            if (value.includes('غير موافق')) return `<span class="badge bg-danger">${value}</span>`;
        }
        
        return value || '-';
    },

    viewDetails: function(id) {
        // يمكن تنفيذ عرض التفاصيل هنا
        alert(`عرض تفاصيل العنصر ${id} - هذه الميزة قيد التطوير`);
    },

    exportResults: function(format) {
        const filters = this.getActiveFilters();
        const queryParams = new URLSearchParams();
        
        Object.entries(filters).forEach(([key, value]) => {
            if (Array.isArray(value)) {
                value.forEach(v => queryParams.append(key + '[]', v));
            } else if (typeof value === 'object') {
                Object.entries(value).forEach(([k, v]) => {
                    queryParams.append(`${key}[${k}]`, v);
                });
            } else {
                queryParams.append(key, value);
            }
        });
        
        queryParams.append('format', format);
        
        window.open(`${this.config.apiBaseUrl}/export.php?${queryParams.toString()}`, '_blank');
    },

    saveFilters: function(filters) {
        localStorage.setItem('search_filters', JSON.stringify(filters));
    },

    loadFilters: function() {
        const savedFilters = localStorage.getItem('search_filters');
        if (!savedFilters) return;
        
        try {
            const filters = JSON.parse(savedFilters);
            this.applySavedFilters(filters);
        } catch (error) {
            console.error('خطأ في تحميل الفلاتر المحفوظة:', error);
        }
    },

    applySavedFilters: function(filters) {
        // تطبيق فلاتر الفئات
        if (filters.categories) {
            filters.categories.forEach(category => {
                const checkbox = document.querySelector(`.filter-checkbox[value="${category}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        // تطبيق فلاتر النطاق
        if (filters.experience) {
            const minInput = document.getElementById('experienceMin');
            const maxInput = document.getElementById('experienceMax');
            
            if (minInput && filters.experience.min) {
                minInput.value = filters.experience.min;
            }
            
            if (maxInput && filters.experience.max) {
                maxInput.value = filters.experience.max;
            }
            
            this.updateRangeFilters();
        }

        // تطبيق فلاتر الجنس
        if (filters.gender) {
            const genderRadio = document.querySelector(`input[name="gender"][value="${filters.gender}"]`);
            if (genderRadio) {
                genderRadio.checked = true;
            }
        }

        // تطبيق فلاتر التخصص
        if (filters.specialization) {
            const specializationSelect = document.getElementById('specializationFilter');
            if (specializationSelect) {
                specializationSelect.value = filters.specialization;
            }
        }

        this.updateFilters();
    },

    clearSavedFilters: function() {
        localStorage.removeItem('search_filters');
        
        // إعادة تعيين جميع الفلاتر
        document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
            checkbox.checked = false;
        });
        
        document.querySelectorAll('.filter-range').forEach(range => {
            range.value = range.min || 0;
        });
        
        document.querySelectorAll('input[name="gender"]').forEach(radio => {
            radio.checked = false;
        });
        
        const specializationSelect = document.getElementById('specializationFilter');
        if (specializationSelect) {
            specializationSelect.value = '';
        }
        
        this.updateFilters();
        this.updateRangeFilters();
    },

    debounce: function(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    },

    showError: function(message) {
        const container = document.getElementById('searchResults') || document.getElementById('filteredResults');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    ${message}
                </div>
            `;
        }
    },

    showSuccess: function(message) {
        const container = document.getElementById('searchResults') || document.getElementById('filteredResults');
        if (container) {
            container.innerHTML = `
                <div class="alert alert-success">
                    <i class="bi bi-check-circle"></i>
                    ${message}
                </div>
            `;
        }
    }
};

// تهيئة البحث المتقدم
document.addEventListener('DOMContentLoaded', function() {
    AdvancedSearch.init();
});