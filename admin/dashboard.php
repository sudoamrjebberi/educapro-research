<?php
session_start();
require_once '../config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// جلب الإحصائيات
$conn = connectDB();

// إحصائيات عامة
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total FROM teachers");
$stats['total_teachers'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(*) as total FROM surveys");
$stats['total_surveys'] = $result->fetch_assoc()['total'];

$result = $conn->query("SELECT COUNT(DISTINCT teacher_id) as active FROM surveys");
$stats['active_participants'] = $result->fetch_assoc()['active'];

$result = $conn->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM surveys 
    GROUP BY DATE(created_at) 
    ORDER BY date DESC 
    LIMIT 7
");
$stats['recent_activity'] = [];
while ($row = $result->fetch_assoc()) {
    $stats['recent_activity'][] = $row;
}

// أحدث الاستبيانات
$result = $conn->query("
    SELECT s.*, t.teacher_code, t.gender, t.specialization
    FROM surveys s
    JOIN teachers t ON s.teacher_id = t.id
    ORDER BY s.created_at DESC
    LIMIT 10
");
$recent_surveys = [];
while ($row = $result->fetch_assoc()) {
    $recent_surveys[] = $row;
}

// توزيع الإجابات
$result = $conn->query("
    SELECT question_category, answer_value, COUNT(*) as count
    FROM answers
    GROUP BY question_category, answer_value
    ORDER BY question_category, answer_value
");
$answer_distribution = [];
while ($row = $result->fetch_assoc()) {
    $answer_distribution[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم - الرئيسية</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .welcome-message {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            margin-left: 20px;
        }
        
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-box:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            display: block;
            margin: 10px 0;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .recent-activity {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .activity-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .activity-table th,
        .activity-table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #eee;
        }
        
        .activity-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        
        .badge-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        .badge-complete {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="admin-body">
    <!-- الشريط الجانبي -->
    <div class="sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="logo">
                <i class="fas fa-chart-bar"></i>
                <span>بحث تربوي</span>
            </a>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active"><i class="fas fa-home"></i> الرئيسية</a></li>
            <li><a href="teachers.php"><i class="fas fa-users"></i> إدارة المعلمين</a></li>
            <li><a href="surveys.php"><i class="fas fa-clipboard-list"></i> الاستبيانات</a></li>
            <li><a href="results.php"><i class="fas fa-chart-pie"></i> التحليلات</a></li>
            <li><a href="export.php"><i class="fas fa-download"></i> تصدير البيانات</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> الإعدادات</a></li>
        </ul>
        
        <div class="sidebar-footer">
            <div class="user-info">
                <div class="avatar"><?php echo substr($_SESSION['admin_username'], 0, 1); ?></div>
                <div>
                    <div class="user-name"><?php echo $_SESSION['admin_username']; ?></div>
                    <small>مدير النظام</small>
                </div>
            </div>
            <a href="logout.php" class="btn-admin btn-admin-danger btn-sm mt-3" style="width: 100%;">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
            </a>
        </div>
    </div>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <div class="dashboard-header">
            <div class="welcome-message">
                <h2>مرحباً، <?php echo $_SESSION['admin_username']; ?>!</h2>
                <p>إحصائيات وتقارير البحث التربوي</p>
            </div>
            <div class="header-actions">
                <button class="btn-admin btn-admin-primary">
                    <i class="fas fa-sync-alt"></i> تحديث
                </button>
                <button class="btn-admin btn-admin-success">
                    <i class="fas fa-download"></i> تقرير
                </button>
            </div>
        </div>
        
        <!-- الإحصائيات السريعة -->
        <div class="quick-stats">
            <div class="stat-box">
                <div class="stat-icon text-primary">
                    <i class="fas fa-users"></i>
                </div>
                <span class="stat-number"><?php echo $stats['total_teachers']; ?></span>
                <span class="stat-label">عدد المعلمين</span>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon text-success">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <span class="stat-number"><?php echo $stats['total_surveys']; ?></span>
                <span class="stat-label">استبيان مكتمل</span>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon text-warning">
                    <i class="fas fa-chart-line"></i>
                </div>
                <span class="stat-number"><?php echo $stats['active_participants']; ?></span>
                <span class="stat-label">مشارك نشط</span>
            </div>
            
            <div class="stat-box">
                <div class="stat-icon text-danger">
                    <i class="fas fa-percentage"></i>
                </div>
                <span class="stat-number">
                    <?php echo $stats['total_teachers'] > 0 ? 
                        round(($stats['active_participants'] / $stats['total_teachers']) * 100, 1) : 0; ?>%
                </span>
                <span class="stat-label">نسبة المشاركة</span>
            </div>
        </div>
        
        <!-- النشاط الأخير -->
        <div class="recent-activity">
            <h3><i class="fas fa-history"></i> أحدث الاستبيانات</h3>
            <table class="activity-table">
                <thead>
                    <tr>
                        <th>كود المعلم</th>
                        <th>التخصص</th>
                        <th>استخدام التقنية</th>
                        <th>معرفة التحويل</th>
                        <th>الفعالية</th>
                        <th>التاريخ</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_surveys as $survey): ?>
                    <tr>
                        <td><?php echo $survey['teacher_code']; ?></td>
                        <td><?php echo $survey['specialization']; ?></td>
                        <td>
                            <span class="badge 
                                <?php 
                                if($survey['current_tech_usage'] == 'ممتاز') echo 'bg-success';
                                elseif($survey['current_tech_usage'] == 'جيد') echo 'bg-primary';
                                elseif($survey['current_tech_usage'] == 'متوسط') echo 'bg-warning';
                                else echo 'bg-danger';
                                ?>">
                                <?php echo $survey['current_tech_usage']; ?>
                            </span>
                        </td>
                        <td><?php echo $survey['animation_knowledge']; ?></td>
                        <td>
                            <span class="badge 
                                <?php 
                                if($survey['visual_effectiveness'] == 'ممتاز') echo 'bg-success';
                                elseif($survey['visual_effectiveness'] == 'جيد') echo 'bg-primary';
                                elseif($survey['visual_effectiveness'] == 'متوسط') echo 'bg-warning';
                                else echo 'bg-danger';
                                ?>">
                                <?php echo $survey['visual_effectiveness']; ?>
                            </span>
                        </td>
                        <td><?php echo date('Y-m-d', strtotime($survey['created_at'])); ?></td>
                        <td><span class="badge-status badge-complete">مكتمل</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- الرسوم البيانية -->
        <div class="chart-container">
            <h3><i class="fas fa-chart-bar"></i> توزيع الإجابات حسب الفئة</h3>
            <canvas id="distributionChart" height="100"></canvas>
        </div>
        
        <div class="chart-container">
            <h3><i class="fas fa-chart-line"></i> النشاط خلال الأسبوع</h3>
            <canvas id="activityChart" height="100"></canvas>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // مخطط توزيع الإجابات
        const distCtx = document.getElementById('distributionChart').getContext('2d');
        const distChart = new Chart(distCtx, {
            type: 'bar',
            data: {
                labels: ['إيجابية', 'تحديات', 'مخاوف', 'توصيات'],
                datasets: [{
                    label: 'عدد الإجابات',
                    data: [
                        <?php echo count(array_filter($answer_distribution, fn($a) => $a['question_category'] == 'positive')); ?>,
                        <?php echo count(array_filter($answer_distribution, fn($a) => $a['question_category'] == 'challenges')); ?>,
                        <?php echo count(array_filter($answer_distribution, fn($a) => $a['question_category'] == 'concerns')); ?>,
                        <?php echo count(array_filter($answer_distribution, fn($a) => $a['question_category'] == 'recommendations')); ?>
                    ],
                    backgroundColor: [
                        '#2ecc71',
                        '#e74c3c',
                        '#f39c12',
                        '#3498db'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // مخطط النشاط
        const actCtx = document.getElementById('activityChart').getContext('2d');
        const activityData = <?php echo json_encode(array_reverse($stats['recent_activity'])); ?>;
        const actChart = new Chart(actCtx, {
            type: 'line',
            data: {
                labels: activityData.map(a => a.date),
                datasets: [{
                    label: 'عدد الاستبيانات',
                    data: activityData.map(a => a.count),
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
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
                            stepSize: 1
                        }
                    }
                }
            }
        });
    });
    </script>
</body>
</html>