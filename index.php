<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - الصفحة الرئيسية</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link href="assets/css/custom.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #2c3e50;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 100px 0;
            margin-top: 56px; /* لتعويض الـ navbar الثابت */
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--secondary-color);
            display: block;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .category-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        
        .badge-positive { background: #d4edda; color: #155724; }
        .badge-challenges { background: #fff3cd; color: #856404; }
        .badge-concerns { background: #f8d7da; color: #721c24; }
        
        .result-card {
            border-left: 5px solid var(--secondary-color);
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .progress-custom {
            height: 20px;
            border-radius: 10px;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- شريط التنقل -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-journal-bookmark-fill"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="bi bi-house-door"></i> الرئيسية
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">
                            <i class="bi bi-info-circle"></i> عن البحث
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#results">
                            <i class="bi bi-bar-chart"></i> النتائج
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#data">
                            <i class="bi bi-database"></i> البيانات
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/login.php">
                            <i class="bi bi-lock"></i> لوحة التحكم
                        </a>
                    </li>
                </ul>
                
                <form class="d-flex ms-3" id="searchForm">
                    <input class="form-control form-control-sm" type="search" placeholder="ابحث..." id="searchInput">
                    <button class="btn btn-outline-light btn-sm ms-2" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- القسم الرئيسي -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        تصورات المعلمين حول <span class="text-warning">تقنية تحويل النصوص</span> إلى صور متحركة
                    </h1>
                    <p class="lead mb-4">
                        بحث تربوي متكامل يدرس أثر التقنية الحديثة على تنمية كفايات الفهم القرائي 
                        والحد من الأمية الوظيفية - عينة 37 معلماً ومعلمة
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#results" class="btn btn-light btn-lg">
                            <i class="bi bi-bar-chart"></i> استعرض النتائج
                        </a>
                        <a href="#data" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-database"></i> تصفح البيانات
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <div class="animation-demo bg-white p-4 rounded-3 shadow-lg d-inline-block">
                            <div class="d-flex align-items-center mb-3">
                                <div class="p-3 bg-primary text-white rounded-circle me-3">
                                    <i class="bi bi-file-text fs-2"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                                    </div>
                                </div>
                                <div class="p-3 bg-success text-white rounded-circle ms-3">
                                    <i class="bi bi-play-circle fs-2"></i>
                                </div>
                            </div>
                            <p class="text-muted mb-0">محاكاة عملية تحويل النص إلى صورة متحركة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- إحصائيات سريعة -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-people feature-icon"></i>
                        <span class="stat-number" id="totalTeachers">37</span>
                        <span class="stat-label">معلم مشارك</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-check-circle feature-icon"></i>
                        <span class="stat-number">83.8%</span>
                        <span class="stat-label">قناعة بفعالية التقنية</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-exclamation-triangle feature-icon"></i>
                        <span class="stat-number">83.8%</span>
                        <span class="stat-label">نقص البنية التحتية</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <i class="bi bi-lightbulb feature-icon"></i>
                        <span class="stat-number">6</span>
                        <span class="stat-label">محاور بحثية</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- عن البحث -->
    <section id="about" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">عن البحث العلمي</h2>
            <div class="row">
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-bullseye text-primary me-2"></i>أهداف الدراسة
                            </h4>
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    تحليل تصورات المعلمين حول تقنية تحويل النصوص
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    تقييم أثر التقنية على الفهم القرائي
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    دراسة دورها في الحد من الأمية الوظيفية
                                </li>
                                <li class="mb-3">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    تحديد التحديات والمخاوف التربوية
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <h4 class="card-title mb-4">
                                <i class="bi bi-clipboard-data text-primary me-2"></i>المنهجية والعينة
                            </h4>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle p-3 me-3">
                                            <i class="bi bi-people text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0" id="sampleSize">37</h5>
                                            <small class="text-muted">حجم العينة</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success rounded-circle p-3 me-3">
                                            <i class="bi bi-gender-female text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">94.6%</h5>
                                            <small class="text-muted">نسبة الإناث</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle p-3 me-3">
                                            <i class="bi bi-calendar-week text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">5-10 سنوات</h5>
                                            <small class="text-muted">متوسط الخبرة</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-info rounded-circle p-3 me-3">
                                            <i class="bi bi-clipboard-check text-white"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">30</h5>
                                            <small class="text-muted">بند استبيان</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- النتائج التفاعلية -->
    <section id="results" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">النتائج التفاعلية</h2>
            
            <!-- أزرار الفلترة -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active filter-btn" data-category="all">
                            جميع النتائج
                        </button>
                        <button type="button" class="btn btn-outline-success filter-btn" data-category="positive">
                            التصورات الإيجابية
                        </button>
                        <button type="button" class="btn btn-outline-warning filter-btn" data-category="challenges">
                            التحديات
                        </button>
                        <button type="button" class="btn btn-outline-danger filter-btn" data-category="concerns">
                            المخاوف
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- عرض النتائج -->
            <div id="resultsContainer" class="row">
                <div class="col-12 text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل النتائج...</p>
                </div>
            </div>
            
            <!-- الرسوم البيانية -->
            <div class="row mt-5">
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="mb-3">استخدام التقنية حالياً</h5>
                        <canvas id="techUsageChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="mb-3">المعرفة بتقنيات التحويل</h5>
                        <canvas id="knowledgeChart" height="200"></canvas>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="chart-container">
                        <h5 class="mb-3">فعالية الوسائل البصرية</h5>
                        <canvas id="effectivenessChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- تحليل البيانات -->
    <section id="analysis" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">التحليل الإحصائي</h2>
            
            <ul class="nav nav-tabs justify-content-center mb-4" id="analysisTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="gender-tab" data-bs-toggle="tab" data-bs-target="#gender" type="button">
                        <i class="bi bi-gender-ambiguous"></i> حسب الجنس
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="experience-tab" data-bs-toggle="tab" data-bs-target="#experience" type="button">
                        <i class="bi bi-calendar"></i> حسب الخبرة
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="specialization-tab" data-bs-toggle="tab" data-bs-target="#specialization" type="button">
                        <i class="bi bi-book"></i> حسب التخصص
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="analysisTabContent">
                <div class="tab-pane fade show active" id="gender" role="tabpanel">
                    <div class="row">
                        <div class="col-md-8">
                            <canvas id="genderChart"></canvas>
                        </div>
                        <div class="col-md-4">
                            <div id="genderAnalysis" class="p-3 bg-light rounded">
                                <h5>التحليل حسب الجنس</h5>
                                <p class="mt-3">جاري تحميل البيانات...</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="experience" role="tabpanel">
                    <div id="experienceAnalysis">
                        <p>جاري تحميل تحليل الخبرة...</p>
                    </div>
                </div>
                <div class="tab-pane fade" id="specialization" role="tabpanel">
                    <div id="specializationAnalysis">
                        <p>جاري تحميل تحليل التخصص...</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- عرض البيانات -->
    <section id="data" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">عرض البيانات التفاعلي</h2>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchData" placeholder="ابحث في بيانات المعلمين...">
                        <button class="btn btn-primary" type="button" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="api/export.php?format=json" class="btn btn-outline-secondary">
                            <i class="bi bi-file-code"></i> JSON
                        </a>
                        <a href="api/export.php?format=csv" class="btn btn-outline-secondary">
                            <i class="bi bi-file-spreadsheet"></i> CSV
                        </a>
                        <button class="btn btn-success" onclick="window.print()">
                            <i class="bi bi-printer"></i> طباعة
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover table-striped" id="teachersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>كود المعلم</th>
                            <th>الجنس</th>
                            <th>سنوات الخبرة</th>
                            <th>الدرجة</th>
                            <th>التخصص</th>
                            <th>استخدام التقنية</th>
                            <th>معرفة التحويل</th>
                            <th>فعالية الوسائل</th>
                        </tr>
                    </thead>
                    <tbody id="teachersData">
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="spinner-border spinner-border-sm" role="status"></div>
                                جاري تحميل البيانات...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <nav aria-label="Page navigation" id="pagination">
                <ul class="pagination justify-content-center">
                    <!-- سيتم إنشاء الترقيم ديناميكياً -->
                </ul>
            </nav>
        </div>
    </section>

    <!-- تذييل الصفحة -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        <?php echo SITE_NAME; ?>
                    </h5>
                    <p>بحث تربوي متكامل حول استخدام تقنية تحويل النصوص إلى صور متحركة في التعليم.</p>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> الرئيسية
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#results" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> النتائج
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="#data" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> البيانات
                            </a>
                        </li>
                        <li>
                            <a href="admin/login.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> لوحة التحكم
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">معلومات الاستضافة</h5>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-hdd-network me-2"></i>
                        <span>مستضاف على InfinityFree</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-database me-2"></i>
                        <span>قاعدة بيانات MySQL</span>
                    </div>
                    <div class="mt-3">
                        <a href="https://infinityfree.net" target="_blank" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i> زيارة InfinityFree
                        </a>
                    </div>
                </div>
            </div>
            
            <hr class="bg-white">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2024 بحث تربوي. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        تطوير بواسطة 
                        <span class="text-warning">فريق البحث التربوي</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <script>
    // تهيئة الصفحة
    document.addEventListener('DOMContentLoaded', function() {
        loadInitialData();
        setupEventListeners();
    });
    
    function loadInitialData() {
        loadResults('all');
        loadTeachersData();
        loadCharts();
        loadStatistics();
    }
    
    function setupEventListeners() {
        // أحداث أزرار الفلترة
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => {
                    b.classList.remove('active');
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                });
                
                this.classList.add('active');
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                const category = this.dataset.category;
                loadResults(category);
            });
        });
        
        // حدث البحث
        document.getElementById('searchBtn').addEventListener('click', function() {
            const query = document.getElementById('searchData').value;
            searchTeachers(query);
        });
        
        // بحث بالضغط على Enter
        document.getElementById('searchData').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchTeachers(this.value);
            }
        });
    }
    
    function loadResults(category) {
        const container = document.getElementById('resultsContainer');
        container.innerHTML = `
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري التحميل...</span>
                </div>
                <p class="mt-2">جاري تحميل النتائج...</p>
            </div>
        `;
        
        fetch(`api/results.php?category=${category}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderResults(data.data);
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i>
                            حدث خطأ في تحميل البيانات. يرجى المحاولة لاحقاً.
                        </div>
                    </div>
                `;
                console.error('Error:', error);
            });
    }
    
    function renderResults(data) {
        const container = document.getElementById('resultsContainer');
        let html = '';
        
        Object.keys(data).forEach(category => {
            const categoryData = data[category];
            
            html += `
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">${getCategoryName(category)}</h5>
                        </div>
                        <div class="card-body">
            `;
            
            Object.keys(categoryData.questions).forEach(questionKey => {
                const question = categoryData.questions[questionKey];
                
                html += `
                    <div class="result-card mb-3">
                        <h6 class="mb-3">${question.text}</h6>
                `;
                
                let total = 0;
                question.answers.forEach(answer => {
                    total += answer.count;
                });
                
                question.answers.forEach(answer => {
                    const percentage = (answer.count / total) * 100;
                    const color = getAnswerColor(answer.value);
                    
                    html += `
                        <div class="mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <span>${answer.value}</span>
                                <span>${answer.count} (${percentage.toFixed(1)}%)</span>
                            </div>
                            <div class="progress progress-custom">
                                <div class="progress-bar ${color}" 
                                     style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                });
                
                html += `</div>`;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    function loadTeachersData(page = 1) {
        fetch(`api/teachers.php?page=${page}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderTeachersTable(data.data);
                    renderPagination(data.pagination);
                }
            });
    }
    
    function renderTeachersTable(teachers) {
        const tbody = document.getElementById('teachersData');
        
        if (teachers.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="alert alert-info mb-0">
                            <i class="bi bi-info-circle"></i>
                            لا توجد بيانات لعرضها.
                        </div>
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
                        <span class="badge ${getTechUsageBadge(teacher.current_tech_usage)}">
                            ${teacher.current_tech_usage || ''}
                        </span>
                    </td>
                    <td>${teacher.animation_knowledge || ''}</td>
                    <td>${teacher.visual_effectiveness || ''}</td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    function getCategoryName(category) {
        const names = {
            'positive': 'التصورات الإيجابية',
            'challenges': 'التحديات والعوائق',
            'concerns': 'المخاوف التربوية',
            'recommendations': 'التوصيات والمقترحات'
        };
        return names[category] || category;
    }
    
    function getAnswerColor(answer) {
        if (answer.includes('موافق بشدة') || answer === 'نعم') return 'bg-success';
        if (answer.includes('موافق')) return 'bg-primary';
        if (answer.includes('محايد')) return 'bg-warning';
        if (answer.includes('غير موافق') || answer === 'لا') return 'bg-danger';
        return 'bg-secondary';
    }
    
    function getTechUsageBadge(usage) {
        switch(usage) {
            case 'ممتاز': return 'bg-success';
            case 'جيد': return 'bg-primary';
            case 'متوسط': return 'bg-warning';
            case 'ضعيف': return 'bg-danger';
            case 'ضعيف جداً': return 'bg-dark';
            default: return 'bg-secondary';
        }
    }
    
    // ستضيف بقية الدوال هنا
    </script>
</body>
</html>