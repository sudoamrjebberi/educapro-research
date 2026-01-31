<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

$type = isset($_GET['type']) ? sanitize($_GET['type']) : 'gender';
$conn = connectDB();

$analysis = [];

try {
    switch ($type) {
        case 'gender':
            // تحليل حسب الجنس
            $sql = "SELECT gender, COUNT(*) as total FROM teachers GROUP BY gender";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $analysis[] = [
                    'category' => 'الجنس',
                    'value' => $row['gender'],
                    'count' => (int)$row['total'],
                    'percentage' => round(($row['total'] / 37) * 100, 1) // 37 هو العدد الكلي
                ];
            }
            break;
            
        case 'experience':
            // تحليل حسب الخبرة
            $sql = "SELECT 
                        CASE 
                            WHEN experience_years < 5 THEN 'أقل من 5 سنوات'
                            WHEN experience_years BETWEEN 5 AND 10 THEN '5-10 سنوات'
                            ELSE 'أكثر من 10 سنوات'
                        END as experience_range,
                        COUNT(*) as total
                    FROM teachers 
                    GROUP BY experience_range";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $analysis[] = [
                    'category' => 'الخبرة',
                    'value' => $row['experience_range'],
                    'count' => (int)$row['total'],
                    'percentage' => round(($row['total'] / 37) * 100, 1)
                ];
            }
            break;
            
        case 'specialization':
            // تحليل حسب التخصص
            $sql = "SELECT specialization, COUNT(*) as total 
                    FROM teachers 
                    WHERE specialization IS NOT NULL 
                    GROUP BY specialization 
                    ORDER BY total DESC 
                    LIMIT 10";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $analysis[] = [
                    'category' => 'التخصص',
                    'value' => $row['specialization'],
                    'count' => (int)$row['total'],
                    'percentage' => round(($row['total'] / 37) * 100, 1)
                ];
            }
            break;
    }
    
    echo json_encode([
        'success' => true,
        'type' => $type,
        'data' => $analysis,
        'message' => 'تم تحميل بيانات التحليل بنجاح'
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'خطأ في تحميل التحليل: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>