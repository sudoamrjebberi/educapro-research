-- في بداية ملف database.sql
SET time_zone = '+01:00'; -- توقيت تونس (UTC+1)

-- في جدول surveys، أضف عمود التوقيت التونسي
ALTER TABLE surveys ADD COLUMN tunis_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

-- أو عند إنشاء الجدول:
CREATE TABLE IF NOT EXISTS surveys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    current_tech_usage ENUM('ممتاز', 'جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'),
    animation_knowledge ENUM('جيد', 'متوسط', 'ضعيف', 'ضعيف جداً'),
    visual_effectiveness ENUM('ممتاز', 'جيد', 'متوسط', 'ضعيف'),
    comments TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    tunis_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id) ON DELETE CASCADE
);

-- دالة لحساب الوقت التونسي
CREATE FUNCTION get_tunis_time() 
RETURNS TIMESTAMP
DETERMINISTIC
RETURN CONVERT_TZ(NOW(), '+00:00', '+01:00');