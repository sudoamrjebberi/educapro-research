<?php
// ملف الشريط الجانبي للوحة التحكم
?>
<!-- الشريط الجانبي -->
<div class="sidebar">
    <div class="sidebar-header">
        <a href="dashboard.php" class="logo">
            <i class="fas fa-chart-bar"></i>
            <span>بحث تربوي</span>
        </a>
        <div class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </div>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> الرئيسية
            </a>
        </li>
        
        <li class="menu-divider">
            <span>إدارة البيانات</span>
        </li>
        
        <li>
            <a href="manage.php?table=teachers&action=list" 
               class="<?php echo isset($_GET['table']) && $_GET['table'] == 'teachers' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> المعلمين
            </a>
        </li>
        
        <li>
            <a href="manage.php?table=surveys&action=list" 
               class="<?php echo isset($_GET['table']) && $_GET['table'] == 'surveys' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i> الاستبيانات
            </a>
        </li>
        
        <li>
            <a href="manage.php?table=answers&action=list" 
               class="<?php echo isset($_GET['table']) && $_GET['table'] == 'answers' ? 'active' : ''; ?>">
                <i class="fas fa-comments"></i> الإجابات
            </a>
        </li>
        
        <li class="menu-divider">
            <span>التقارير والإحصائيات</span>
        </li>
        
        <li>
            <a href="reports.php">
                <i class="fas fa-chart-pie"></i> التقارير
            </a>
        </li>
        
        <li>
            <a href="analytics.php">
                <i class="fas fa-chart-line"></i> التحليلات
            </a>
        </li>
        
        <li>
            <a href="export.php">
                <i class="fas fa-download"></i> التصدير
            </a>
        </li>
        
        <li class="menu-divider">
            <span>الإعدادات</span>
        </li>
        
        <li>
            <a href="settings.php">
                <i class="fas fa-cog"></i> الإعدادات
            </a>
        </li>
        
        <li>
            <a href="profile.php">
                <i class="fas fa-user"></i> الملف الشخصي
            </a>
        </li>
        
        <li>
            <a href="logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <div class="avatar">
                <?php 
                $username = $_SESSION['admin_username'] ?? 'Admin';
                echo strtoupper(substr($username, 0, 1)); 
                ?>
            </div>
            <div>
                <div class="user-name"><?php echo htmlspecialchars($username); ?></div>
                <small class="user-role">مدير النظام</small>
            </div>
        </div>
        
        <div class="system-info">
            <div class="info-item">
                <i class="fas fa-database"></i>
                <span>قاعدة البيانات: نشطة</span>
            </div>
            <div class="info-item">
                <i class="fas fa-server"></i>
                <span>الإصدار: 1.0.0</span>
            </div>
        </div>
    </div>
</div>

<!-- زر تبديل الشريط الجانبي للجوال -->
<button class="sidebar-toggle-mobile" id="sidebarToggleMobile">
    <i class="fas fa-bars"></i>
</button>

<style>
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
    color: white;
    position: fixed;
    top: 0;
    right: 0;
    height: 100vh;
    z-index: 1000;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    overflow-y: auto;
}

.sidebar-header {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-header .logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.2rem;
    font-weight: 700;
    color: white;
    text-decoration: none;
}

.sidebar-header .logo i {
    font-size: 1.5rem;
    color: #3498db;
}

.sidebar-toggle {
    display: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
}

.sidebar-menu {
    list-style: none;
    padding: 20px 0;
    margin: 0;
}

.sidebar-menu li {
    margin: 0;
}

.sidebar-menu a {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    transition: all 0.3s ease;
    border-right: 3px solid transparent;
    font-size: 0.95rem;
}

.sidebar-menu a:hover,
.sidebar-menu a.active {
    background: rgba(255,255,255,0.1);
    color: white;
    border-right-color: #3498db;
}

.sidebar-menu a i {
    margin-left: 10px;
    width: 20px;
    text-align: center;
    font-size: 1.1rem;
}

.menu-divider {
    padding: 10px 20px;
    margin-top: 10px;
    color: rgba(255,255,255,0.5);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.menu-divider:first-child {
    border-top: none;
    margin-top: 0;
}

.logout-link {
    color: #e74c3c !important;
}

.logout-link:hover {
    background: rgba(231, 76, 60, 0.1) !important;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    width: 100%;
    padding: 20px;
    background: rgba(0,0,0,0.2);
    border-top: 1px solid rgba(255,255,255,0.1);
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.user-info .avatar {
    width: 40px;
    height: 40px;
    background: #3498db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.user-info .user-name {
    font-weight: 600;
    font-size: 0.95rem;
}

.user-info .user-role {
    color: rgba(255,255,255,0.6);
    font-size: 0.8rem;
}

.system-info {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.sidebar-toggle-mobile {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1001;
    width: 50px;
    height: 50px;
    background: #3498db;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 1.2rem;
    cursor: pointer;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

.sidebar-toggle-mobile:hover {
    background: #2980b9;
    transform: scale(1.1);
}

@media (max-width: 992px) {
    .sidebar {
        transform: translateX(100%);
    }
    
    .sidebar.show {
        transform: translateX(0);
    }
    
    .sidebar-toggle {
        display: block;
    }
    
    .sidebar-toggle-mobile {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .main-content {
        margin-right: 0 !important;
        padding-right: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarToggleMobile = document.getElementById('sidebarToggleMobile');
    
    function toggleSidebar() {
        sidebar.classList.toggle('show');
    }
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', toggleSidebar);
    }
    
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', toggleSidebar);
    }
    
    // إغلاق الشريط الجانبي عند النقر خارجيه (للجوال)
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 992) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggle = (sidebarToggle && sidebarToggle.contains(event.target)) || 
                                   (sidebarToggleMobile && sidebarToggleMobile.contains(event.target));
            
            if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        }
    });
    
    // تحديث العرض عند تغيير حجم النافذة
    window.addEventListener('resize', function() {
        if (window.innerWidth > 992) {
            sidebar.classList.remove('show');
        }
    });
});
</script>