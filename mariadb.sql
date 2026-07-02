-- MariaDB schema for PearlNest
-- Compatible with MariaDB 10.5+ and designed to avoid common compatibility issues.

CREATE DATABASE IF NOT EXISTS `pearlnest`
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `pearlnest`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `property_videos`;
DROP TABLE IF EXISTS `property_images`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `admins` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL COMMENT 'bcrypt hash',
  `name` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `properties` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `type` ENUM(
    'hostel_shared',
    'hostel_private',
    'studio',
    '1br',
    '2br',
    '3br',
    'self_contained'
  ) NOT NULL DEFAULT 'studio',
  `description` TEXT DEFAULT NULL,
  `location` VARCHAR(255) NOT NULL COMMENT 'e.g. Kololo, Kampala',
  `district` VARCHAR(100) DEFAULT NULL COMMENT 'e.g. Kampala Central',
  `address` TEXT DEFAULT NULL COMMENT 'Street / plot number',
  `price` DECIMAL(12,2) NOT NULL,
  `price_period` ENUM('night','month','year') NOT NULL DEFAULT 'month',
  `bedrooms` INT NOT NULL DEFAULT 1,
  `bathrooms` INT NOT NULL DEFAULT 1,
  `area_sqm` INT DEFAULT NULL COMMENT 'Floor area in square metres',
  `status` ENUM('available','rented','sold','under_review') NOT NULL DEFAULT 'available',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `amenities` TEXT DEFAULT NULL COMMENT 'Comma-separated list',
  `badge` VARCHAR(50) DEFAULT NULL COMMENT 'e.g. VERIFIED, POPULAR, LAST AVAILABLE',
  `rating` DECIMAL(3,1) NOT NULL DEFAULT 0.0,
  `rating_count` INT NOT NULL DEFAULT 0,
  `views` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_type` (`type`),
  KEY `idx_district` (`district`),
  KEY `idx_is_featured` (`is_featured`),
  KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `property_images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `property_id` INT NOT NULL,
  `image_path` VARCHAR(500) NOT NULL COMMENT 'Relative path or external URL',
  `is_primary` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_property_id` (`property_id`),
  CONSTRAINT `fk_img_property`
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `property_videos` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `property_id` INT NOT NULL,
  `video_path` VARCHAR(500) NOT NULL,
  `title` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vid_property_id` (`property_id`),
  CONSTRAINT `fk_vid_property`
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `inquiries` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `property_id` INT DEFAULT NULL COMMENT 'NULL = general inquiry',
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(50) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('pending','responded','closed') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inq_property_id` (`property_id`),
  KEY `idx_inq_status` (`status`),
  CONSTRAINT `fk_inq_property`
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `ratings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `property_id` INT NOT NULL,
  `name` VARCHAR(255) DEFAULT 'Anonymous',
  `rating` TINYINT NOT NULL COMMENT '1–5',
  `review` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rat_property_id` (`property_id`),
  CONSTRAINT `fk_rat_property`
    FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Additional dummy data for testing ─────────────────────
INSERT INTO `properties`
    (`id`, `title`, `type`, `description`, `location`, `district`, `address`,
     `price`, `price_period`, `bedrooms`, `bathrooms`, `area_sqm`,
     `status`, `is_featured`, `amenities`, `badge`, `rating`, `rating_count`, `views`)
VALUES
(11, 'Kampala Road Modern Loft', 'studio',
    'Bright loft-style studio close to Kampala Road with an open-plan layout and modern finishes.',
    'Kampala Road', 'Kampala Central', 'Kampala Road, Plot 8',
    760000, 'month', 1, 1, 38,
    'available', 0,
    'Wi-Fi,Security,Parking,Water,Electricity', NULL, 4.1, 4, 82),
(12, 'Mbuya Family House', '3br',
    'Spacious family home with a fenced compound, garden area, and room for parking.',
    'Mbuya', 'Kawempe', 'Mbuya Road, House 12',
    2100000, 'month', 3, 2, 170,
    'available', 1,
    'Parking,Garden,Security,Water,Electricity', 'POPULAR', 4.6, 7, 154),
(13, 'Makerere Student Annex', 'hostel_shared',
    'Budget-friendly shared accommodation near Makerere University with study desks and common lounge.',
    'Makerere', 'Kawempe', 'Wandegeya Road',
    180000, 'month', 1, 1, 15,
    'available', 0,
    'Wi-Fi,Shared Bathrooms,Study Lounge,Water', NULL, 3.9, 11, 67);

INSERT INTO `property_images` (`property_id`, `image_path`, `is_primary`) VALUES
(11, 'https://picsum.photos/seed/loft1/800/500', 1),
(11, 'https://picsum.photos/seed/loft2/800/500', 0),
(12, 'https://picsum.photos/seed/familyhouse1/800/500', 1),
(12, 'https://picsum.photos/seed/familyhouse2/800/500', 0),
(13, 'https://picsum.photos/seed/studentannex1/800/500', 1),
(13, 'https://picsum.photos/seed/studentannex2/800/500', 0);

INSERT INTO `property_videos` (`property_id`, `video_path`, `title`) VALUES
(11, 'https://example.com/videos/loft-tour.mp4', 'Loft Tour'),
(12, 'https://example.com/videos/family-house.mp4', 'House Walkthrough'),
(13, 'https://example.com/videos/student-annex.mp4', 'Student Annex Tour');

INSERT INTO `inquiries` (`property_id`, `name`, `email`, `phone`, `message`, `status`) VALUES
(11, 'Noah Kato', 'noah@example.com', '+256712345678', 'Is the loft still available for immediate move-in?', 'pending'),
(12, 'Ruth Namuli', 'ruth@example.com', '+256788765432', 'Can I arrange a viewing this weekend for the family house?', 'pending'),
(13, 'Ali Bukenya', 'ali@example.com', '+256770111222', 'Do you offer discounts for long-term student bookings?', 'responded');

INSERT INTO `ratings` (`property_id`, `name`, `rating`, `review`) VALUES
(11, 'Irene Muwonge', 4, 'Modern space and very convenient location.'),
(12, 'David Ssemakula', 5, 'Great house with plenty of room for the family.'),
(13, 'Moses Kibirige', 4, 'Affordable and close to campus. Good value.');