// ===== مكتبة الرسوم البيانية المخصصة للبحث =====

const ResearchCharts = {
    // إعدادات الرسوم البيانية
    config: {
        colors: {
            primary: '#3498db',
            secondary: '#2ecc71',
            warning: '#f39c12',
            danger: '#e74c3c',
            info: '#9b59b6',
            light: '#ecf0f1',
            dark: '#34495e'
        },
        fontFamily: 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif',
        rtl: true
    },

    // تهيئة الرسوم البيانية
    init: function() {
        console.log('📊 تهيئة مكتبة الرسوم البيانية...');
        
        // تعيين الإعدادات العامة
        Chart.defaults.font.family = this.config.fontFamily;
        Chart.defaults.rtl = this.config.rtl;
        
        // تسجيل المكونات المخصصة
        this.registerCustomElements();
    },

    // تسجيل العناصر المخصصة
    registerCustomElements: function() {
        // تسجيل مخطط نسبة المشاركة
        Chart.register({
            id: 'participationChart',
            beforeDraw: function(chart) {
                if (chart.config.type === 'participation') {
                    const ctx = chart.ctx;
                    const centerX = chart.chartArea.left + (chart.chartArea.right - chart.chartArea.left) / 2;
                    const centerY = chart.chartArea.top + (chart.chartArea.bottom - chart.chartArea.top) / 2;
                    
                    // رسم خلفية
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, 80, 0, Math.PI * 2);
                    ctx.fillStyle = '#f8f9fa';
                    ctx.fill();
                    ctx.restore();
                    
                    // كتابة النسبة
                    ctx.save();
                    ctx.font = 'bold 24px ' + ResearchCharts.config.fontFamily;
                    ctx.fillStyle = ResearchCharts.config.colors.primary;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillText('85%', centerX, centerY);
                    ctx.restore();
                    
                    // كتابة التسمية
                    ctx.save();
                    ctx.font = '12px ' + ResearchCharts.config.fontFamily;
                    ctx.fillStyle = ResearchCharts.config.colors.dark;
                    ctx.textAlign = 'center';
                    ctx.fillText('معدل المشاركة', centerX, centerY + 30);
                    ctx.restore();
                }
            }
        });
    },

    // إنشاء مخطط استخدام التقنية
    createTechUsageChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
            datasets: [{
                label: 'عدد المعلمين',
                data: data || [1, 7, 17, 7, 5],
                backgroundColor: [
                    this.config.colors.secondary,
                    this.config.colors.primary,
                    this.config.colors.warning,
                    this.config.colors.danger,
                    '#95a5a6'
                ],
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        return new Chart(ctx, {
            type: 'bar',
            data: defaultData,
            options: this.getBarChartOptions('توزيع استخدام التقنية الحالي')
        });
    },

    // إنشاء مخطط المعرفة بالتقنية
    createKnowledgeChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'],
            datasets: [{
                data: data || [6, 11, 12, 8],
                backgroundColor: [
                    this.config.colors.primary,
                    this.config.colors.warning,
                    this.config.colors.danger,
                    '#95a5a6'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        return new Chart(ctx, {
            type: 'doughnut',
            data: defaultData,
            options: this.getDoughnutChartOptions('مستوى المعرفة بتقنيات التحويل')
        });
    },

    // إنشاء مخطط الفعالية
    createEffectivenessChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['ممتاز', 'جيد', 'متوسط', 'ضعيف'],
            datasets: [{
                label: 'مستوى الفعالية',
                data: data || [4, 12, 16, 5],
                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                borderColor: this.config.colors.primary,
                borderWidth: 2,
                pointBackgroundColor: this.config.colors.primary,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                fill: true
            }]
        };

        return new Chart(ctx, {
            type: 'radar',
            data: defaultData,
            options: this.getRadarChartOptions('فعالية الوسائل البصرية')
        });
    },

    // إنشاء مخطط التحديات
    createChallengesChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['بنية تحتية', 'صعوبات تقنية', 'وقت تحضير', 'مقاومة'],
            datasets: [{
                label: 'نسبة المواجهة',
                data: data || [83.8, 75.7, 73.0, 18.9],
                backgroundColor: [
                    this.config.colors.danger,
                    this.config.colors.warning,
                    this.config.colors.info,
                    this.config.colors.light
                ],
                borderWidth: 1
            }]
        };

        return new Chart(ctx, {
            type: 'horizontalBar',
            data: defaultData,
            options: this.getHorizontalBarOptions('التحديات الرئيسية')
        });
    },

    // إنشاء مخطط المخاوف
    createConcernsChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: [
                'تبسيط التفسيرات',
                'تقليل وقت المهارات',
                'تنميط الشخصيات',
                'عدم ملاءمة سمعية',
                'التركيز على الترفيه',
                'إضعاف الخيال'
            ],
            datasets: [{
                label: 'مستوى القلق (%)',
                data: data || [64.9, 62.2, 62.2, 56.8, 48.6, 43.2],
                backgroundColor: (context) => {
                    const value = context.dataset.data[context.dataIndex];
                    if (value > 60) return this.config.colors.danger;
                    if (value > 50) return this.config.colors.warning;
                    return this.config.colors.info;
                }
            }]
        };

        return new Chart(ctx, {
            type: 'bar',
            data: defaultData,
            options: this.getConcernsChartOptions('المخاوف التربوية')
        });
    },

    // إنشاء مخطط حسب الجنس
    createGenderChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['إناث', 'ذكور'],
            datasets: [{
                data: data || [35, 2],
                backgroundColor: [
                    '#e83e8c',
                    this.config.colors.primary
                ],
                borderWidth: 3,
                borderColor: '#fff'
            }]
        };

        return new Chart(ctx, {
            type: 'pie',
            data: defaultData,
            options: this.getPieChartOptions('توزيع العينة حسب الجنس')
        });
    },

    // إنشاء مخطط حسب الخبرة
    createExperienceChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['أقل من 5 سنوات', '5-10 سنوات', 'أكثر من 10 سنوات'],
            datasets: [{
                label: 'عدد المعلمين',
                data: data || [4, 23, 10],
                backgroundColor: [
                    this.config.colors.secondary,
                    this.config.colors.primary,
                    this.config.colors.warning
                ],
                borderWidth: 0
            }]
        };

        return new Chart(ctx, {
            type: 'polarArea',
            data: defaultData,
            options: this.getPolarAreaOptions('توزيع العينة حسب الخبرة')
        });
    },

    // إنشاء مخطط التخصصات
    createSpecializationChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['اللغة العربية', 'العلوم', 'اللغات الأجنبية', 'الاجتماعيات'],
            datasets: [{
                data: data || [22, 20, 13, 10],
                backgroundColor: [
                    this.config.colors.primary,
                    this.config.colors.secondary,
                    this.config.colors.info,
                    this.config.colors.warning
                ]
            }]
        };

        return new Chart(ctx, {
            type: 'doughnut',
            data: defaultData,
            options: this.getSpecializationChartOptions('توزيع التخصصات التعليمية')
        });
    },

    // إنشاء مخطط زمني
    createTimelineChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: ['ديسمبر', 'يناير'],
            datasets: [{
                label: 'عدد الاستبيانات',
                data: data || [15, 22],
                backgroundColor: this.config.colors.primary + '20',
                borderColor: this.config.colors.primary,
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        };

        return new Chart(ctx, {
            type: 'line',
            data: defaultData,
            options: this.getLineChartOptions('تطور جمع البيانات')
        });
    },

    // إنشاء مخطط النتائج الإيجابية
    createPositiveResultsChart: function(canvasId, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        const defaultData = {
            labels: [
                'مساعدة التخيل',
                'جذب الانتباه',
                'تسهيل التسلسل',
                'فهم النصوص المعقدة',
                'تحسين التحليل',
                'ملاءمة الأنماط'
            ],
            datasets: [{
                label: 'نسبة الموافقة (%)',
                data: data || [89.2, 81.1, 81.1, 83.8, 83.8, 78.4],
                backgroundColor: (context) => {
                    const value = context.dataset.data[context.dataIndex];
                    if (value > 85) return this.config.colors.secondary;
                    if (value > 80) return this.config.colors.primary;
                    return this.config.colors.warning;
                },
                borderWidth: 1,
                borderRadius: 5
            }]
        };

        return new Chart(ctx, {
            type: 'bar',
            data: defaultData,
            options: this.getPositiveResultsOptions('التصورات الإيجابية')
        });
    },

    // ===== إعدادات الرسوم البيانية =====

    getBarChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
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
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        };
    },

    getDoughnutChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 10
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        };
    },

    getRadarChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 20,
                    ticks: {
                        stepSize: 5,
                        display: false
                    },
                    pointLabels: {
                        font: {
                            size: 12
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                }
            },
            elements: {
                line: {
                    tension: 0.3
                }
            }
        };
    },

    getHorizontalBarOptions: function(title) {
        return {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.x}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    grid: {
                        display: false
                    }
                }
            }
        };
    },

    getConcernsChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    },
                    grid: {
                        display: false
                    }
                }
            }
        };
    },

    getPieChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 10
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        };
    },

    getPolarAreaOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 10
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.label}: ${context.parsed.r}`;
                        }
                    }
                }
            },
            scales: {
                r: {
                    ticks: {
                        display: false
                    }
                }
            }
        };
    },

    getSpecializationChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 11
                        }
                    }
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 10
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed / total) * 100);
                            return `${context.label}: ${context.parsed} (${percentage}%)`;
                        }
                    }
                }
            }
        };
    },

    getLineChartOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 5
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    radius: 6,
                    hoverRadius: 8
                }
            }
        };
    },

    getPositiveResultsOptions: function(title) {
        return {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: title,
                    font: {
                        size: 16,
                        weight: 'bold'
                    },
                    padding: 20
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `${context.parsed.x}%`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                y: {
                    ticks: {
                        autoSkip: false,
                        maxRotation: 0
                    },
                    grid: {
                        display: false
                    }
                }
            }
        };
    },

    // ===== وظائف مساعدة =====

    // تحديث بيانات المخطط
    updateChartData: function(chart, newData) {
        if (!chart || !newData) return;
        
        chart.data.datasets[0].data = newData;
        chart.update();
    },

    // تحميل بيانات المخطط من API
    loadChartData: async function(chartId, endpoint) {
        try {
            const response = await fetch(endpoint);
            const data = await response.json();
            
            if (data.success && data.data) {
                const chart = Chart.getChart(chartId);
                if (chart) {
                    this.updateChartData(chart, data.data);
                }
            }
        } catch (error) {
            console.error(`خطأ في تحميل بيانات المخطط ${chartId}:`, error);
        }
    },

    // إنشاء جميع الرسوم البيانية
    createAllCharts: function() {
        const charts = {};
        
        charts.techUsage = this.createTechUsageChart('techUsageChart');
        charts.knowledge = this.createKnowledgeChart('knowledgeChart');
        charts.effectiveness = this.createEffectivenessChart('effectivenessChart');
        charts.challenges = this.createChallengesChart('challengesChart');
        charts.concerns = this.createConcernsChart('concernsChart');
        charts.gender = this.createGenderChart('genderChart');
        charts.experience = this.createExperienceChart('experienceChart');
        charts.specialization = this.createSpecializationChart('specializationChart');
        charts.timeline = this.createTimelineChart('timelineChart');
        charts.positiveResults = this.createPositiveResultsChart('positiveResultsChart');
        
        return charts;
    },

    // تدمير جميع الرسوم البيانية
    destroyAllCharts: function() {
        Chart.instances.forEach(instance => {
            instance.destroy();
        });
    },

    // تصدير المخطط كصورة
    exportChartAsImage: function(chartId, fileName = 'chart') {
        const chart = Chart.getChart(chartId);
        if (!chart) {
            console.error('لم يتم العثور على المخطط:', chartId);
            return;
        }
        
        const image = chart.toBase64Image();
        const link = document.createElement('a');
        link.href = image;
        link.download = `${fileName}_${new Date().toISOString().split('T')[0]}.png`;
        link.click();
    },

    // طباعة المخطط
    printChart: function(chartId) {
        const chart = Chart.getChart(chartId);
        if (!chart) {
            console.error('لم يتم العثور على المخطط:', chartId);
            return;
        }
        
        const printWindow = window.open('', '_blank');
        const image = chart.toBase64Image();
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl">
            <head>
                <title>طباعة المخطط</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        text-align: center; 
                        padding: 50px; 
                        direction: rtl;
                    }
                    img { 
                        max-width: 100%; 
                        height: auto; 
                        margin: 20px 0;
                    }
                    .print-info {
                        margin: 20px 0;
                        color: #666;
                    }
                </style>
            </head>
            <body>
                <h2>مخطط البحث التربوي</h2>
                <div class="print-info">
                    <p>تم إنشاء هذا المخطط في: ${new Date().toLocaleString('ar-SA')}</p>
                </div>
                <img src="${image}" alt="مخطط البحث">
                <div class="print-info">
                    <p>نظام عرض نتائج البحث التربوي © ${new Date().getFullYear()}</p>
                </div>
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(function() {
                            window.close();
                        }, 1000);
                    }
                </script>
            </body>
            </html>
        `);
    }
};

// ===== تهيئة مكتبة الرسوم البيانية =====
document.addEventListener('DOMContentLoaded', function() {
    ResearchCharts.init();
});

// ===== جعل المكتبة متاحة عالمياً =====
window.ResearchCharts = ResearchCharts;