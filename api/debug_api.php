<?php
// debug_api.php - لفحص أخطاء PHP
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🔍 اختبار إعدادات PHP وقاعدة البيانات<br><hr>";

// 1. اختبار اتصال PHP الأساسي
echo "✅ PHP يعمل (الإصدار: " . phpversion() . ")<br>";

// 2. اختبار اتصال قاعدة البيانات مباشرة
$host = 'sql211.infinityfree.com';
$user = 'if0_40988159';
$pass = 'qkldzR3Buv8P';
$db = 'if0_40988159_researcheduca';

try {
    $conn = new mysqli($host, $user, $pass, $db);
    
    if ($conn->connect_error) {
        echo "❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error . "<br>";
    } else {
        echo "✅ تم الاتصال بقاعدة البيانات بنجاح<br>";
        
        // عرض الجداول
        $result = $conn->query("SHOW TABLES");
        echo "📊 عدد الجداول: " . $result->num_rows . "<br>";
        
        if ($result->num_rows > 0) {
            echo "قائمة الجداول:<br>";
            while($row = $result->fetch_array()) {
                echo "- " . $row[0] . "<br>";
            }
        } else {
            echo "⚠️ لا توجد جداول في قاعدة البيانات!<br>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "❌ استثناء: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// 3. اختبار إعدادات الموقع
echo "📁 المسار الحالي: " . __DIR__ . "<br>";
echo "🔗 ملف config.php موجود: " . (file_exists('../config.php') ? 'نعم' : 'لا') . "<br>";
?>