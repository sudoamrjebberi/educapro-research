// ===== إدارة لوحة التحكم =====

const AdminDashboard = {
    config: {
        apiBaseUrl: '../api',
        currentPage: 1,
        itemsPerPage: 10
    },

    init: function() {
        console.log('🎯 تهيئة لوحة التحكم...');
        
        this.checkSession();
        this.loadDashboardData();
        this.setupEventListeners();
        this.setupRealTimeUpdates();
    },

    checkSession: function() {
        // التحقق من الجلسة كل 5 دقائق
        setInterval(() => {
            fetch('../admin/check_session.php')
                .then(response => response.json())
                .then(data => {
                    if (!data.valid) {
                        this.showSessionWarning();
                    }
                })
                .catch(error => console.error('خطأ في التحقق من الجلسة:', error));
        }, 300000); // 5 دقائق
    },

    loadDashboardData: function() {
        this.loadStatistics();
        this.loadRecentActivity();
        this.loadCharts();
    },

    setupEventListeners: function() {
        // تحديث البيانات
        const refreshBtn = document.getElementById('refreshData');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.refreshData());
        }

        // تصدير التقارير
        const exportBtns = document.querySelectorAll('.export-report');
        exportBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const format = e.target.dataset.format;
                this.exportReport(format);
            });
        });

        // البحث في لوحة التحكم
        const adminSearch = document.getElementById('adminSearch');
        if (adminSearch) {
            adminSearch.addEventListener('input', 
                this.debounce(this.searchAdminData.bind(this), 300)
            );
        }

        // إدارة الجداول
        this.setupTableManagement();
        
        // إدارة النماذج
        this.setupFormManagement();
    },

    setupRealTimeUpdates: function() {
        // تحديث تلقائي كل دقيقة
        setInterval(() => {
            this.loadStatistics();
        }, 60000);
    },

    loadStatistics: async function() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/statistics.php`);
            const data = await response.json();
            
            if (data.success) {
                this.updateStatsDisplay(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل الإحصائيات:', error);
        }
    },

    updateStatsDisplay: function(stats) {
        // تحديث العدادات
        this.updateCounter('totalTeachers', stats.total_teachers);
        this.updateCounter('totalSurveys', stats.total_surveys);
        this.updateCounter('activeParticipants', stats.active_participants || 0);
        this.updateCounter('participationRate', stats.participation_rate || 0);
        
        // تحديث مخططات الإحصائيات
        this.updateStatsCharts(stats);
    },

    updateCounter: function(elementId, value) {
        const element = document.getElementById(elementId);
        if (!element) return;
        
        const current = parseInt(element.textContent) || 0;
        if (current !== value) {
            this.animateCounter(element, current, value);
        }
    },

    animateCounter: function(element, from, to) {
        const duration = 1000; // 1 ثانية
        const steps = 60;
        const increment = (to - from) / steps;
        let current = from;
        
        const timer = setInterval(() => {
            current += increment;
            element.textContent = Math.round(current);
            
            if (Math.abs(current - to) < Math.abs(increment)) {
                element.textContent = to;
                clearInterval(timer);
            }
        }, duration / steps);
    },

    loadRecentActivity: async function() {
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/activity.php`);
            const data = await response.json();
            
            if (data.success) {
                this.updateActivityTable(data.data);
            }
        } catch (error) {
            console.error('خطأ في تحميل النشاط الأخير:', error);
        }
    },

    updateActivityTable: function(activities) {
        const tbody = document.getElementById('recentActivityBody');
        if (!tbody) return;
        
        let html = '';
        
        activities.forEach(activity => {
            const timeAgo = this.getTimeAgo(activity.created_at);
            
            html += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-2">
                                ${activity.admin_name ? activity.admin_name.charAt(0) : 'A'}
                            </div>
                            <div>
                                <div class="fw-medium">${activity.admin_name || 'نظام'}</div>
                                <small class="text-muted">${activity.username || ''}</small>
                            </div>
                        </div>
                    </td>
                    <td>${activity.action}</td>
                    <td>${activity.details || '-'}</td>
                    <td>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-clock me-1"></i>
                            ${timeAgo}
                        </span>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    },

    loadCharts: function() {
        this.createDashboardCharts();
    },

    createDashboardCharts: function() {
        // مخطط المشاركة
        this.createParticipationChart();
        
        // مخطط النشاط الزمني
        this.createActivityTimelineChart();
        
        // مخطط التوزيع الجغرافي (إن وجد)
        this.createDistributionChart();
    },

    createParticipationChart: function() {
        const ctx = document.getElementById('participationChart');
        if (!ctx) return;
        
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'ناقص', 'لم يبدأ'],
                datasets: [{
                    data: [85, 10, 5],
                    backgroundColor: [
                        '#2ecc71',
                        '#f39c12',
                        '#e74c3c'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'معدل المشاركة'
                    }
                },
                cutout: '70%'
            }
        });
    },

    createActivityTimelineChart: function() {
        const ctx = document.getElementById('activityTimelineChart');
        if (!ctx) return;
        
        // بيانات افتراضية
        const data = {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'عدد الاستبيانات',
                data: [12, 19, 8, 15, 22, 18],
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: '#3498db',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        };
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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

    createDistributionChart: function() {
        const ctx = document.getElementById('distributionChart');
        if (!ctx) return;
        
        const data = {
            labels: ['اللغة العربية', 'الرياضيات', 'العلوم', 'اللغة الإنجليزية', 'الاجتماعيات'],
            datasets: [{
                label: 'عدد المعلمين',
                data: [22, 18, 15, 12, 8],
                backgroundColor: [
                    '#3498db',
                    '#2ecc71',
                    '#e74c3c',
                    '#f39c12',
                    '#9b59b6'
                ]
            }]
        };
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    },

    updateStatsCharts: function(stats) {
        // تحديث المخططات بناءً على الإحصائيات الجديدة
        // يمكن تنفيذ هذا حسب الحاجة
    },

    refreshData: function() {
        this.showLoading('جاري تحديث البيانات...');
        
        Promise.all([
            this.loadStatistics(),
            this.loadRecentActivity()
        ]).then(() => {
            this.hideLoading();
            this.showNotification('تم تحديث البيانات بنجاح', 'success');
        }).catch(error => {
            this.hideLoading();
            this.showNotification('حدث خطأ أثناء التحديث', 'error');
            console.error('خطأ في تحديث البيانات:', error);
        });
    },

    searchAdminData: async function(e) {
        const query = e.target.value.trim();
        
        if (query.length < 2) {
            this.clearSearchResults();
            return;
        }
        
        try {
            const response = await fetch(`${this.config.apiBaseUrl}/admin_search.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            
            if (data.success) {
                this.displayAdminSearchResults(data.results);
            }
        } catch (error) {
            console.error('خطأ في البحث:', error);
            this.showNotification('حدث خطأ أثناء البحث', 'error');
        }
    },

    displayAdminSearchResults: function(results) {
        const container = document.getElementById('adminSearchResults');
        if (!container) return;
        
        if (results.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i>
                    لا توجد نتائج
                </div>
            `;
            return;
        }
        
        let html = '<div class="list-group">';
        
        results.forEach(result => {
            html += `
                <a href="${result.link}" class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${result.title}</h6>
                            <small class="text-muted">${result.type} • ${result.description}</small>
                        </div>
                        <i class="bi bi-chevron-left"></i>
                    </div>
                </a>
            `;
        });
        
        html += '</div>';
        container.innerHTML = html;
        container.classList.remove('d-none');
    },

    clearSearchResults: function() {
        const container = document.getElementById('adminSearchResults');
        if (container) {
            container.innerHTML = '';
            container.classList.add('d-none');
        }
    },

    exportReport: function(format) {
        this.showLoading(`جاري إنشاء تقرير ${format.toUpperCase()}...`);
        
        const url = `${this.config.apiBaseUrl}/export.php?format=${format}&report=dashboard`;
        
        fetch(url)
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('فشل إنشاء التقرير');
            })
            .then(blob => {
                const downloadUrl = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = downloadUrl;
                a.download = `dashboard_report_${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(downloadUrl);
                
                this.hideLoading();
                this.showNotification('تم إنشاء التقرير بنجاح', 'success');
            })
            .catch(error => {
                this.hideLoading();
                this.showNotification('حدث خطأ أثناء إنشاء التقرير', 'error');
                console.error('خطأ في التصدير:', error);
            });
    },

    setupTableManagement: function() {
        // فرز الجداول
        document.querySelectorAll('th[data-sort]').forEach(th => {
            th.addEventListener('click', (e) => {
                const column = e.target.dataset.sort;
                const direction = e.target.dataset.direction || 'asc';
                this.sortTable(column, direction);
                
                // تبديل الاتجاه
                e.target.dataset.direction = direction === 'asc' ? 'desc' : 'asc';
            });
        });
        
        // حذف العناصر
        document.querySelectorAll('.delete-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                const type = e.target.dataset.type;
                this.confirmDelete(id, type);
            });
        });
        
        // تعديل العناصر
        document.querySelectorAll('.edit-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = e.target.dataset.id;
                const type = e.target.dataset.type;
                this.editItem(id, type);
            });
        });
    },

    setupFormManagement: function() {
        // التحقق من النماذج
        document.querySelectorAll('form.needs-validation').forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    e.stopPropagation();
                }
                form.classList.add('was-validated');
            });
        });
        
        // معاينة الصور
        const imageInputs = document.querySelectorAll('input[type="file"][accept^="image/"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.previewImage(e.target);
            });
        });
    },

    sortTable: function(column, direction) {
        const table = document.querySelector('.admin-table');
        if (!table) return;
        
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        rows.sort((a, b) => {
            const aValue = a.querySelector(`td[data-column="${column}"]`)?.textContent || '';
            const bValue = b.querySelector(`td[data-column="${column}"]`)?.textContent || '';
            
            if (direction === 'asc') {
                return aValue.localeCompare(bValue, 'ar');
            } else {
                return bValue.localeCompare(aValue, 'ar');
            }
        });
        
        // إعادة ترتيب الصفوف
        rows.forEach(row => tbody.appendChild(row));
    },

    confirmDelete: function(id, type) {
        if (!confirm(`هل أنت متأكد من حذف هذا ${this.getItemTypeName(type)}؟`)) {
            return;
        }
        
        fetch(`../admin/delete.php?type=${type}&id=${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('تم الحذف بنجاح', 'success');
                this.refreshData();
            } else {
                this.showNotification(data.error || 'فشل الحذف', 'error');
            }
        })
        .catch(error => {
            console.error('خطأ في الحذف:', error);
            this.showNotification('حدث خطأ أثناء الحذف', 'error');
        });
    },

    getItemTypeName: function(type) {
        const types = {
            'teacher': 'المعلم',
            'survey': 'الاستبيان',
            'answer': 'الإجابة',
            'admin': 'المسؤول'
        };
        
        return types[type] || 'العنصر';
    },

    editItem: function(id, type) {
        // فتح نموذج التعديل
        this.openEditModal(id, type);
    },

    openEditModal: function(id, type) {
        // هنا يمكن فتح مودال التعديل
        console.log(`فتح تعديل ${type} رقم ${id}`);
        // تنفيذ حسب الحاجة
    },

    previewImage: function(input) {
        const preview = document.getElementById('imagePreview');
        if (!preview) return;
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    },

    getTimeAgo: function(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'الآن';
        if (minutes < 60) return `قبل ${minutes} دقيقة`;
        if (hours < 24) return `قبل ${hours} ساعة`;
        if (days < 7) return `قبل ${days} يوم`;
        
        return date.toLocaleDateString('ar-SA');
    },

    showLoading: function(message = 'جاري التحميل...') {
        let loading = document.getElementById('loadingOverlay');
        
        if (!loading) {
            loading = document.createElement('div');
            loading.id = 'loadingOverlay';
            loading.className = 'loading-overlay';
            loading.innerHTML = `
                <div class="loading-content">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="loading-text mt-3">${message}</div>
                </div>
            `;
            document.body.appendChild(loading);
        }
        
        loading.style.display = 'flex';
    },

    hideLoading: function() {
        const loading = document.getElementById('loadingOverlay');
        if (loading) {
            loading.style.display = 'none';
        }
    },

    showNotification: function(message, type = 'info') {
        // إزالة الإشعارات القديمة
        this.removeOldNotifications();
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show notification-alert`;
        notification.innerHTML = `
            <i class="bi ${this.getNotificationIcon(type)} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        const container = document.querySelector('.notifications-container') || this.createNotificationsContainer();
        container.appendChild(notification);
        
        // إزالة تلقائية بعد 5 ثوانٍ
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    },

    getNotificationIcon: function(type) {
        const icons = {
            'success': 'bi-check-circle-fill',
            'error': 'bi-exclamation-circle-fill',
            'warning': 'bi-exclamation-triangle-fill',
            'info': 'bi-info-circle-fill'
        };
        
        return icons[type] || 'bi-info-circle-fill';
    },

    createNotificationsContainer: function() {
        const container = document.createElement('div');
        container.className = 'notifications-container';
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

    removeOldNotifications: function() {
        const oldNotifications = document.querySelectorAll('.notification-alert');
        oldNotifications.forEach(notification => {
            if (notification.parentNode) {
                notification.remove();
            }
        });
    },

    showSessionWarning: function() {
        const warning = document.createElement('div');
        warning.className = 'alert alert-warning session-warning';
        warning.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>
            جلسة العمل على وشك الانتهاء. الرجاء تسجيل الدخول مرة أخرى.
            <button class="btn btn-sm btn-outline-warning ms-2" onclick="location.reload()">
                تجديد الجلسة
            </button>
        `;
        
        const container = document.querySelector('.session-warnings') || this.createSessionWarningsContainer();
        container.appendChild(warning);
    },

    createSessionWarningsContainer: function() {
        const container = document.createElement('div');
        container.className = 'session-warnings';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        `;
        document.body.appendChild(container);
        return container;
    },

    debounce: function(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }
};

// تهيئة لوحة التحكم
document.addEventListener('DOMContentLoaded', function() {
    AdminDashboard.init();
});