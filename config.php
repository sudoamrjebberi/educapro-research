<?php
// إعدادات قاعدة البيانات لـ InfinityFree
session_start();

// معلومات InfinityFree (ستغيرها بعد النشر)
define('DB_HOST', 'sql211.infinityfree.com');
define('DB_USER', 'if0_40988159');
define('DB_PASS', 'qkldzR3Buv8P');
define('DB_NAME', 'if0_40988159_researcheduca');

// إعدادات الموقع
define('SITE_URL', 'https://yourdomain.epizy.com');
define('SITE_NAME', 'بحث تربوي: تقنية تحويل النصوص إلى صور متحركة');

// وظائف مساعدة
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

function sanitize($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function jsonResponse($data) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit();
}

// فحص الاتصال عند التحميل
try {
    $test_conn = connectDB();
    $test_conn->close();
} catch (Exception $e) {
    error_log("Database connection error: " . $e->getMessage());
}
?>