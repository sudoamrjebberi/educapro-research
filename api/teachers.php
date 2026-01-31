<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config.php';

// بيانات معلمين تجريبية
$teachers = [
    'success' => true,
    'page' => 1,
    'total' => 10,
    'data' => [
        [
            'teacher_code' => 'T001',
            'gender' => 'أنثى',
            'experience_years' => 8,
            'education_level' => 'ماجستير',
            'specialization' => 'اللغة العربية',
            'current_tech_usage' => 'جيد',
            'animation_knowledge' => 'متوسط',
            'visual_effectiveness' => 'جيد'
        ],
        [
            'teacher_code' => 'T002', 
            'gender' => 'أنثى',
            'experience_years' => 12,
            'education_level' => 'بكالوريوس',
            'specialization' => 'الرياضيات',
            'current_tech_usage' => 'متوسط',
            'animation_knowledge' => 'ضعيف',
            'visual_effectiveness' => 'متوسط'
        ],
        [
            'teacher_code' => 'T003',
            'gender' => 'ذكر',
            'experience_years' => 5,
            'education_level' => 'بكالوريوس',
            'specialization' => 'العلوم',
            'current_tech_usage' => 'ممتاز',
            'animation_knowledge' => 'جيد',
            'visual_effectiveness' => 'ممتاز'
        ]
    ],
    'pagination' => [
        'current' => 1,
        'total' => 1,
        'has_prev' => false,
        'has_next' => false
    ],
    'message' => 'بيانات تجريبية - سيتم تحميل البيانات الحقيقية قريباً'
];

echo json_encode($teachers, JSON_UNESCAPED_UNICODE);
?>