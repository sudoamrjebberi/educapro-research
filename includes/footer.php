<?php
// ملف الفوتر المشترك
?>
    </main>
    
    <?php if (!isset($no_footer) || !$no_footer): ?>
    <!-- تذييل الصفحة -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">
                        <i class="bi bi-journal-bookmark-fill"></i>
                        بحث تربوي
                    </h5>
                    <p>بحث تربوي متكامل حول استخدام تقنية تحويل النصوص إلى صور متحركة في التعليم.</p>
                </div>
                
                <div class="col-md-6 text-md-end">
                    <h5 class="mb-3">روابط سريعة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="../index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> الرئيسية
                            </a>
                        </li>
                        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <li class="mb-2">
                            <a href="dashboard.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> لوحة التحكم
                            </a>
                        </li>
                        <?php else: ?>
                        <li class="mb-2">
                            <a href="login.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> تسجيل الدخول
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="bg-white">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">© 2024 بحث تربوي. جميع الحقوق محفوظة.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">
                        تطوير بواسطة 
                        <span class="text-warning">فريق البحث التربوي</span>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if (isset($additional_js)): ?>
    <?php foreach ($additional_js as $js): ?>
    <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
    <?php endif; ?>
    
    <?php if (isset($custom_script)): ?>
    <script>
    <?php echo $custom_script; ?>
    </script>
    <?php endif; ?>
</body>
</html>