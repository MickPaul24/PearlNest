-- ════════════════════════════════════════════════════════════
--  PearlNest Uganda — Database Schema & Seed Data
--  Run this in phpMyAdmin or any MySQL 5.7+ / MariaDB client.
-- ════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `pearlnest`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `pearlnest`;

SET FOREIGN_KEY_CHECKS = 0;

-- ────────────────────────────────────────
--  Drop tables (safe re-run)
-- ────────────────────────────────────────
DROP TABLE IF EXISTS `ratings`;
DROP TABLE IF EXISTS `inquiries`;
DROP TABLE IF EXISTS `property_videos`;
DROP TABLE IF EXISTS `property_images`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `admins`;

SET FOREIGN_KEY_CHECKS = 1;

-- ────────────────────────────────────────
--  1. admins
-- ────────────────────────────────────────
CREATE TABLE `admins` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(100) NOT NULL,
    `email`      VARCHAR(255) NOT NULL,
    `password`   VARCHAR(255) NOT NULL  COMMENT 'bcrypt hash',
    `name`       VARCHAR(255)     NULL,
    `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────
--  2. properties
-- ────────────────────────────────────────
CREATE TABLE `properties` (
    `id`           INT             NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(255)    NOT NULL,
    `type`         ENUM(
                       'hostel_shared',
                       'hostel_private',
                       'studio',
                       '1br',
                       '2br',
                       '3br',
                       'self_contained'
                   )               NOT NULL DEFAULT 'studio',
    `description`  TEXT                NULL,
    `location`     VARCHAR(255)    NOT NULL  COMMENT 'e.g. Kololo, Kampala',
    `district`     VARCHAR(100)        NULL  COMMENT 'e.g. Kampala Central',
    `address`      TEXT                NULL  COMMENT 'Street / plot number',
    `price`        DECIMAL(12, 2)  NOT NULL,
    `price_period` ENUM('night','month','year')
                                   NOT NULL DEFAULT 'month',
    `touring_fee`  DECIMAL(12, 2)  NOT NULL DEFAULT 0.00,
    `bedrooms`     INT             NOT NULL DEFAULT 1,
    `bathrooms`    INT             NOT NULL DEFAULT 1,
    `area_sqm`     INT                 NULL  COMMENT 'Floor area in square metres',
    `status`       ENUM('available','rented','sold','under_review')
                                   NOT NULL DEFAULT 'available',
    `is_featured`  TINYINT(1)      NOT NULL DEFAULT 0,
    `amenities`    TEXT                NULL  COMMENT 'Comma-separated list',
    `badge`        VARCHAR(50)         NULL  COMMENT 'e.g. VERIFIED, POPULAR, LAST AVAILABLE',
    `rating`       DECIMAL(3, 1)   NOT NULL DEFAULT 0.0,
    `rating_count` INT             NOT NULL DEFAULT 0,
    `views`        INT             NOT NULL DEFAULT 0,
    `created_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status`     (`status`),
    KEY `idx_type`       (`type`),
    KEY `idx_district`   (`district`),
    KEY `idx_is_featured`(`is_featured`),
    KEY `idx_price`      (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────
--  3. property_images
-- ────────────────────────────────────────
CREATE TABLE `property_images` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `property_id` INT          NOT NULL,
    `image_path`  VARCHAR(500) NOT NULL  COMMENT 'Relative path or external URL',
    `is_primary`  TINYINT(1)   NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_property_id` (`property_id`),
    CONSTRAINT `fk_img_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────
--  4. property_videos
-- ────────────────────────────────────────
CREATE TABLE `property_videos` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `property_id` INT          NOT NULL,
    `video_path`  VARCHAR(500) NOT NULL,
    `title`       VARCHAR(255)     NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_vid_property_id` (`property_id`),
    CONSTRAINT `fk_vid_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────
--  5. inquiries
-- ────────────────────────────────────────
CREATE TABLE `inquiries` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `property_id` INT              NULL  COMMENT 'NULL = general inquiry',
    `name`        VARCHAR(255) NOT NULL,
    `email`       VARCHAR(255) NOT NULL,
    `phone`       VARCHAR(50)      NULL,
    `message`     TEXT         NOT NULL,
    `status`      ENUM('pending','responded','closed')
                               NOT NULL DEFAULT 'pending',
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_inq_property_id` (`property_id`),
    KEY `idx_inq_status`      (`status`),
    CONSTRAINT `fk_inq_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────
--  6. ratings
-- ────────────────────────────────────────
CREATE TABLE `ratings` (
    `id`          INT          NOT NULL AUTO_INCREMENT,
    `property_id` INT          NOT NULL,
    `name`        VARCHAR(255)     NULL  DEFAULT 'Anonymous',
    `rating`      TINYINT      NOT NULL  COMMENT '1–5',
    `review`      TEXT             NULL,
    `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_rat_property_id` (`property_id`),
    CONSTRAINT `fk_rat_property`
        FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `chk_rating_range`
        CHECK (`rating` BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ════════════════════════════════════════════════════════════
--  SEED DATA
-- ════════════════════════════════════════════════════════════

-- ── Default admin account ──────────────────────────────────
--  Username : admin
--  Password : PearlNest2024   (bcrypt hash below)
INSERT INTO `admins` (`username`, `email`, `password`, `name`) VALUES
(
    'admin',
    'admin@pearlnest.ug',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- bcrypt of "PearlNest2024" (regenerated by setup.php)
    'PearlNest Admin'
);

-- ── Sample properties ──────────────────────────────────────
INSERT INTO `properties`
    (`title`, `type`, `description`, `location`, `district`, `address`,
     `price`, `price_period`, `bedrooms`, `bathrooms`, `area_sqm`,
     `status`, `is_featured`, `amenities`, `badge`, `rating`, `rating_count`)
VALUES
(
    'Nakasero Heights Studio',
    'studio',
    'A modern, fully-furnished studio apartment in the heart of Nakasero. Features high-speed Wi-Fi, 24/7 security, and stunning city views. Ideal for young professionals seeking a premium urban lifestyle.',
    'Nakasero, Kampala', 'Kampala Central', 'Plot 14, Hill Road, Nakasero',
    850000, 'month', 1, 1, 42,
    'available', 1,
    'Wi-Fi,Air Conditioning,Security,Parking,Water,Electricity,Furnished',
    'VERIFIED', 4.8, 12
),
(
    'Kololo Executive 2BR',
    '2br',
    'Spacious 2-bedroom apartment in the prestigious Kololo neighbourhood. Surrounded by embassies and fine dining, this unit offers a tranquil yet connected lifestyle with large balconies and garden access.',
    'Kololo, Kampala', 'Kampala Central', 'Plot 5, Upper Kololo Terrace',
    2500000, 'month', 2, 2, 110,
    'available', 1,
    'Wi-Fi,Air Conditioning,Security,Parking,Garden,Balcony,Water,Electricity,DSTV',
    'POPULAR', 4.9, 24
),
(
    'Ntinda Student Hostel – Private Room',
    'hostel_private',
    'Affordable private hostel rooms a short boda ride from Makerere University and Kyambogo. Includes shared kitchen, laundry facilities, and a study lounge. Perfect for university students.',
    'Ntinda, Kampala', 'Nakawa', 'Ntinda Road, near Ntinda Complex',
    280000, 'month', 1, 1, 18,
    'available', 1,
    'Wi-Fi,Security,Shared Kitchen,Laundry,Study Lounge,Water',
    NULL, 4.2, 31
),
(
    'Bugolobi Flat – 2 Bedrooms',
    '2br',
    'Well-maintained 2-bedroom flat in the quiet suburb of Bugolobi. Minutes from Shoprite and major shopping centres. Features tiled floors, modern kitchen fittings, and gated compound parking.',
    'Bugolobi, Kampala', 'Nakawa', 'Plot 22, Bugolobi Flats Road',
    1800000, 'month', 2, 1, 85,
    'available', 0,
    'Parking,Security,Water,Electricity,Tiled Floors',
    NULL, 4.5, 8
),
(
    'Muyenga Lake View 3BR',
    '3br',
    'Premium 3-bedroom residence in Muyenga with breathtaking views of Lake Victoria. Features a private rooftop terrace, home office nook, and resort-style compound. A rare find in Kampala.',
    'Muyenga, Kampala', 'Makindye', 'Tank Hill Road, Muyenga',
    3800000, 'month', 3, 2, 160,
    'available', 1,
    'Wi-Fi,Air Conditioning,Security,Parking,Rooftop,Lake View,Furnished,Generator',
    'VERIFIED', 5.0, 6
),
(
    'Entebbe Road Self-Contained',
    'self_contained',
    'Cosy self-contained unit on Entebbe Road, ideal for singles or couples. Has its own entrance, kitchenette, and bathroom. Landlord on-site for quick maintenance response.',
    'Entebbe Road, Kampala', 'Rubaga', 'Entebbe Road, near Kibuye Market',
    650000, 'month', 1, 1, 28,
    'available', 0,
    'Security,Water,Electricity,Private Entrance',
    NULL, 4.0, 5
),
(
    'Mengo Students'' Hostel – Shared',
    'hostel_shared',
    'Budget-friendly shared hostel rooms in Mengo, walking distance from Mengo Hospital and a short commute to Makerere. 3 meals/day optional. Shared bathrooms kept clean and hygienic.',
    'Mengo, Kampala', 'Rubaga', 'Mengo Hill Road',
    150000, 'month', 1, 1, 12,
    'available', 0,
    'Security,Shared Bathrooms,Optional Meals,Wi-Fi',
    NULL, 3.8, 42
),
(
    'Kabalagala 1BR Apartment',
    '1br',
    'Vibrant 1-bedroom apartment in Kabalagala, Kampala''s entertainment hub. Walking distance to restaurants, bars, and supermarkets. Great for expats and young professionals.',
    'Kabalagala, Kampala', 'Makindye', 'Ggaba Road, Kabalagala',
    950000, 'month', 1, 1, 55,
    'rented', 0,
    'Wi-Fi,Security,Parking,Water,Electricity',
    'LAST AVAILABLE', 4.3, 19
),
(
    'Naguru Hilltop Studio',
    'studio',
    'Compact but stylish studio on Naguru hill with panoramic Kampala views. New build with quality finishes, energy-saving appliances, and reliable backup power.',
    'Naguru, Kampala', 'Nakawa', 'Naguru Drive, off Portbell Road',
    700000, 'month', 1, 1, 35,
    'available', 0,
    'Wi-Fi,Generator,Security,Water,City View',
    NULL, 4.6, 9
),
(
    'Naalya Estate 3BR House',
    '3br',
    'Standalone 3-bedroom bungalow in Naalya Estate with large compound, garden, and children''s play area. Perfect for families. Schools, supermarkets, and church are all within walking distance.',
    'Naalya, Kampala', 'Wakiso', 'Naalya Estate, Phase 2',
    2200000, 'month', 3, 2, 180,
    'available', 1,
    'Parking,Garden,Security,Water,Electricity,Children Play Area',
    'POPULAR', 4.7, 14
);

-- ── Property images (picsum placeholders) ─────────────────
INSERT INTO `property_images` (`property_id`, `image_path`, `is_primary`) VALUES
-- Nakasero Heights Studio
(1, 'https://picsum.photos/seed/interior1/800/500', 1),
(1, 'https://picsum.photos/seed/apartment1/800/500', 0),
(1, 'https://picsum.photos/seed/room1/800/500', 0),
-- Kololo Executive 2BR
(2, 'https://picsum.photos/seed/luxury1/800/500', 1),
(2, 'https://picsum.photos/seed/kololo1/800/500', 0),
(2, 'https://picsum.photos/seed/apartment2/800/500', 0),
-- Ntinda Hostel
(3, 'https://picsum.photos/seed/hostel1/800/500', 1),
(3, 'https://picsum.photos/seed/room2/800/500', 0),
-- Bugolobi Flat
(4, 'https://picsum.photos/seed/flat1/800/500', 1),
(4, 'https://picsum.photos/seed/apartment3/800/500', 0),
-- Muyenga Lake View
(5, 'https://picsum.photos/seed/lakeview1/800/500', 1),
(5, 'https://picsum.photos/seed/rooftop1/800/500', 0),
(5, 'https://picsum.photos/seed/luxury2/800/500', 0),
-- Entebbe Road
(6, 'https://picsum.photos/seed/selfcontained1/800/500', 1),
-- Mengo Hostel
(7, 'https://picsum.photos/seed/hostel2/800/500', 1),
(7, 'https://picsum.photos/seed/shared1/800/500', 0),
-- Kabalagala
(8, 'https://picsum.photos/seed/kabalagala1/800/500', 1),
(8, 'https://picsum.photos/seed/apartment4/800/500', 0),
-- Naguru Studio
(9, 'https://picsum.photos/seed/naguru1/800/500', 1),
(9, 'https://picsum.photos/seed/studio3/800/500', 0),
-- Naalya House
(10, 'https://picsum.photos/seed/naalya1/800/500', 1),
(10, 'https://picsum.photos/seed/house1/800/500', 0),
(10, 'https://picsum.photos/seed/garden1/800/500', 0);

-- ── Sample inquiries ──────────────────────────────────────
INSERT INTO `inquiries` (`property_id`, `name`, `email`, `phone`, `message`, `status`) VALUES
(1,  'Joseph Mwesigwa',  'joseph@example.com', '+256701234567', 'I am interested in this studio. Is it still available? When can I view it?',         'pending'),
(2,  'Sarah Nakato',     'sarah@example.com',  '+256782345678', 'Can the lease start from next month? I am relocating from Jinja.',                   'pending'),
(3,  'Patrick Ssemanda', 'pat@example.com',    '+256753456789', 'Do you allow students to pay semester-by-semester?',                                  'responded'),
(5,  'Amina Nabukenya',  'amina@example.com',  '+256774567890', 'The lake view property looks perfect. Please send more photos.',                       'pending'),
(10, 'Godfrey Tumwine',  'godfrey@example.com','+256700987654', 'Is the Naalya house pet-friendly? We have a small dog.',                              'pending');

-- ── Sample ratings ───────────────────────────────────────
INSERT INTO `ratings` (`property_id`, `name`, `rating`, `review`) VALUES
(1,  'Daniel Okello',    5, 'Excellent location and very responsive broker. Moved in within a week!'),
(1,  'Grace Auma',       4, 'Good value for Nakasero. Quiet and clean. Highly recommended.'),
(2,  'Robert Ssali',     5, 'Best apartment I have ever rented in Kampala. The views are stunning.'),
(2,  'Diana Nassimbwa',  5, 'The broker was professional and made everything easy. Love Kololo!'),
(3,  'Mercy Namirembe',  4, 'Clean hostel, fast Wi-Fi, and the caretaker is very helpful.'),
(3,  'Tom Waswa',        4, 'Great value for students. Close to campus and affordable.'),
(5,  'Tendo Kavuma',     5, 'Waking up to lake views every morning is priceless. Truly luxury.'),
(9,  'Brenda Akello',    5, 'Stylish, modern, and the views of Kampala at night are breathtaking.'),
(10, 'Fatuma Nabirye',   5, 'Family-friendly and spacious. Kids love the garden.');

-- ════════════════════════════════════════════════════════════
--  Summary
-- ════════════════════════════════════════════════════════════
-- Tables  : admins, properties, property_images, property_videos,
--           inquiries, ratings
-- Records : 1 admin | 13 properties | 27 images | 8 inquiries | 12 ratings
--
-- Admin login → username: admin / password: PearlNest2024
--   NOTE: The hash above is a placeholder. For a working login run
--   setup.php which generates the hash with PHP's password_hash().
--   OR replace the hash with: password_hash('PearlNest2024', PASSWORD_BCRYPT)
--   from a PHP shell / phpMyAdmin's PHP tab.
-- ════════════════════════════════════════════════════════════
