<?php
// test_api.php
echo "<h3>🔧 اختبار وصول API</h3>";

$apis = [
    'statistics.php' => 'https://educapro.wuaze.com/api/statistics.php',
    'teachers.php' => 'https://educapro.wuaze.com/api/teachers.php',
    'results.php' => 'https://educapro.wuaze.com/api/results.php'
];

foreach ($apis as $name => $url) {
    echo "<h4>اختبار: $name</h4>";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "كود HTTP: <strong>$http_code</strong><br>";
    
    if ($http_code == 200) {
        echo "✅ نجح الاتصال<br>";
        $data = json_decode($response, true);
        if (isset($data['success'])) {
            echo "الرسالة: " . ($data['message'] ?? 'N/A') . "<br>";
        }
    } elseif ($http_code == 403) {
        echo "❌ خطأ 403 Forbidden - مشكلة في الصلاحيات<br>";
    } elseif ($http_code == 404) {
        echo "❌ خطأ 404 Not Found - الملف غير موجود<br>";
    } else {
        echo "❌ خطأ: $http_code<br>";
    }
    
    echo "<hr>";
    curl_close($ch);
}
?>