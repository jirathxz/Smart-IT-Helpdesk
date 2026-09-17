-- ==========================================================
-- Smart IT Helpdesk & Notification System
-- Database Schema & Initial Seed Data
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `smart_helpdesk` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `smart_helpdesk`;

-- Drop existing tables in reverse FK order
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `status_logs`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `tickets`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------
-- 1. Table: users
-- ----------------------------------------------------------
CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'technician', 'admin') NOT NULL DEFAULT 'user',
    `line_user_id` VARCHAR(100) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. Table: categories
-- ----------------------------------------------------------
CREATE TABLE `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. Table: tickets
-- ----------------------------------------------------------
CREATE TABLE `tickets` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `category_id` INT NOT NULL,
    `technician_id` INT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('open', 'assigned', 'in_progress', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `resolved_at` DATETIME NULL,
    `closed_at` DATETIME NULL,
    CONSTRAINT `fk_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tickets_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_tickets_tech` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. Table: comments
-- ----------------------------------------------------------
CREATE TABLE `comments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `body` TEXT NOT NULL,
    `image_path` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_comments_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. Table: status_logs
-- ----------------------------------------------------------
CREATE TABLE `status_logs` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL,
    `changed_by` INT NOT NULL,
    `from_status` ENUM('open', 'assigned', 'in_progress', 'resolved', 'closed') NOT NULL,
    `to_status` ENUM('open', 'assigned', 'in_progress', 'resolved', 'closed') NOT NULL,
    `note` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_logs_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_logs_user` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. Table: ratings
-- ----------------------------------------------------------
CREATE TABLE `ratings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ticket_id` INT NOT NULL UNIQUE,
    `score` TINYINT NOT NULL CHECK (`score` BETWEEN 1 AND 5),
    `feedback` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_ratings_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==========================================================
-- Initial Seed Data
-- Passwords:
-- admin@helpdesk.local  -> admin123
-- tech@helpdesk.local   -> tech123
-- tech2@helpdesk.local  -> tech123
-- user@helpdesk.local   -> user123
-- ==========================================================

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `line_user_id`, `created_at`) VALUES
(1, 'ผู้ดูแลระบบ (Admin)', 'admin@helpdesk.local', '$2y$10$QOaZ3kE3yT4V2ZzK5mN0ve7FjKqG7bA9oF.J3I.u4x4B6b9Cq7E6K', 'admin', 'Uadmin1234567890', NOW()),
(2, 'ช่างสมชาย บริการดี', 'tech@helpdesk.local', '$2y$10$1/fP3G8J6eZp4Yx6c2r4Nu7WjLqK7cA9oE.K3H.v4w4A6a9Bq8D5J', 'technician', 'Utech1111111111', NOW()),
(3, 'ช่างวิชัย สายไอที', 'tech2@helpdesk.local', '$2y$10$1/fP3G8J6eZp4Yx6c2r4Nu7WjLqK7cA9oE.K3H.v4w4A6a9Bq8D5J', 'technician', 'Utech2222222222', NOW()),
(4, 'คุณสมหญิง ใจดี', 'user@helpdesk.local', '$2y$10$e8wF4H7K5eYp3Zx5b1r3Mu6VjKpG6bA8oD.I2G.u3v3Z5z8Ap7C4I', 'user', 'Uuser4444444444', NOW());

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Hardware & คอมพิวเตอร์', 'ปัญหาเครื่องคอมพิวเตอร์, ปริ้นเตอร์, สแกนเนอร์, หน้าจอ, อุปกรณ์ต่อพ่วง'),
(2, 'Software & ระบบปฏิบัติการ', 'ปัญหา Windows, โปรแกรมค้าง, ลิขสิทธิ์ Office, ซอฟต์แวร์ทำงาน'),
(3, 'Network & อินเทอร์เน็ต', 'Wi-Fi หลุด, สาย LAN ใช้งานไม่ได้, VPN เข้าบริษัทไม่ได้'),
(4, 'CCTV & ระบบความปลอดภัย', 'กล้องวงจรปิด, ระบบสแกนนิ้วเข้างาน, คีย์การ์ดประตู'),
(5, 'Email & บัญชีผู้ใช้งาน', 'รีเซ็ตรหัสผ่าน, ล็อกอินไม่ได้, ขอเปิดสิทธิ์การใช้งานระบบใหม่');

-- Sample Initial Tickets
INSERT INTO `tickets` (`id`, `user_id`, `category_id`, `technician_id`, `title`, `description`, `status`, `priority`, `created_at`, `updated_at`, `resolved_at`, `closed_at`) VALUES
(1, 4, 1, 2, 'คอมพิวเตอร์แผนกบัญชีเปิดไม่ติด มีไฟกระพริบสีส้ม', 'กดปุ่ม Power แล้วพัดลมหมุนแป๊บเดียวแล้วดับ มีเสียงเตือน Beep 3 ครั้ง', 'in_progress', 'high', DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), NULL, NULL),
(2, 4, 3, 2, 'Wi-Fi ชั้น 3 หลุดบ่อยมาก สัญญาณอ่อน', 'ใช้งานระหว่างประชุมไม่ได้เลย หลุดทุก 5 นาที รบกวนตรวจสอบ Access Point ด่วนครับ', 'assigned', 'urgent', DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), NULL, NULL),
(3, 4, 2, NULL, 'ขอติดตั้งโปรแกรม Adobe Acrobat Reader ลิขสิทธิ์', 'ต้องการใช้สำหรับเปิดเอกสารสัญญาทางกฎหมาย', 'open', 'low', DATE_SUB(NOW(), INTERVAL 3 HOUR), NOW(), NULL, NULL),
(4, 4, 4, 3, 'เครื่องสแกนนิ้วประตูหน้าไม่ยอมบันทึกเวลา', 'พนักงานสแกนติดแต่หน้าจอขึ้น Error code: E-02', 'resolved', 'medium', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), NULL);

-- Sample Comments
INSERT INTO `comments` (`id`, `ticket_id`, `user_id`, `body`, `image_path`, `created_at`) VALUES
(1, 1, 2, 'ช่างสมชายรับงานแล้วครับ กำลังเดินทางไปตรวจสอบที่แผนกบัญชีชั้น 2', NULL, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 1, 2, 'เบื้องต้นพบปัญหา RAM หลวมและมีฝุ่น ได้ทำความสะอาดสล็อตและทดสอบเปิดติดแล้ว อยู่ระหว่าง Stress Test เมนบอร์ด', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 2, 1, 'มอบหมายให้ช่างสมชาย เข้าไปเช็ค Controller และสายแลน PoE ที่ตู้ Rack ชั้น 3', NULL, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 4, 3, 'เปลี่ยนเซ็นเซอร์ Optical ตัวใหม่เรียบร้อยแล้ว ทดสอบสแกน 10 ครั้งผ่านทั้งหมดครับ', 'storage/uploads/sample_sensor_fix.jpg', DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Sample Status Logs
INSERT INTO `status_logs` (`id`, `ticket_id`, `changed_by`, `from_status`, `to_status`, `note`, `created_at`) VALUES
(1, 1, 1, 'open', 'assigned', 'แอดมินมอบหมายงานให้ช่างสมชาย', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 1, 2, 'assigned', 'in_progress', 'ช่างสมชายรับงานเข้าดำเนินการตรวจสอบที่หน้างาน', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 2, 1, 'open', 'assigned', 'แอดมินมอบหมายงานด่วนให้ช่างสมชาย', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(4, 4, 1, 'open', 'assigned', 'มอบหมายงานให้ช่างวิชัย', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(5, 4, 3, 'assigned', 'in_progress', 'ช่างวิชัยเริ่มงานเปลี่ยนอะไหล่', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(6, 4, 3, 'in_progress', 'resolved', 'ซ่อมเสร็จสิ้น เปลี่ยนเซ็นเซอร์แล้ว', DATE_SUB(NOW(), INTERVAL 1 DAY));
