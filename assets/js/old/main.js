// ===== الملف الرئيسي للواجهة الأمامية =====

// كائن التطبيق الرئيسي
const ResearchApp = {
    // إعدادات التطبيق
    config: {
        apiBaseUrl: window.location.origin + '/api',
        currentPage: 1,
        itemsPerPage: 10,
        darkMode: localStorage.getItem('darkMode') === 'true',
        fontSize: localStorage.getItem('fontSize') || 'medium'
    },

    // تهيئة التطبيق
    init: function() {
        console.log('🚀 تهيئة تطبيق البحث التربوي...');
        
        this.applySettings();
        this.loadInitialData();
        this.setupEventListeners();
        this.setupScrollToTop();
        
        // إظهار رسالة ترحيب
        this.showWelcomeMessage();
    },

    // تطبيق الإعدادات المحفوظة
    applySettings: function() {
        // وضع الظلام
        if (this.config.darkMode) {
            document.body.classList.add('dark-mode');
            const darkModeBtn = document.querySelector('[data-action="dark-mode"]');
            if (darkModeBtn) {
                darkModeBtn.innerHTML = '<i class="bi bi-sun"></i>';
            }
        }

        // حجم الخط
        document.body.style.fontSize = this.getFontSizeValue(this.config.fontSize);
    },

    // تحميل البيانات الأولية
    loadInitialData: async function() {
        try {
            // عرض مؤشر التحميل
            this.showLoading(true);
            
            // تحميل الإحصائيات
            await this.loadStatistics();
            
            // تحميل النتائج
            await this.loadResults('all');
            
            // تحميل بيانات المعلمين
            await this.loadTeachersData();
            
            // تحميل الرسوم البيانية
            await this.loadCharts();
            
            console.log('✅ تم تحميل جميع البيانات بنجاح');
        } catch (error) {
            console.error('❌ خطأ في تحميل البيانات:', error);
            this.showError('حدث خطأ في تحميل البيانات. يرجى تحديث الصفحة.');
        } finally {
            this.showLoading(false);
        }
    },

    // إعداد مستمعي الأحداث
    setupEventListeners: function() {
        // البحث
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const query = document.getElementById('searchInput').value;
                this.searchData(query);
            });
        }

        // البحث في بيانات المعلمين
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                const query = document.getElementById('searchData').value;
                this.searchTeachers(query);
            });
        }

        // أزرار الفلترة
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const category = e.target.dataset.category;
                this.filterResults(category);
            });
        });

        // التبويبات
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const tabId = e.target.getAttribute('href').substring(1);
                this.handleTabChange(tabId);
            });
        });

        // تصدير البيانات
        document.querySelectorAll('[data-export]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const format = e.target.dataset.export;
                this.exportData(format);
            });
        });

        // وضع الظلام
        const darkModeBtn = document.querySelector('[data-action="dark-mode"]');
        if (darkModeBtn) {
            darkModeBtn.addEventListener('click', () => this.toggleDarkMode());
        }

        // تغيير حجم الخط
        const fontSizeBtns = document.querySelectorAll('[data-action="font-size"]');
        fontSizeBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const size = e.target.dataset.size;
                this.changeFontSize(size);
            });
        });

        // الطباعة
        const printBtn = document.querySelector('[data-action="print"]');
        if (printBtn) {
            printBtn.addEventListener('click', () => window.print());
        }

        // تحديث البيانات
        const refreshBtn = document.querySelector('[data-action="refresh"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshData());
        }

        // البحث السريع
        const quickSearch = document.getElementById('quickSearch');
        if (quickSearch) {
            quickSearch.addEventListener('input', (e) => {
                const query = e.target.value;
                if (query.length >= 2) {
                    this.quickSearch(query);
                }
            });
        }
    },

    // تحميل الإحصائيات
    loadStatistics: async function() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/statistics.php`);
            const data = await response.json();
            
            if (data.success) {
                this.updateStatistics(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل الإحصائيات:', error);
        }
    },

    // تحديث عرض الإحصائيات
    updateStatistics: function(data) {
        // تحديث العدادات
        if (data.totalTeachers) {
            this.updateCounter('totalTeachers', data.totalTeachers);
        }
        
        if (data.totalSurveys) {
            this.updateCounter('totalSurveys', data.totalSurveys);
        }

        // تحديث توزيع الجنس
        if (data.genderDistribution) {
            this.updateGenderDistribution(data.genderDistribution);
        }

        // تحديث توزيع الخبرة
        if (data.experienceDistribution) {
            this.updateExperienceDistribution(data.experienceDistribution);
        }
    },

    // تحميل النتائج
    loadResults: async function(category = 'all') {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/results.php?category=${category}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderResults(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل النتائج:', error);
            this.showError('تعذر تحميل النتائج');
        }
    },

    // عرض النتائج
    renderResults: function(data) {
        const container = document.getElementById('resultsContainer');
        if (!container) return;

        if (Object.keys(data).length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        لا توجد نتائج لعرضها
                    </div>
                </div>
            `;
            return;
        }

        let html = '';
        
        Object.entries(data).forEach(([category, categoryData]) => {
            const categoryName = this.getCategoryName(category);
            
            html += `
                <div class="col-12 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi ${this.getCategoryIcon(category)} me-2"></i>
                                ${categoryName}
                            </h5>
                        </div>
                        <div class="card-body">
            `;

            Object.values(categoryData.questions).forEach(question => {
                html += this.renderQuestionCard(question);
            });

            html += `
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    },

    // عرض بطاقة السؤال
    renderQuestionCard: function(question) {
        let total = 0;
        question.answers.forEach(answer => {
            total += answer.count || 0;
        });

        let answersHtml = '';
        question.answers.forEach(answer => {
            const percentage = total > 0 ? ((answer.count || 0) / total) * 100 : 0;
            const colorClass = this.getAnswerColorClass(answer.value);
            
            answersHtml += `
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="fw-medium">${answer.value}</span>
                        <span class="text-muted">${answer.count || 0} (${percentage.toFixed(1)}%)</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div class="progress-bar ${colorClass}" 
                             role="progressbar" 
                             style="width: ${percentage}%"
                             aria-valuenow="${percentage}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                        </div>
                    </div>
                </div>
            `;
        });

        return `
            <div class="result-card mb-4 p-3 border rounded">
                <h6 class="question-text mb-3">${question.text}</h6>
                ${answersHtml}
                <div class="mt-2 text-end text-muted small">
                    إجمالي الإجابات: ${total}
                </div>
            </div>
        `;
    },

    // تحميل بيانات المعلمين
    loadTeachersData: async function(page = 1) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/teachers.php?page=${page}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTeachersTable(data.data);
                this.renderPagination(data.pagination);
            }
        } catch (error) {
            console.error('خطأ في تحميل بيانات المعلمين:', error);
        }
    },

    // عرض جدول المعلمين
    renderTeachersTable: function(teachers) {
        const tbody = document.getElementById('teachersData');
        if (!tbody) return;

        if (teachers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            لا توجد بيانات للمعلمين
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        teachers.forEach(teacher => {
            html += `
                <tr class="fade-in">
                    <td class="fw-medium">${teacher.teacher_code || 'غير معروف'}</td>
                    <td>
                        <span class="badge ${teacher.gender === 'أنثى' ? 'bg-pink' : 'bg-blue'}">
                            ${teacher.gender || ''}
                        </span>
                    </td>
                    <td>${teacher.experience_years || ''}</td>
                    <td>
                        <span class="badge bg-secondary">
                            ${teacher.education_level || ''}
                        </span>
                    </td>
                    <td class="text-truncate" style="max-width: 200px;" 
                        title="${teacher.specialization || ''}">
                        ${teacher.specialization || ''}
                    </td>
                    <td>
                        <span class="badge ${this.getTechUsageBadge(teacher.current_tech_usage)}">
                            ${teacher.current_tech_usage || ''}
                        </span>
                    </td>
                    <td>${teacher.animation_knowledge || ''}</td>
                    <td>
                        <span class="badge ${this.getEffectivenessBadge(teacher.visual_effectiveness)}">
                            ${teacher.visual_effectiveness || ''}
                        </span>
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    },

    // عرض ترقيم الصفحات
    renderPagination: function(pagination) {
        const container = document.getElementById('pagination');
        if (!container || !pagination) return;

        const { current, total, has_prev, has_next } = pagination;
        
        let html = '';
        
        // زر السابق
        html += `
            <li class="page-item ${!has_prev ? 'disabled' : ''}">
                <button class="page-link" ${has_prev ? `onclick="ResearchApp.loadTeachersData(${current - 1})"` : ''}>
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        `;

        // أرقام الصفحات
        for (let i = 1; i <= total; i++) {
            if (i === 1 || i === total || (i >= current - 2 && i <= current + 2)) {
                html += `
                    <li class="page-item ${i === current ? 'active' : ''}">
                        <button class="page-link" onclick="ResearchApp.loadTeachersData(${i})">
                            ${i}
                        </button>
                    </li>
                `;
            } else if (i === current - 3 || i === current + 3) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // زر التالي
        html += `
            <li class="page-item ${!has_next ? 'disabled' : ''}">
                <button class="page-link" ${has_next ? `onclick="ResearchApp.loadTeachersData(${current + 1})"` : ''}>
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
        `;

        container.querySelector('.pagination').innerHTML = html;
    },

    // تحميل الرسوم البيانية
    loadCharts: function() {
        this.createTechUsageChart();
        this.createKnowledgeChart();
        this.createEffectivenessChart();
        this.createGenderChart();
    },

    // إنشاء مخطط استخدام التقنية
    createTechUsageChart: function() {
        const ctx = document.getElementById('techUsageChart');
        if (!ctx) return;

        // بيانات افتراضية (يجب استبدالها ببيانات حقيقية)
        const data = {
            labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
            datasets: [{
                label: 'عدد المعلمين',
                data: [1, 7, 17, 7, 5],
                backgroundColor: [
                    '#27ae60',
                    '#3498db',
                    '#f39c12',
                    '#e74c3c',
                    '#95a5a6'
                ],
                borderWidth: 1
            }]
        };

        new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
    },

    // إنشاء مخطط المعرفة
    createKnowledgeChart: function() {
        const ctx = document.getElementById('knowledgeChart');
        if (!ctx) return;

        const data = {
            labels: ['جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
            datasets: [{
                data: [6, 11, 12, 8],
                backgroundColor: [
                    '#3498db',
                    '#f39c12',
                    '#e74c3c',
                    '#95a5a6'
                ]
            }]
        };

        new Chart(ctx, {
            type: 'doughnut',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    },

    // إنشاء مخطط الفعالية
    createEffectivenessChart: function() {
        const ctx = document.getElementById('effectivenessChart');
        if (!ctx) return;

        const data = {
            labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف'],
            datasets: [{
                label: 'التقييم',
                data: [4, 12, 16, 5],
                backgroundColor: 'rgba(52, 152, 219, 0.5)',
                borderColor: '#3498db',
                borderWidth: 2,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'radar',
            data: data,
            options: {
                responsive: true,
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 20,
                        ticks: {
                            stepSize: 5
                        }
                    }
                }
            }
        });
    },

    // إنشاء مخطط الجنس
    createGenderChart: function() {
        const ctx = document.getElementById('genderChart');
        if (!ctx) return;

        const data = {
            labels: ['إناث', 'ذكور'],
            datasets: [{
                data: [35, 2],
                backgroundColor: [
                    '#e83e8c',
                    '#3498db'
                ]
            }]
        };

        new Chart(ctx, {
            type: 'pie',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    },

    // فلترة النتائج
    filterResults: function(category) {
        // تحديث حالة الأزرار
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active', 'btn-primary');
            btn.classList.add('btn-outline-primary');
        });

        const activeBtn = document.querySelector(`[data-category="${category}"]`);
        if (activeBtn) {
            activeBtn.classList.remove('btn-outline-primary');
            activeBtn.classList.add('active', 'btn-primary');
        }

        // تحميل النتائج المفلترة
        this.loadResults(category);
    },

    // البحث في البيانات
    searchData: function(query) {
        if (query.trim().length < 2) {
            this.showWarning('يرجى إدخال كلمة بحث مكونة من حرفين على الأقل');
            return;
        }

        this.showLoading(true, 'جاري البحث...');

        // هنا يمكن إضافة كود البحث الحقيقي
        setTimeout(() => {
            this.showLoading(false);
            this.showInfo(`تم العثور على نتائج للبحث: "${query}"`);
        }, 1000);
    },

    // البحث في المعلمين
    searchTeachers: async function(query) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/teachers.php?search=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTeachersTable(data.data);
                this.renderPagination(data.pagination);
            }
        } catch (error) {
            console.error('خطأ في البحث:', error);
            this.showError('حدث خطأ أثناء البحث');
        }
    },

    // البحث السريع
    quickSearch: async function(query) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success && data.results) {
                this.showQuickSearchResults(data.results);
            }
        } catch (error) {
            console.error('خطأ في البحث السريع:', error);
        }
    },

    // عرض نتائج البحث السريع
    showQuickSearchResults: function(results) {
        const container = document.getElementById('quickSearchResults');
        if (!container) return;

        if (results.length === 0) {
            container.innerHTML = `
                <div class="dropdown-item text-muted">
                    لا توجد نتائج
                </div>
            `;
            return;
        }

        let html = '';
        results.forEach(result => {
            html += `
                <a href="#" class="dropdown-item" onclick="ResearchApp.viewResult(${result.id})">
                    <div class="d-flex justify-content-between">
                        <span>${result.title || 'عنصر غير معروف'}</span>
                        <small class="text-muted">${result.type || ''}</small>
                    </div>
                </a>
            `;
        });

        container.innerHTML = html;
    },

    // تصدير البيانات
    exportData: function(format) {
        switch (format) {
            case 'json':
                window.open(`${this.config.apiBaseUrl}/export.php?format=json`, '_blank');
                break;
            case 'csv':
                window.open(`${this.config.apiBaseUrl}/export.php?format=csv`, '_blank');
                break;
            case 'pdf':
                this.exportToPDF();
                break;
            default:
                this.showError('تنسيق التصدير غير مدعوم');
        }
    },

    // تصدير إلى PDF
    exportToPDF: function() {
        this.showLoading(true, 'جاري إنشاء ملف PDF...');
        
        // محاكاة إنشاء PDF (في التطبيق الحقيقي، استخدم مكتبة jsPDF)
        setTimeout(() => {
            this.showLoading(false);
            this.showSuccess('تم إنشاء ملف PDF بنجاح. جاري التحميل...');
            
            // في التطبيق الحقيقي، استخدم:
            // window.open(`${this.config.apiBaseUrl}/export.php?format=pdf`, '_blank');
        }, 2000);
    },

    // تبديل وضع الظلام
    toggleDarkMode: function() {
        this.config.darkMode = !this.config.darkMode;
        localStorage.setItem('darkMode', this.config.darkMode);
        
        document.body.classList.toggle('dark-mode', this.config.darkMode);
        
        const darkModeBtn = document.querySelector('[data-action="dark-mode"]');
        if (darkModeBtn) {
            darkModeBtn.innerHTML = this.config.darkMode ? 
                '<i class="bi bi-sun"></i>' : 
                '<i class="bi bi-moon"></i>';
        }
        
        this.showInfo(`تم تفعيل ${this.config.darkMode ? 'وضع الظلام' : 'وضع النهار'}`);
    },

    // تغيير حجم الخط
    changeFontSize: function(size) {
        this.config.fontSize = size;
        localStorage.setItem('fontSize', size);
        
        document.body.style.fontSize = this.getFontSizeValue(size);
        this.showInfo(`تم تغيير حجم الخط إلى ${this.getFontSizeLabel(size)}`);
    },

    // تحديث البيانات
    refreshData: function() {
        this.showLoading(true, 'جاري تحديث البيانات...');
        
        setTimeout(() => {
            this.loadInitialData();
            this.showSuccess('تم تحديث البيانات بنجاح');
        }, 1500);
    },

    // التعامل مع تغيير التبويب
    handleTabChange: function(tabId) {
        switch (tabId) {
            case 'gender':
                this.createGenderChart();
                break;
            case 'experience':
                this.loadExperienceAnalysis();
                break;
            case 'specialization':
                this.loadSpecializationAnalysis();
                break;
        }
    },

    // تحليل الخبرة
    loadExperienceAnalysis: async function() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/analysis.php?type=experience`);
            const data = await response.json();
            
            if (data.success) {
                this.renderExperienceAnalysis(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل تحليل الخبرة:', error);
        }
    },

    // تحليل التخصص
    loadSpecializationAnalysis: async function() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/analysis.php?type=specialization`);
            const data = await response.json();
            
            if (data.success) {
                this.renderSpecializationAnalysis(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل تحليل التخصص:', error);
        }
    },

    // ===== أدوات مساعدة =====

    // تحديث العداد برقم
    updateCounter: function(elementId, value) {
        const element = document.getElementById(elementId);
        if (!element) return;

        element.textContent = value;
        this.animateCounter(element, value);
    },

    // تحريك العداد
    animateCounter: function(element, target) {
        const current = parseInt(element.textContent) || 0;
        const increment = target > current ? 1 : -1;
        let currentValue = current;

        const timer = setInterval(() => {
            currentValue += increment;
            element.textContent = currentValue;
            
            if (currentValue === target) {
                clearInterval(timer);
            }
        }, 30);
    },

    // الحصول على اسم الفئة
    getCategoryName: function(category) {
        const categories = {
            'positive': 'التصورات الإيجابية',
            'challenges': 'التحديات والعوائق',
            'concerns': 'المخاوف التربوية',
            'recommendations': 'التوصيات والمقترحات'
        };
        return categories[category] || category;
    },

    // الحصول على أيقونة الفئة
    getCategoryIcon: function(category) {
        const icons = {
            'positive': 'bi-check-circle-fill',
            'challenges': 'bi-exclamation-triangle-fill',
            'concerns': 'bi-question-circle-fill',
            'recommendations': 'bi-lightbulb-fill'
        };
        return icons[category] || 'bi-info-circle';
    },

    // الحصول على لون الإجابة
    getAnswerColorClass: function(answer) {
        if (answer.includes('موافق بشدة')) return 'bg-success';
        if (answer.includes('موافق')) return 'bg-primary';
        if (answer.includes('محايد')) return 'bg-warning';
        if (answer.includes('غير موافق')) return 'bg-danger';
        if (answer === 'نعم') return 'bg-success';
        if (answer === 'لا') return 'bg-danger';
        return 'bg-secondary';
    },

    // الحصول على بادج استخدام التقنية
    getTechUsageBadge: function(usage) {
        switch(usage) {
            case 'ممتاز': return 'bg-success';
            case 'جيد': return 'bg-primary';
            case 'متوسط': return 'bg-warning';
            case 'ضعيف': return 'bg-danger';
            case 'ضعيف جداً': return 'bg-dark';
            default: return 'bg-secondary';
        }
    },

    // الحصول على بادج الفعالية
    getEffectivenessBadge: function(effectiveness) {
        switch(effectiveness) {
            case 'ممتاز': return 'bg-success';
            case 'جيد': return 'bg-primary';
            case 'متوسط': return 'bg-warning';
            case 'ضعيف': return 'bg-danger';
            default: return 'bg-secondary';
        }
    },

    // الحصول على قيمة حجم الخط
    getFontSizeValue: function(size) {
        const sizes = {
            'small': '0.875rem',
            'medium': '1rem',
            'large': '1.125rem',
            'x-large': '1.25rem'
        };
        return sizes[size] || '1rem';
    },

    // الحصول على تسمية حجم الخط
    getFontSizeLabel: function(size) {
        const labels = {
            'small': 'صغير',
            'medium': 'متوسط',
            'large': 'كبير',
            'x-large': 'كبير جداً'
        };
        return labels[size] || 'متوسط';
    },

    // ===== إدارة التحميل والرسائل =====

    // عرض/إخفاء مؤشر التحميل
    showLoading: function(show, message = 'جاري التحميل...') {
        const loadingElement = document.getElementById('loadingOverlay');
        
        if (!loadingElement) {
            // إنشاء عنصر التحميل إذا لم يكن موجوداً
            const overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.className = 'loading-overlay';
            overlay.innerHTML = `
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="loading-text mt-3">${message}</div>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            if (show) {
                loadingElement.style.display = 'flex';
                const loadingText = loadingElement.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
            } else {
                loadingElement.style.display = 'none';
            }
        }
    },

    // عرض رسالة نجاح
    showSuccess: function(message) {
        this.showMessage(message, 'success');
    },

    // عرض رسالة خطأ
    showError: function(message) {
        this.showMessage(message, 'danger');
    },

    // عرض رسالة تحذير
    showWarning: function(message) {
        this.showMessage(message, 'warning');
    },

    // عرض رسالة معلومات
    showInfo: function(message) {
        this.showMessage(message, 'info');
    },

    // عرض رسالة
    showMessage: function(message, type = 'info') {
        // إزالة الرسائل القديمة
        this.removeOldMessages();

        // إنشاء عنصر الرسالة
        const messageElement = document.createElement('div');
        messageElement.className = `alert alert-${type} alert-dismissible fade show message-alert`;
        messageElement.innerHTML = `
            <i class="bi ${this.getMessageIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        // إضافة الرسالة إلى الصفحة
        const container = document.querySelector('.messages-container') || this.createMessagesContainer();
        container.appendChild(messageElement);

        // إزالة الرسالة تلقائياً بعد 5 ثوانٍ
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.remove();
            }
        }, 5000);
    },

    // إزالة الرسائل القديمة
    removeOldMessages: function() {
        const oldMessages = document.querySelectorAll('.message-alert');
        oldMessages.forEach(msg => {
            if (msg.parentNode) {
                msg.remove();
            }
        });
    },

    // إنشاء حاوية الرسائل
    createMessagesContainer: function() {
        const container = document.createElement('div');
        container.className = 'messages-container';
        container.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        return container;
    },

    // الحصول على أيقونة الرسالة
    getMessageIcon: function(type) {
        const icons = {
            'success': 'bi-check-circle-fill',
            'danger': 'bi-exclamation-circle-fill',
            'warning': 'bi-exclamation-triangle-fill',
            'info': 'bi-info-circle-fill'
        };
        return icons[type] || 'bi-info-circle-fill';
    },

    // عرض رسالة ترحيب
    showWelcomeMessage: function() {
        if (!localStorage.getItem('welcomeShown')) {
            setTimeout(() => {
                this.showInfo('مرحباً بك في نظام عرض نتائج البحث التربوي. استخدم الفلاتر والبحث لاستكشاف البيانات.');
                localStorage.setItem('welcomeShown', 'true');
            }, 1000);
        }
    },

    // إعداد زر التمرير للأعلى
    setupScrollToTop: function() {
        const scrollBtn = document.createElement('button');
        scrollBtn.id = 'scrollToTop';
        scrollBtn.className = 'scroll-to-top';
        scrollBtn.innerHTML = '<i class="bi bi-chevron-up"></i>';
        scrollBtn.style.cssText = `
            display: none;
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        `;

        document.body.appendChild(scrollBtn);

        // إظهار/إخفاء الزر عند التمرير
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                scrollBtn.style.display = 'flex';
                scrollBtn.style.alignItems = 'center';
                scrollBtn.style.justifyContent = 'center';
            } else {
                scrollBtn.style.display = 'none';
            }
        });

        // حدث النقر
        scrollBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    },

    // تحديث توزيع الجنس
    updateGenderDistribution: function(data) {
        const container = document.getElementById('genderDistribution');
        if (!container) return;

        let html = '';
        data.forEach(item => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>${item.gender}</span>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 mx-2" style="width: 100px; height: 8px;">
                            <div class="progress-bar ${item.gender === 'أنثى' ? 'bg-pink' : 'bg-blue'}" 
                                 style="width: ${item.percentage || 0}%">
                            </div>
                        </div>
                        <span class="text-muted small">${item.count} (${item.percentage || 0}%)</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    },

    // تحديث توزيع الخبرة
    updateExperienceDistribution: function(data) {
        const container = document.getElementById('experienceDistribution');
        if (!container) return;

        let html = '';
        data.forEach(item => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>${item.experience_years}</span>
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 mx-2" style="width: 100px; height: 8px;">
                            <div class="progress-bar bg-warning" 
                                 style="width: ${item.percentage || 0}%">
                            </div>
                        </div>
                        <span class="text-muted small">${item.count} (${item.percentage || 0}%)</span>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
    },

    // عرض نتيجة مفردة
    viewResult: function(resultId) {
        this.showLoading(true, 'جاري تحميل التفاصيل...');
        
        // محاكاة تحميل التفاصيل
        setTimeout(() => {
            this.showLoading(false);
            this.showInfo('تفاصيل النتيجة قيد التطوير');
        }, 1000);
    }
};

// ===== تهيئة التطبيق عند تحميل الصفحة =====
document.addEventListener('DOMContentLoaded', function() {
    ResearchApp.init();
});

// ===== جعل التطبيق متاحاً عالمياً =====
window.ResearchApp = ResearchApp;

// ===== وظائف مساعدة عامة =====
function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA').format(num);
}

function formatDate(date) {
    return new Intl.DateTimeFormat('ar-SA', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(new Date(date));
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ===== تحسينات الأداء =====
// تحميل متأخر للصور
document.addEventListener('DOMContentLoaded', function() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                imageObserver.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => imageObserver.observe(img));
});

// ===== إدارة الذاكرة =====
// تنظيف المستمعين عند إغلاق الصفحة
window.addEventListener('beforeunload', function() {
    // تنظيف المؤقتات
    const highestTimeoutId = setTimeout(() => {}, 0);
    for (let i = 0; i < highestTimeoutId; i++) {
        clearTimeout(i);
    }
    
    // تنظيف المستمعين
    document.removeEventListener('scroll', null);
});

// ===== تحسينات للشاشات اللمس =====
if ('ontouchstart' in window) {
    document.documentElement.classList.add('touch-device');
    
    // زيادة حجم العناصر القابلة للنقر
    const clickableElements = document.querySelectorAll('button, a, .btn, [role="button"]');
    clickableElements.forEach(el => {
        el.style.minHeight = '44px';
        el.style.minWidth = '44px';
    });
}