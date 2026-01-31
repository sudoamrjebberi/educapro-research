<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// بيانات افتراضية مؤقتة للتجربة
$stats = [
    'success' => true,
    'data' => [
        'total_teachers' => 37,
        'total_surveys' => 37,
        'participation_rate' => 100,
        'gender_distribution' => [
            ['gender' => 'أنثى', 'count' => 35, 'percentage' => 94.6],
            ['gender' => 'ذكر', 'count' => 2, 'percentage' => 5.4]
        ],
        'tech_usage' => [
            ['level' => 'ممتاز', 'count' => 1, 'percentage' => 2.7],
            ['level' => 'جيد', 'count' => 7, 'percentage' => 18.9],
            ['level' => 'متوسط', 'count' => 17, 'percentage' => 45.9],
            ['level' => 'ضعيف', 'count' => 7, 'percentage' => 18.9],
            ['level' => 'ضعيف جداً', 'count' => 5, 'percentage' => 13.5]
        ],
        'knowledge_distribution' => [
            ['level' => 'جيد', 'count' => 6, 'percentage' => 16.2],
            ['level' => 'متوسط', 'count' => 11, 'percentage' => 29.7],
            ['level' => 'ضعيف', 'count' => 12, 'percentage' => 32.4],
            ['level' => 'ضعيف جداً', 'count' => 8, 'percentage' => 21.6]
        ],
        'effectiveness_distribution' => [
            ['level' => 'ممتاز', 'count' => 4, 'percentage' => 10.8],
            ['level' => 'جيد', 'count' => 12, 'percentage' => 32.4],
            ['level' => 'متوسط', 'count' => 16, 'percentage' => 43.2],
            ['level' => 'ضعيف', 'count' => 5, 'percentage' => 13.5]
        ]
    ],
    'message' => 'بيانات تجريبية - قاعدة البيانات قيد الإعداد'
];

echo json_encode($stats, JSON_UNESCAPED_UNICODE);
?>