<?php
session_start();
require_once '../config.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

$conn = connectDB();

// الحصول على نوع العملية والجدول
$action = isset($_GET['action']) ? sanitize($_GET['action']) : 'list';
$table = isset($_GET['table']) ? sanitize($_GET['table']) : 'teachers';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// رسائل النجاح/الخطأ
$success_message = '';
$error_message = '';

// معالجة طلبات POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($table) {
            case 'teachers':
                if ($action === 'add') {
                    $stmt = $conn->prepare("
                        INSERT INTO teachers (
                            teacher_code, 
                            gender, 
                            experience_years, 
                            education_level, 
                            specialization
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->bind_param(
                        'ssiss',
                        $_POST['teacher_code'],
                        $_POST['gender'],
                        $_POST['experience_years'],
                        $_POST['education_level'],
                        $_POST['specialization']
                    );
                    
                    if ($stmt->execute()) {
                        $success_message = 'تم إضافة المعلم بنجاح';
                    } else {
                        $error_message = 'فشل إضافة المعلم: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                    
                } elseif ($action === 'edit' && $id > 0) {
                    $stmt = $conn->prepare("
                        UPDATE teachers SET 
                            teacher_code = ?, 
                            gender = ?, 
                            experience_years = ?, 
                            education_level = ?, 
                            specialization = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->bind_param(
                        'ssissi',
                        $_POST['teacher_code'],
                        $_POST['gender'],
                        $_POST['experience_years'],
                        $_POST['education_level'],
                        $_POST['specialization'],
                        $id
                    );
                    
                    if ($stmt->execute()) {
                        $success_message = 'تم تحديث بيانات المعلم بنجاح';
                    } else {
                        $error_message = 'فشل تحديث المعلم: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                    
                } elseif ($action === 'delete' && $id > 0) {
                    $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
                    $stmt->bind_param('i', $id);
                    
                    if ($stmt->execute()) {
                        $success_message = 'تم حذف المعلم بنجاح';
                    } else {
                        $error_message = 'فشل حذف المعلم: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                }
                break;
                
            case 'surveys':
                if ($action === 'add') {
                    $stmt = $conn->prepare("
                        INSERT INTO surveys (
                            teacher_id,
                            current_tech_usage,
                            animation_knowledge,
                            visual_effectiveness,
                            comments
                        ) VALUES (?, ?, ?, ?, ?)
                    ");
                    
                    $stmt->bind_param(
                        'issss',
                        $_POST['teacher_id'],
                        $_POST['current_tech_usage'],
                        $_POST['animation_knowledge'],
                        $_POST['visual_effectiveness'],
                        $_POST['comments']
                    );
                    
                    if ($stmt->execute()) {
                        $success_message = 'تم إضافة الاستبيان بنجاح';
                    } else {
                        $error_message = 'فشل إضافة الاستبيان: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                    
                } elseif ($action === 'edit' && $id > 0) {
                    $stmt = $conn->prepare("
                        UPDATE surveys SET 
                            current_tech_usage = ?,
                            animation_knowledge = ?,
                            visual_effectiveness = ?,
                            comments = ?
                        WHERE id = ?
                    ");
                    
                    $stmt->bind_param(
                        'ssssi',
                        $_POST['current_tech_usage'],
                        $_POST['animation_knowledge'],
                        $_POST['visual_effectiveness'],
                        $_POST['comments'],
                        $id
                    );
                    
                    if ($stmt->execute()) {
                        $success_message = 'تم تحديث الاستبيان بنجاح';
                    } else {
                        $error_message = 'فشل تحديث الاستبيان: ' . $stmt->error;
                    }
                    
                    $stmt->close();
                }
                break;
        }
        
        // التوجيه بعد المعالجة
        if ($success_message && $action !== 'delete') {
            header("Location: manage.php?table=$table&action=list&success=" . urlencode($success_message));
            exit();
        }
        
    } catch (Exception $e) {
        $error_message = 'حدث خطأ: ' . $e->getMessage();
    }
}

// جلب البيانات للعرض
$data = [];
$item = [];
$columns = [];
$teachers_list = [];

switch ($table) {
    case 'teachers':
        // جلب قائمة المعلمين
        $result = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // جلب معلم محدد للتعديل
        if ($id > 0 && $action === 'edit') {
            $stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();
        }
        
        $columns = [
            'teacher_code' => 'كود المعلم',
            'gender' => 'الجنس',
            'experience_years' => 'سنوات الخبرة',
            'education_level' => 'الدرجة العلمية',
            'specialization' => 'التخصص'
        ];
        break;
        
    case 'surveys':
        // جلب قائمة الاستبيانات مع بيانات المعلمين
        $result = $conn->query("
            SELECT s.*, t.teacher_code, t.specialization 
            FROM surveys s 
            JOIN teachers t ON s.teacher_id = t.id 
            ORDER BY s.created_at DESC
        ");
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // جلب استبيان محدد للتعديل
        if ($id > 0 && $action === 'edit') {
            $stmt = $conn->prepare("
                SELECT s.*, t.teacher_code 
                FROM surveys s 
                JOIN teachers t ON s.teacher_id = t.id 
                WHERE s.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();
        }
        
        // جلب قائمة المعلمين للقائمة المنسدلة
        $teachers_result = $conn->query("SELECT id, teacher_code, specialization FROM teachers ORDER BY teacher_code");
        while ($row = $teachers_result->fetch_assoc()) {
            $teachers_list[] = $row;
        }
        
        $columns = [
            'teacher_code' => 'كود المعلم',
            'current_tech_usage' => 'استخدام التقنية',
            'animation_knowledge' => 'معرفة التحويل',
            'visual_effectiveness' => 'فعالية الوسائل',
            'created_at' => 'تاريخ الإدخال'
        ];
        break;
        
    case 'answers':
        // جلب الإجابات مع البيانات المرتبطة
        $result = $conn->query("
            SELECT a.*, t.teacher_code, s.created_at 
            FROM answers a 
            JOIN surveys s ON a.survey_id = s.id 
            JOIN teachers t ON s.teacher_id = t.id 
            ORDER BY a.id DESC
            LIMIT 100
        ");
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        $columns = [
            'teacher_code' => 'كود المعلم',
            'question_category' => 'فئة السؤال',
            'question_text' => 'نص السؤال',
            'answer_value' => 'الإجابة',
            'created_at' => 'تاريخ الإدخال'
        ];
        break;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة البيانات - لوحة التحكم</title>
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .manage-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .page-title {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .page-title i {
            font-size: 24px;
            color: #3498db;
        }
        
        .table-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .breadcrumb {
            background: #f8f9fa;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }
        
        .breadcrumb .separator {
            color: #666;
            margin: 0 10px;
        }
        
        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .data-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 2px solid #dee2e6;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background: #3498db;
            color: white;
        }
        
        .btn-delete {
            background: #e74c3c;
            color: white;
        }
        
        .btn-view {
            background: #2ecc71;
            color: white;
        }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 5px rgba(0,0,0,0.2);
        }
        
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
            outline: none;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .alert-message {
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ddd;
        }
    </style>
</head>
<body class="admin-body">
    <!-- الشريط الجانبي -->
    <?php include '../includes/sidebar.php'; ?>
    
    <!-- المحتوى الرئيسي -->
    <div class="main-content">
        <div class="manage-header">
            <div class="page-title">
                <?php if ($table === 'teachers'): ?>
                <i class="fas fa-users"></i>
                <h1>إدارة المعلمين</h1>
                <?php elseif ($table === 'surveys'): ?>
                <i class="fas fa-clipboard-list"></i>
                <h1>إدارة الاستبيانات</h1>
                <?php elseif ($table === 'answers'): ?>
                <i class="fas fa-comments"></i>
                <h1>إدارة الإجابات</h1>
                <?php endif; ?>
            </div>
            
            <div class="table-actions">
                <a href="dashboard.php" class="btn-admin btn-admin-primary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
                
                <?php if ($action === 'list'): ?>
                <a href="manage.php?table=<?php echo $table; ?>&action=add" class="btn-admin btn-admin-success">
                    <i class="fas fa-plus"></i> إضافة جديد
                </a>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- مسار التنقل -->
        <div class="breadcrumb">
            <a href="dashboard.php">لوحة التحكم</a>
            <span class="separator">/</span>
            <span>إدارة <?php 
                echo $table === 'teachers' ? 'المعلمين' : 
                     ($table === 'surveys' ? 'الاستبيانات' : 'الإجابات');
            ?></span>
        </div>
        
        <!-- عرض رسائل النجاح/الخطأ -->
        <?php if (isset($_GET['success'])): ?>
        <div class="alert-message alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
        <?php elseif ($success_message): ?>
        <div class="alert-message alert-success">
            <i class="fas fa-check-circle"></i>
            <?php echo $success_message; ?>
        </div>
        <?php elseif ($error_message): ?>
        <div class="alert-message alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($action === 'list'): ?>
        <!-- عرض الجدول -->
        <div class="data-table">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <?php foreach ($columns as $key => $label): ?>
                            <th><?php echo $label; ?></th>
                            <?php endforeach; ?>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="<?php echo count($columns) + 1; ?>" class="text-center py-4">
                                <div class="no-data">
                                    <i class="fas fa-database"></i>
                                    <p>لا توجد بيانات لعرضها</p>
                                </div>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($data as $row): ?>
                        <tr>
                            <?php foreach ($columns as $key => $label): ?>
                            <td>
                                <?php 
                                if ($key === 'gender') {
                                    echo '<span class="badge ' . ($row[$key] === 'أنثى' ? 'bg-pink' : 'bg-blue') . '">' . $row[$key] . '</span>';
                                } elseif (in_array($key, ['current_tech_usage', 'visual_effectiveness'])) {
                                    $badge_class = '';
                                    if ($row[$key] === 'ممتاز') $badge_class = 'bg-success';
                                    elseif ($row[$key] === 'جيد') $badge_class = 'bg-primary';
                                    elseif ($row[$key] === 'متوسط') $badge_class = 'bg-warning';
                                    else $badge_class = 'bg-danger';
                                    
                                    echo '<span class="badge ' . $badge_class . '">' . $row[$key] . '</span>';
                                } elseif ($key === 'created_at') {
                                    echo date('Y-m-d', strtotime($row[$key]));
                                } else {
                                    echo htmlspecialchars($row[$key] ?? '');
                                }
                                ?>
                            </td>
                            <?php endforeach; ?>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($table !== 'answers'): ?>
                                    <a href="manage.php?table=<?php echo $table; ?>&action=edit&id=<?php echo $row['id']; ?>" 
                                       class="btn-action btn-edit" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="#" 
                                       onclick="confirmDelete('<?php echo $table; ?>', <?php echo $row['id']; ?>)" 
                                       class="btn-action btn-delete" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    
                                    <a href="#" 
                                       onclick="viewDetails(<?php echo $row['id']; ?>)" 
                                       class="btn-action btn-view" title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <?php elseif (in_array($action, ['add', 'edit'])): ?>
        <!-- عرض النموذج -->
        <div class="form-container">
            <h3 class="mb-4">
                <?php echo $action === 'add' ? 'إضافة جديد' : 'تعديل بيانات'; ?>
            </h3>
            
            <form method="POST" action="">
                <?php if ($table === 'teachers'): ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">كود المعلم *</label>
                            <input type="text" name="teacher_code" class="form-control" 
                                   value="<?php echo $item['teacher_code'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">الجنس *</label>
                            <select name="gender" class="form-control" required>
                                <option value="">اختر الجنس</option>
                                <option value="ذكر" <?php echo ($item['gender'] ?? '') === 'ذكر' ? 'selected' : ''; ?>>ذكر</option>
                                <option value="أنثى" <?php echo ($item['gender'] ?? '') === 'أنثى' ? 'selected' : ''; ?>>أنثى</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">سنوات الخبرة *</label>
                            <input type="number" name="experience_years" class="form-control" min="0" max="50"
                                   value="<?php echo $item['experience_years'] ?? ''; ?>" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">الدرجة العلمية</label>
                            <input type="text" name="education_level" class="form-control"
                                   value="<?php echo $item['education_level'] ?? ''; ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">التخصص *</label>
                    <input type="text" name="specialization" class="form-control"
                           value="<?php echo $item['specialization'] ?? ''; ?>" required>
                </div>
                
                <?php elseif ($table === 'surveys'): ?>
                <div class="form-group">
                    <label class="form-label">المعلم *</label>
                    <select name="teacher_id" class="form-control" required>
                        <option value="">اختر المعلم</option>
                        <?php foreach ($teachers_list as $teacher): ?>
                        <option value="<?php echo $teacher['id']; ?>"
                            <?php echo ($item['teacher_id'] ?? '') == $teacher['id'] ? 'selected' : ''; ?>>
                            <?php echo $teacher['teacher_code'] . ' - ' . $teacher['specialization']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">استخدام التقنية الحالي</label>
                            <select name="current_tech_usage" class="form-control">
                                <option value="">اختر المستوى</option>
                                <option value="ممتاز" <?php echo ($item['current_tech_usage'] ?? '') === 'ممتاز' ? 'selected' : ''; ?>>ممتاز</option>
                                <option value="جيد" <?php echo ($item['current_tech_usage'] ?? '') === 'جيد' ? 'selected' : ''; ?>>جيد</option>
                                <option value="متوسط" <?php echo ($item['current_tech_usage'] ?? '') === 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                                <option value="ضعيف" <?php echo ($item['current_tech_usage'] ?? '') === 'ضعيف' ? 'selected' : ''; ?>>ضعيف</option>
                                <option value="ضعيف جداً" <?php echo ($item['current_tech_usage'] ?? '') === 'ضعيف جداً' ? 'selected' : ''; ?>>ضعيف جداً</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">معرفة تقنيات التحويل</label>
                            <select name="animation_knowledge" class="form-control">
                                <option value="">اختر المستوى</option>
                                <option value="جيد" <?php echo ($item['animation_knowledge'] ?? '') === 'جيد' ? 'selected' : ''; ?>>جيد</option>
                                <option value="متوسط" <?php echo ($item['animation_knowledge'] ?? '') === 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                                <option value="ضعيف" <?php echo ($item['animation_knowledge'] ?? '') === 'ضعيف' ? 'selected' : ''; ?>>ضعيف</option>
                                <option value="ضعيف جداً" <?php echo ($item['animation_knowledge'] ?? '') === 'ضعيف جداً' ? 'selected' : ''; ?>>ضعيف جداً</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">فعالية الوسائل البصرية</label>
                            <select name="visual_effectiveness" class="form-control">
                                <option value="">اختر المستوى</option>
                                <option value="ممتاز" <?php echo ($item['visual_effectiveness'] ?? '') === 'ممتاز' ? 'selected' : ''; ?>>ممتاز</option>
                                <option value="جيد" <?php echo ($item['visual_effectiveness'] ?? '') === 'جيد' ? 'selected' : ''; ?>>جيد</option>
                                <option value="متوسط" <?php echo ($item['visual_effectiveness'] ?? '') === 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                                <option value="ضعيف" <?php echo ($item['visual_effectiveness'] ?? '') === 'ضعيف' ? 'selected' : ''; ?>>ضعيف</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ملاحظات إضافية</label>
                    <textarea name="comments" class="form-control" rows="4"><?php echo $item['comments'] ?? ''; ?></textarea>
                </div>
                <?php endif; ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <a href="manage.php?table=<?php echo $table; ?>&action=list" class="btn-admin btn-admin-warning">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                    
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save"></i> 
                        <?php echo $action === 'add' ? 'حفظ' : 'تحديث'; ?>
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
    
    <script>
    function confirmDelete(table, id) {
        if (confirm('هل أنت متأكد من حذف هذا العنصر؟')) {
            window.location.href = `manage.php?table=${table}&action=delete&id=${id}`;
        }
    }
    
    function viewDetails(id) {
        // يمكن تنفيذ عرض التفاصيل في مودال
        alert(`عرض تفاصيل العنصر رقم ${id}\nهذه الميزة قيد التطوير`);
    }
    </script>
</body>
</html>