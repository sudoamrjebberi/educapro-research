<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$conn = connectDB();

// التحقق من الاتصال
if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error' => 'فشل الاتصال بقاعدة البيانات'
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

// الحصول على معاملات البحث
$query = isset($_GET['q']) ? sanitize($_GET['q']) : '';
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$offset = ($page - 1) * $limit;

// التحقق من وجود كلمة بحث
if (empty($query) || strlen($query) < 2) {
    echo json_encode([
        'success' => false,
        'error' => 'يرجى إدخال كلمة بحث مكونة من حرفين على الأقل',
        'query' => $query
    ], JSON_UNESCAPED_UNICODE);
    exit();
}

$results = [];
$total = 0;

try {
    // البحث في المعلمين
    $teacher_sql = "
        SELECT 
            t.id,
            t.teacher_code,
            t.gender,
            t.experience_years,
            t.education_level,
            t.specialization,
            'teacher' as type,
            CONCAT('معلم/ة - ', t.specialization) as description
        FROM teachers t
        WHERE t.teacher_code LIKE ? 
           OR t.specialization LIKE ? 
           OR t.education_level LIKE ?
        LIMIT ? OFFSET ?
    ";
    
    $search_term = "%$query%";
    $stmt = $conn->prepare($teacher_sql);
    $stmt->bind_param('sssii', $search_term, $search_term, $search_term, $limit, $offset);
    $stmt->execute();
    $teacher_result = $stmt->get_result();
    
    while ($row = $teacher_result->fetch_assoc()) {
        $row['link'] = "../index.php#data";
        $results[] = $row;
    }
    $stmt->close();

    // البحث في الاستبيانات (إذا كانت نتائج المعلمين قليلة)
    if (count($results) < 5) {
        $survey_sql = "
            SELECT 
                s.id,
                t.teacher_code,
                s.current_tech_usage,
                s.animation_knowledge,
                s.visual_effectiveness,
                'survey' as type,
                CONCAT('استبيان - ', t.teacher_code) as description
            FROM surveys s
            JOIN teachers t ON s.teacher_id = t.id
            WHERE s.current_tech_usage LIKE ? 
               OR s.animation_knowledge LIKE ? 
               OR s.visual_effectiveness LIKE ?
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($survey_sql);
        $stmt->bind_param('sssii', $search_term, $search_term, $search_term, $limit, $offset);
        $stmt->execute();
        $survey_result = $stmt->get_result();
        
        while ($row = $survey_result->fetch_assoc()) {
            $row['link'] = "../index.php#data";
            $results[] = $row;
        }
        $stmt->close();
    }

    // البحث في الأسئلة والإجابات (إذا كانت النتائج قليلة)
    if (count($results) < 5 && $category !== 'teachers') {
        $answer_sql = "
            SELECT 
                a.id,
                a.question_text,
                a.answer_value,
                a.question_category,
                t.teacher_code,
                'answer' as type,
                CONCAT('إجابة - ', LEFT(a.question_text, 50), '...') as description
            FROM answers a
            JOIN surveys s ON a.survey_id = s.id
            JOIN teachers t ON s.teacher_id = t.id
            WHERE a.question_text LIKE ? 
               OR a.answer_value LIKE ?
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($answer_sql);
        $stmt->bind_param('ssii', $search_term, $search_term, $limit, $offset);
        $stmt->execute();
        $answer_result = $stmt->get_result();
        
        while ($row = $answer_result->fetch_assoc()) {
            $row['link'] = "../index.php#results";
            $results[] = $row;
        }
        $stmt->close();
    }

    // حساب العدد الإجمالي
    $count_sql = "
        SELECT (
            (SELECT COUNT(*) FROM teachers 
             WHERE teacher_code LIKE ? OR specialization LIKE ? OR education_level LIKE ?) +
            (SELECT COUNT(*) FROM surveys s 
             JOIN teachers t ON s.teacher_id = t.id
             WHERE s.current_tech_usage LIKE ? OR s.animation_knowledge LIKE ? OR s.visual_effectiveness LIKE ?) +
            (SELECT COUNT(*) FROM answers a
             JOIN surveys s ON a.survey_id = s.id
             JOIN teachers t ON s.teacher_id = t.id
             WHERE a.question_text LIKE ? OR a.answer_value LIKE ?)
        ) as total
    ";
    
    $stmt = $conn->prepare($count_sql);
    $stmt->bind_param('ssssssss', 
        $search_term, $search_term, $search_term,
        $search_term, $search_term, $search_term,
        $search_term, $search_term
    );
    $stmt->execute();
    $count_result = $stmt->get_result();
    
    if ($row = $count_result->fetch_assoc()) {
        $total = (int)$row['total'];
    }
    $stmt->close();

    $conn->close();

    // تجهيز النتائج للإرجاع
    $total_pages = ceil($total / $limit);
    
    echo json_encode([
        'success' => true,
        'query' => $query,
        'page' => $page,
        'total' => $total,
        'total_pages' => $total_pages,
        'results_count' => count($results),
        'results' => $results,
        'pagination' => [
            'current' => $page,
            'total' => $total_pages,
            'has_prev' => $page > 1,
            'has_next' => $page < $total_pages
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    if ($conn) {
        $conn->close();
    }
    
    echo json_encode([
        'success' => false,
        'error' => 'خطأ في البحث: ' . $e->getMessage(),
        'query' => $query
    ], JSON_UNESCAPED_UNICODE);
}
?>