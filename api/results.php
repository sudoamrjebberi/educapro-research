<?php
require_once '../config.php';

header('Content-Type: application/json; charset=utf-8');

// الحصول على الفئة المطلوبة
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';
$conn = connectDB();

// بناء الاستعلام حسب الفئة
$sql = "
    SELECT 
        a.question_category,
        a.question_text,
        a.answer_value,
        COUNT(*) as count,
        ROUND((COUNT(*) * 100.0 / (
            SELECT COUNT(DISTINCT survey_id) 
            FROM answers 
            WHERE question_category = a.question_category
        )), 1) as percentage
    FROM answers a
    WHERE 1=1
";

if ($category !== 'all') {
    $sql .= " AND a.question_category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $category);
} else {
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];
$categories = [];

while ($row = $result->fetch_assoc()) {
    $cat = $row['question_category'];
    if (!isset($categories[$cat])) {
        $categories[$cat] = [
            'category' => $cat,
            'questions' => []
        ];
    }
    
    $question = $row['question_text'];
    if (!isset($categories[$cat]['questions'][$question])) {
        $categories[$cat]['questions'][$question] = [
            'text' => $question,
            'answers' => []
        ];
    }
    
    $categories[$cat]['questions'][$question]['answers'][] = [
        'value' => $row['answer_value'],
        'count' => $row['count'],
        'percentage' => $row['percentage']
    ];
}

$stmt->close();
$conn->close();

echo json_encode([
    'success' => true,
    'category' => $category,
    'data' => array_values($categories)
], JSON_UNESCAPED_UNICODE);
?>