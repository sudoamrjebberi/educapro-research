<?php
require_once 'config.php';

echo "<h3>🕐 اختبار إعدادات الوقت - تونس</h3>";
echo "الوقت الحالي بتوقيت تونس: " . getTunisTime() . "<br>";
echo "التاريخ العربي: " . formatArabicDate(getTunisTime()) . "<br>";
echo "نطاق الوقت: " . date_default_timezone_get() . "<br>";

// اختبار مع قاعدة البيانات
try {
    $conn = connectDB();
    $result = $conn->query("SELECT NOW() as db_time, @@global.time_zone as db_timezone");
    $row = $result->fetch_assoc();
    echo "<br>⏱️ وقت قاعدة البيانات: " . $row['db_time'] . "<br>";
    echo "نطاق وقت قاعدة البيانات: " . $row['db_timezone'] . "<br>";
    $conn->close();
} catch (Exception $e) {
    echo "<br>❌ خطأ في قاعدة البيانات: " . $e->getMessage();
}
?>