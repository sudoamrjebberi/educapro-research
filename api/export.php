<?php
require_once '../config.php';

$format = isset($_GET['format']) ? sanitize($_GET['format']) : 'json';

$conn = connectDB();

$sql = "
    SELECT 
        t.teacher_code,
        t.gender,
        t.experience_years,
        t.education_level,
        t.specialization,
        s.current_tech_usage,
        s.animation_knowledge,
        s.visual_effectiveness,
        a.question_category,
        a.question_text,
        a.answer_value
    FROM teachers t
    LEFT JOIN surveys s ON t.id = s.teacher_id
    LEFT JOIN answers a ON s.id = a.survey_id
    ORDER BY t.teacher_code, a.question_category
";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$conn->close();

switch ($format) {
    case 'json':
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="research_data.json"');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;
        
    case 'csv':
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="research_data.csv"');
        
        $output = fopen('php://output', 'w');
        
        // كتابة العنوان
        fputcsv($output, [
            'كود المعلم', 'الجنس', 'سنوات الخبرة', 'الدرجة التعليمية', 'التخصص',
            'استخدام التقنية', 'معرفة التحويل', 'فعالية الوسائل البصرية',
            'فئة السؤال', 'نص السؤال', 'الإجابة'
        ]);
        
        // كتابة البيانات
        foreach ($data as $row) {
            fputcsv($output, [
                $row['teacher_code'],
                $row['gender'],
                $row['experience_years'],
                $row['education_level'],
                $row['specialization'],
                $row['current_tech_usage'],
                $row['animation_knowledge'],
                $row['visual_effectiveness'],
                $row['question_category'],
                $row['question_text'],
                $row['answer_value']
            ]);
        }
        
        fclose($output);
        break;
        
    case 'pdf':
        // يمكن إضافة مكتبة TCPDF أو FPDF هنا
        // للتبسيط، سنقوم بإرجاع JSON في الوقت الحالي
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'ميزة PDF تحت التطوير. يمكنك استخدام JSON أو CSV.',
            'data_count' => count($data)
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    default:
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => false,
            'error' => 'تنسيق غير مدعوم. الرجاء استخدام json أو csv أو pdf.'
        ], JSON_UNESCAPED_UNICODE);
}
?>