<?php
// /api/index.php
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'status' => 'active',
    'message' => 'واجهات API جاهزة للعمل',
    'available_endpoints' => [
        '/api/statistics.php' => 'الإحصائيات العامة',
        '/api/teachers.php' => 'قائمة المعلمين',
        '/api/results.php' => 'نتائج البحث',
        '/api/export.php' => 'تصدير البيانات',
        '/api/search.php' => 'البحث'
    ],
    'timestamp' => date('Y-m-d H:i:s')
], JSON_UNESCAPED_UNICODE);
?>