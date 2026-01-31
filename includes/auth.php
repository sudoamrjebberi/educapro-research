<?php
// ملف المصادقة المشترك

/**
 * التحقق من تسجيل دخول المسؤول
 */
function requireAdminLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // حفظ الصفحة الحالية للرجوع إليها بعد تسجيل الدخول
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        
        header('Location: login.php');
        exit();
    }
    
    // تحديث وقت آخر نشاط
    $_SESSION['last_activity'] = time();
}

/**
 * التحقق من صلاحية الجلسة (مهلة 30 دقيقة)
 */
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity'])) {
        $inactive = 1800; // 30 دقيقة بالثواني
        $session_life = time() - $_SESSION['last_activity'];
        
        if ($session_life > $inactive) {
            session_destroy();
            header('Location: login.php?expired=1');
            exit();
        }
    }
    $_SESSION['last_activity'] = time();
}

/**
 * التحقق من دور المسؤول
 */
function checkAdminRole($requiredRole = 'admin') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_role'])) {
        return false;
    }
    
    // ترتيب الأدوار (من الأدنى إلى الأعلى)
    $roles = [
        'viewer' => 1,
        'editor' => 2,
        'admin' => 3,
        'super_admin' => 4
    ];
    
    if (!isset($roles[$_SESSION['admin_role']]) || !isset($roles[$requiredRole])) {
        return false;
    }
    
    return $roles[$_SESSION['admin_role']] >= $roles[$requiredRole];
}

/**
 * تسجيل دخول المسؤول
 */
function adminLogin($username, $password) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات'];
    }
    
    try {
        $stmt = $conn->prepare("
            SELECT id, username, password_hash, full_name, role, status 
            FROM admins 
            WHERE username = ? AND status = 'active'
        ");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // التحقق من كلمة المرور
            if (password_verify($password, $admin['password_hash'])) {
                // بدء الجلسة إذا لم تكن بدأت
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_full_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                $_SESSION['last_activity'] = time();
                
                // تسجيل محاولة تسجيل الدخول الناجحة
                logAdminActivity('login_success', 'تسجيل دخول ناجح من IP: ' . $_SERVER['REMOTE_ADDR']);
                
                return ['success' => true, 'message' => 'تم تسجيل الدخول بنجاح'];
            } else {
                // تسجيل محاولة تسجيل الدخول الفاشلة
                logAdminActivity('login_failed', 'كلمة مرور خاطئة للمستخدم: ' . $username);
                
                return ['success' => false, 'message' => 'كلمة المرور غير صحيحة'];
            }
        } else {
            // تسجيل محاولة تسجيل الدخول الفاشلة
            logAdminActivity('login_failed', 'اسم مستخدم غير موجود: ' . $username);
            
            return ['success' => false, 'message' => 'اسم المستخدم غير موجود'];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'حدث خطأ أثناء تسجيل الدخول: ' . $e->getMessage()];
    }
}

/**
 * تسجيل خروج المسؤول
 */
function adminLogout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // تسجيل نشاط تسجيل الخروج
    if (isset($_SESSION['admin_username'])) {
        logAdminActivity('logout', 'تسجيل خروج المستخدم: ' . $_SESSION['admin_username']);
    }
    
    // تدمير جميع بيانات الجلسة
    $_SESSION = array();
    
    // حذف كوكيز الجلسة
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // تدمير الجلسة
    session_destroy();
}

/**
 * الحصول على معلومات المسؤول الحالي
 */
function getCurrentAdmin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
        return [
            'id' => $_SESSION['admin_id'] ?? null,
            'username' => $_SESSION['admin_username'] ?? '',
            'full_name' => $_SESSION['admin_full_name'] ?? '',
            'role' => $_SESSION['admin_role'] ?? 'viewer'
        ];
    }
    
    return null;
}

/**
 * التحقق من صلاحيات الوصول
 */
function hasPermission($permission) {
    $admin = getCurrentAdmin();
    
    if (!$admin) {
        return false;
    }
    
    // تعريف الصلاحيات حسب الدور
    $permissions = [
        'super_admin' => ['all'],
        'admin' => [
            'view_dashboard', 'manage_teachers', 'manage_surveys', 
            'view_reports', 'export_data', 'manage_settings'
        ],
        'editor' => [
            'view_dashboard', 'manage_teachers', 'manage_surveys', 'view_reports'
        ],
        'viewer' => ['view_dashboard', 'view_reports']
    ];
    
    if (!isset($permissions[$admin['role']])) {
        return false;
    }
    
    // إذا كان له صلاحية "all" فيمكنه كل شيء
    if (in_array('all', $permissions[$admin['role']])) {
        return true;
    }
    
    return in_array($permission, $permissions[$admin['role']]);
}

/**
 * تسجيل نشاط المسؤول
 */
function logAdminActivity($action, $details = null) {
    global $conn;
    
    if (!$conn) {
        return false;
    }
    
    try {
        $adminId = $_SESSION['admin_id'] ?? null;
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $pageUrl = $_SERVER['REQUEST_URI'] ?? '';
        
        $stmt = $conn->prepare("
            INSERT INTO admin_logs (admin_id, action, details, ip_address, user_agent, page_url) 
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param('isssss', $adminId, $action, $details, $ipAddress, $userAgent, $pageUrl);
        $stmt->execute();
        $stmt->close();
        
        return true;
        
    } catch (Exception $e) {
        error_log('خطأ في تسجيل نشاط المسؤول: ' . $e->getMessage());
        return false;
    }
}

/**
 * تغيير كلمة مرور المسؤول
 */
function changeAdminPassword($adminId, $currentPassword, $newPassword) {
    global $conn;
    
    if (!$conn) {
        return ['success' => false, 'message' => 'خطأ في الاتصال بقاعدة البيانات'];
    }
    
    try {
        // التحقق من كلمة المرور الحالية
        $stmt = $conn->prepare("SELECT password_hash FROM admins WHERE id = ?");
        $stmt->bind_param('i', $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            return ['success' => false, 'message' => 'المستخدم غير موجود'];
        }
        
        $admin = $result->fetch_assoc();
        
        if (!password_verify($currentPassword, $admin['password_hash'])) {
            return ['success' => false, 'message' => 'كلمة المرور الحالية غير صحيحة'];
        }
        
        // تحديث كلمة المرور الجديدة
        $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("UPDATE admins SET password_hash = ? WHERE id = ?");
        $stmt->bind_param('si', $newPasswordHash, $adminId);
        
        if ($stmt->execute()) {
            // تسجيل نشاط تغيير كلمة المرور
            logAdminActivity('password_change', 'تم تغيير كلمة المرور');
            
            return ['success' => true, 'message' => 'تم تغيير كلمة المرور بنجاح'];
        } else {
            return ['success' => false, 'message' => 'فشل تغيير كلمة المرور'];
        }
        
        $stmt->close();
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()];
    }
}

/**
 * التحقق من صلاحيات الوصول للصفحة
 */
function checkPageAccess($requiredPermission) {
    requireAdminLogin(); // التحقق من تسجيل الدخول أولاً
    checkSessionTimeout(); // التحقق من مهلة الجلسة
    
    if (!hasPermission($requiredPermission)) {
        header('Location: dashboard.php?error=access_denied');
        exit();
    }
}

/**
 * إنشاء توكن CSRF
 */
function generateCSRFToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    $_SESSION['csrf_token_time'] = time();
    
    return $token;
}

/**
 * التحقق من صحة توكن CSRF
 */
function verifyCSRFToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
        return false;
    }
    
    // مهلة التوكن (ساعة واحدة)
    if (time() - $_SESSION['csrf_token_time'] > 3600) {
        unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
        return false;
    }
    
    if ($_SESSION['csrf_token'] !== $token) {
        return false;
    }
    
    // حذف التوكن بعد استخدامه (استخدام لمرة واحدة)
    unset($_SESSION['csrf_token'], $_SESSION['csrf_token_time']);
    
    return true;
}

/**
 * تنظيف الإدخال لمنع هجمات XSS
 */
function cleanInput($input) {
    if (is_array($input)) {
        return array_map('cleanInput', $input);
    }
    
    $input = trim($input);
    $input = stripslashes($input);
    $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    
    return $input;
}

/**
 * تسجيل الأخطاء
 */
function logError($error, $severity = 'error') {
    $log_file = dirname(__DIR__) . '/logs/error.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $admin = getCurrentAdmin();
    $admin_username = $admin ? $admin['username'] : 'guest';
    
    $log_message = "[$timestamp] [$severity] [$ip] [$admin_username] $error" . PHP_EOL;
    
    // التأكد من وجود مجلد السجلات
    if (!file_exists(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    // كتابة السجل
    file_put_contents($log_file, $log_message, FILE_APPEND);
}
?>