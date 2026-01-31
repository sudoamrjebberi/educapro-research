<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$conn = connectDB();

// التحقق من الاتصال
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => 'فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// جلب الإحصائيات الرئيسية
$stats = [
    'total_teachers' => 0,
    'total_surveys' => 0,
    'participation_rate' => 0,
    'gender_distribution' => [],
    'experience_distribution' => [],
    'tech_usage' => [],
    'knowledge_distribution' => [],
    'effectiveness_distribution' => [],
    'positive_results' => [],
    'challenges' => [],
    'concerns' => [],
    'timestamp' => date('Y-m-d H:i:s')
];

try {
    // 1. إجمالي المعلمين
    $sql = "SELECT COUNT(*) as total FROM teachers";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_teachers'] = (int)$row['total'];
    }

    // 2. إجمالي الاستبيانات
    $sql = "SELECT COUNT(*) as total FROM surveys";
    $result = $conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats['total_surveys'] = (int)$row['total'];
    }

    // 3. نسبة المشاركة
    if ($stats['total_teachers'] > 0) {
        $stats['participation_rate'] = round(($stats['total_surveys'] / $stats['total_teachers']) * 100, 1);
    }

    // 4. توزيع الجنس
    $sql = "SELECT gender, COUNT(*) as count FROM teachers GROUP BY gender";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $percentage = $stats['total_teachers'] > 0 ? 
                round(($row['count'] / $stats['total_teachers']) * 100, 1) : 0;
            
            $stats['gender_distribution'][] = [
                'gender' => $row['gender'],
                'count' => (int)$row['count'],
                'percentage' => $percentage
            ];
        }
    }

    // 5. توزيع الخبرة
    $sql = "
        SELECT 
            CASE 
                WHEN experience_years < 5 THEN 'أقل من 5 سنوات'
                WHEN experience_years BETWEEN 5 AND 10 THEN '5-10 سنوات'
                ELSE 'أكثر من 10 سنوات'
            END as experience_range,
            COUNT(*) as count
        FROM teachers 
        GROUP BY experience_range
    ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $percentage = $stats['total_teachers'] > 0 ? 
                round(($row['count'] / $stats['total_teachers']) * 100, 1) : 0;
            
            $stats['experience_distribution'][] = [
                'range' => $row['experience_range'],
                'count' => (int)$row['count'],
                'percentage' => $percentage
            ];
        }
    }

    // 6. استخدام التقنية
    $sql = "
        SELECT current_tech_usage, COUNT(*) as count 
        FROM surveys 
        WHERE current_tech_usage IS NOT NULL 
        GROUP BY current_tech_usage
    ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $percentage = $stats['total_surveys'] > 0 ? 
                round(($row['count'] / $stats['total_surveys']) * 100, 1) : 0;
            
            $stats['tech_usage'][] = [
                'level' => $row['current_tech_usage'],
                'count' => (int)$row['count'],
                'percentage' => $percentage
            ];
        }
    }

    // 7. المعرفة بالتقنية
    $sql = "
        SELECT animation_knowledge, COUNT(*) as count 
        FROM surveys 
        WHERE animation_knowledge IS NOT NULL 
        GROUP BY animation_knowledge
    ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $percentage = $stats['total_surveys'] > 0 ? 
                round(($row['count'] / $stats['total_surveys']) * 100, 1) : 0;
            
            $stats['knowledge_distribution'][] = [
                'level' => $row['animation_knowledge'],
                'count' => (int)$row['count'],
                'percentage' => $percentage
            ];
        }
    }

    // 8. فعالية الوسائل البصرية
    $sql = "
        SELECT visual_effectiveness, COUNT(*) as count 
        FROM surveys 
        WHERE visual_effectiveness IS NOT NULL 
        GROUP BY visual_effectiveness
    ";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $percentage = $stats['total_surveys'] > 0 ? 
                round(($row['count'] / $stats['total_surveys']) * 100, 1) : 0;
            
            $stats['effectiveness_distribution'][] = [
                'level' => $row['visual_effectiveness'],
                'count' => (int)$row['count'],
                'percentage' => $percentage
            ];
        }
    }

    // 9. النتائج الإيجابية (من بيانات الـ answers)
    $sql = "
        SELECT answer_value, COUNT(*) as count 
        FROM answers 
        WHERE question_category = 'positive' 
        GROUP BY answer_value
    ";
    $result = $conn->query($sql);
    if ($result) {
        $total_positive = 0;
        $positive_counts = [];
        
        while ($row = $result->fetch_assoc()) {
            $total_positive += $row['count'];
            $positive_counts[$row['answer_value']] = (int)$row['count'];
        }
        
        foreach ($positive_counts as $answer => $count) {
            $percentage = $total_positive > 0 ? round(($count / $total_positive) * 100, 1) : 0;
            
            $stats['positive_results'][] = [
                'answer' => $answer,
                'count' => $count,
                'percentage' => $percentage
            ];
        }
    }

    // 10. التحديات
    $sql = "
        SELECT answer_value, COUNT(*) as count 
        FROM answers 
        WHERE question_category = 'challenges' 
        GROUP BY answer_value
    ";
    $result = $conn->query($sql);
    if ($result) {
        $total_challenges = 0;
        $challenge_counts = [];
        
        while ($row = $result->fetch_assoc()) {
            $total_challenges += $row['count'];
            $challenge_counts[$row['answer_value']] = (int)$row['count'];
        }
        
        foreach ($challenge_counts as $answer => $count) {
            $percentage = $total_challenges > 0 ? round(($count / $total_challenges) * 100, 1) : 0;
            
            $stats['challenges'][] = [
                'challenge' => $answer,
                'count' => $count,
                'percentage' => $percentage
            ];
        }
    }

    // 11. المخاوف
    $sql = "
        SELECT answer_value, COUNT(*) as count 
        FROM answers 
        WHERE question_category = 'concerns' 
        GROUP BY answer_value
    ";
    $result = $conn->query($sql);
    if ($result) {
        $total_concerns = 0;
        $concern_counts = [];
        
        while ($row = $result->fetch_assoc()) {
            $total_concerns += $row['count'];
            $concern_counts[$row['answer_value']] = (int)$row['count'];
        }
        
        foreach ($concern_counts as $answer => $count) {
            $percentage = $total_concerns > 0 ? round(($count / $total_concerns) * 100, 1) : 0;
            
            $stats['concerns'][] = [
                'concern' => $answer,
                'count' => $count,
                'percentage' => $percentage
            ];
        }
    }

    $conn->close();

    echo json_encode([
        'success' => true,
        'data' => $stats,
        'message' => 'تم جلب الإحصائيات بنجاح'
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($conn) {
        $conn->close();
    }
    
    echo json_encode([
        'success' => false,
        'error' => 'خطأ في جلب الإحصائيات: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}
?>