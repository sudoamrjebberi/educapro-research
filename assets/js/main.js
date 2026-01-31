// ===== الملف الرئيسي للواجهة الأمامية - نسخة مصححة =====

// كائن التطبيق الرئيسي
const ResearchApp = {
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
        }
    },

    // تحميل البيانات الأولية
    loadInitialData: async function() {
        try {
            // تحميل الإحصائيات
            await this.loadStatistics();
            
            // تحميل النتائج
            await this.loadResults('all');
            
            // تحميل بيانات المعلمين
            await this.loadTeachersData();
            
            // إنشاء الرسوم البيانية (بدلاً من loadCharts)
            this.createAllCharts();
            
            console.log('✅ تم تحميل جميع البيانات بنجاح');
        } catch (error) {
            console.error('❌ خطأ في تحميل البيانات:', error);
            this.showError('حدث خطأ في تحميل البيانات');
        }
    },

    // إنشاء جميع الرسوم البيانية
    createAllCharts: function() {
        // إنشاء الرسوم البيانية الأساسية
        this.createTechUsageChart();
        this.createKnowledgeChart();
        this.createEffectivenessChart();
        
        // إذا كانت مكتبة ResearchCharts موجودة، استخدمها
        if (typeof ResearchCharts !== 'undefined' && ResearchCharts.createAllCharts) {
            ResearchCharts.createAllCharts();
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

        // التبويبات - إصلاح الخطأ في substring
        document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', (e) => {
                const href = e.target.getAttribute('href');
                if (href && href.startsWith('#')) {
                    const tabId = href.substring(1);
                    this.handleTabChange(tabId);
                }
            });
        });

        // تحديث البيانات
        const refreshBtn = document.querySelector('[data-action="refresh"]');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshData());
        }
    },

    // إنشاء مخطط استخدام التقنية
    createTechUsageChart: function() {
        const ctx = document.getElementById('techUsageChart');
        if (!ctx) return;
        
        // بيانات افتراضية
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
                datasets: [{
                    label: 'عدد المعلمين',
                    data: [1, 7, 17, 7, 5],
                    backgroundColor: ['#27ae60', '#3498db', '#f39c12', '#e74c3c', '#95a5a6']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    },

    // إنشاء مخطط المعرفة
    createKnowledgeChart: function() {
        const ctx = document.getElementById('knowledgeChart');
        if (!ctx) return;
        
        new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
                datasets: [{
                    data: [6, 11, 12, 8],
                    backgroundColor: ['#3498db', '#f39c12', '#e74c3c', '#95a5a6']
                }]
            }
        });
    },

    // إنشاء مخطط الفعالية
    createEffectivenessChart: function() {
        const ctx = document.getElementById('effectivenessChart');
        if (!ctx) return;
        
        new Chart(ctx.getContext('2d'), {
            type: 'radar',
            data: {
                labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف'],
                datasets: [{
                    label: 'التقييم',
                    data: [4, 12, 16, 5],
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: '#3498db'
                }]
            }
        });
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

    // تحميل بيانات المعلمين
    loadTeachersData: async function(page = 1) {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/teachers.php?page=${page}`);
            const data = await response.json();
            
            if (data.success) {
                this.renderTeachersTable(data.data);
                this.renderPagination(data.pagination); // سيتم تعريفها
            }
        } catch (error) {
            console.error('خطأ في تحميل بيانات المعلمين:', error);
        }
    },

    // عرض جدول المعلمين
    renderTeachersTable: function(teachers) {
        const tbody = document.getElementById('teachersData');
        if (!tbody) return;

        if (!teachers || teachers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        لا توجد بيانات للمعلمين
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        
        teachers.forEach(teacher => {
            html += `
                <tr>
                    <td>${teacher.teacher_code || ''}</td>
                    <td>
                        <span class="badge ${teacher.gender === 'أنثى' ? 'bg-pink' : 'bg-blue'}">
                            ${teacher.gender || ''}
                        </span>
                    </td>
                    <td>${teacher.experience_years || ''}</td>
                    <td>${teacher.education_level || ''}</td>
                    <td>${teacher.specialization || ''}</td>
                    <td>
                        <span class="badge ${this.getTechUsageBadge(teacher.current_tech_usage)}">
                            ${teacher.current_tech_usage || ''}
                        </span>
                    </td>
                    <td>${teacher.animation_knowledge || ''}</td>
                    <td>${teacher.visual_effectiveness || ''}</td>
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

        const paginationElement = container.querySelector('.pagination');
        if (paginationElement) {
            paginationElement.innerHTML = html;
        }
    },

    // دالات مساعدة
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

    // التعامل مع تغيير التبويب
    handleTabChange: function(tabId) {
        console.log('تم التبديل إلى تبويب:', tabId);
        // يمكن إضافة تحميل بيانات خاص بكل تبويب هنا
    },

    // رسائل التنبيه
    showError: function(message) {
        this.showMessage(message, 'danger');
    },

    showMessage: function(message, type = 'info') {
        // تنفيذ بسيط لعرض الرسائل
        alert(`${type}: ${message}`);
    },

    showWelcomeMessage: function() {
        if (!localStorage.getItem('welcomeShown')) {
            setTimeout(() => {
                console.log('مرحباً بك في نظام عرض نتائج البحث التربوي');
                localStorage.setItem('welcomeShown', 'true');
            }, 1000);
        }
    },

    setupScrollToTop: function() {
        // تنفيذ بسيط
        window.addEventListener('scroll', function() {
            // يمكن إضافة زر التمرير للأعلى هنا
        });
    },

    // دالات أخرى مبسطة
    searchData: function(query) {
        console.log('بحث عن:', query);
    },

    searchTeachers: function(query) {
        console.log('بحث في المعلمين:', query);
    },

    filterResults: function(category) {
        console.log('تصفية حسب:', category);
        this.loadResults(category);
    },

    refreshData: function() {
        console.log('تحديث البيانات...');
        this.loadInitialData();
    },

    updateStatistics: function(data) {
        console.log('تم تحديث الإحصائيات:', data);
        // يمكن تحديث واجهة المستخدم هنا
    },

    renderResults: function(data) {
        console.log('عرض النتائج:', data);
        // يمكن عرض النتائج هنا
    }
};

// ===== تهيئة التطبيق عند تحميل الصفحة =====
document.addEventListener('DOMContentLoaded', function() {
    ResearchApp.init();
});

// ===== جعل الدوال متاحة عالمياً للاستدعاء من HTML =====
window.ResearchApp = ResearchApp;
window.loadTeachersData = function(page) {
    ResearchApp.loadTeachersData(page);
};