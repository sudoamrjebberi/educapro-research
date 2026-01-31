<?php
session_start();

// التحقق مما إذا كان المستخدم مسجلاً بالفعل
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // إذا لم يكن مسجلاً، توجهه إلى صفحة تسجيل الدخول
    header('Location: login.php');
    exit();
}

// تسجيل النشاط (إذا كان هناك نظام تسجيل الأنشطة)
// logAdminActivity('logout', 'تسجيل الخروج من النظام');

// تدمير جميع بيانات الجلسة
$_SESSION = array();

// إذا كنت تريد تدمير الجلسة تماماً، فقم أيضاً بحذف كوكيز الجلسة
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// عرض صفحة تأكيد تسجيل الخروج
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الخروج - لوحة التحكم</title>
    <link href="../assets/css/admin.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .logout-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .logout-icon {
            font-size: 64px;
            color: #3498db;
            margin-bottom: 20px;
        }
        
        .logout-title {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: 700;
        }
        
        .logout-message {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .logout-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn-logout {
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            border: none;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
            color: white;
        }
        
        .btn-home {
            background: transparent;
            color: #3498db;
            border: 2px solid #3498db;
        }
        
        .btn-home:hover {
            background: #3498db;
            color: white;
            transform: translateY(-3px);
        }
        
        .countdown {
            font-size: 14px;
            color: #7f8c8d;
            margin-top: 20px;
        }
        
        .countdown-number {
            font-weight: bold;
            color: #e74c3c;
        }
    </style>
</head>
<body class="login-page">
    <div class="logout-container">
        <div class="logout-icon">
            <i class="fas fa-sign-out-alt"></i>
        </div>
        
        <h2 class="logout-title">تم تسجيل الخروج بنجاح</h2>
        
        <p class="logout-message">
            لقد تم تسجيل خروجك من لوحة التحكم بنجاح.<br>
            يمكنك تسجيل الدخول مرة أخرى للوصول إلى لوحة التحكم.
        </p>
        
        <div class="logout-buttons">
            <a href="login.php" class="btn-logout btn-login">
                <i class="fas fa-sign-in-alt"></i>
                تسجيل الدخول مرة أخرى
            </a>
            
            <a href="../index.php" class="btn-logout btn-home">
                <i class="fas fa-home"></i>
                العودة للصفحة الرئيسية
            </a>
        </div>
        
        <div class="countdown" id="countdownTimer">
            سيتم توجيهك تلقائياً إلى صفحة تسجيل الدخول خلال <span class="countdown-number" id="countdown">10</span> ثوانٍ
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        const countdownTimer = document.getElementById('countdownTimer');
        
        const timer = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'login.php';
            }
            
            // تغيير اللون عندما يقترب العد من الصفر
            if (countdown <= 3) {
                countdownElement.style.color = '#e74c3c';
            }
        }, 1000);
        
        // إمكانية إلغاء التوجيه التلقائي
        const cancelRedirect = function() {
            clearInterval(timer);
            countdownTimer.innerHTML = '<i class="fas fa-check-circle text-success"></i> تم إلغاء التوجيه التلقائي';
            countdownTimer.style.color = '#27ae60';
        };
        
        // إلغاء التوجيه عند النقر على أي زر
        document.querySelectorAll('.btn-logout').forEach(btn => {
            btn.addEventListener('click', cancelRedirect);
        });
    });
    </script>
</body>
</html>