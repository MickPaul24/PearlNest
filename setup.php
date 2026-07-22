<?php
/**
 * PearlNest — Database Setup Script
 * Run once: http://localhost/lyton/setup.php
 * Delete or restrict access after running.
 */

$config = require __DIR__ . '/config/config.php';

try {
    $dsn = sprintf('mysql:host=%s;charset=%s', $config['db']['host'], $config['db']['charset']);
    $pdo = new PDO($dsn, $config['db']['user'], $config['db']['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<b>Connection failed:</b> ' . $e->getMessage() . '<br>Make sure MySQL is running.');
}

$db = $config['db']['name'];
$pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE `$db`");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

$pdo->exec("DROP TABLE IF EXISTS ratings");
$pdo->exec("DROP TABLE IF EXISTS inquiries");
$pdo->exec("DROP TABLE IF EXISTS property_videos");
$pdo->exec("DROP TABLE IF EXISTS property_images");
$pdo->exec("DROP TABLE IF EXISTS properties");
$pdo->exec("DROP TABLE IF EXISTS admins");

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// ---------- Tables ----------

$pdo->exec("
CREATE TABLE admins (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(100) NOT NULL UNIQUE,
    email      VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    name       VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("
CREATE TABLE properties (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    type         ENUM('hostel_shared','hostel_private','studio','1br','2br','3br','self_contained') NOT NULL DEFAULT 'studio',
    description  TEXT,
    location     VARCHAR(255) NOT NULL,
    district     VARCHAR(100),
    address      TEXT,
    price        DECIMAL(12,2) NOT NULL,
    price_period ENUM('night','month','year') DEFAULT 'month',
    touring_fee  DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    bedrooms     INT DEFAULT 1,
    bathrooms    INT DEFAULT 1,
    area_sqm     INT,
    status       ENUM('available','rented','sold','under_review') DEFAULT 'available',
    is_featured  TINYINT(1) DEFAULT 0,
    amenities    TEXT COMMENT 'comma-separated list',
    badge        VARCHAR(50) COMMENT 'e.g. VERIFIED, POPULAR, LAST AVAILABLE',
    rating       DECIMAL(3,1) DEFAULT 0.0,
    rating_count INT DEFAULT 0,
    views        INT DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");

$pdo->exec("
CREATE TABLE property_images (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    image_path  VARCHAR(500) NOT NULL,
    is_primary  TINYINT(1) DEFAULT 0,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)");

$pdo->exec("
CREATE TABLE property_videos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    video_path  VARCHAR(500) NOT NULL,
    title       VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)");

$pdo->exec("
CREATE TABLE inquiries (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT,
    name        VARCHAR(255) NOT NULL,
    email       VARCHAR(255) NOT NULL,
    phone       VARCHAR(50),
    message     TEXT NOT NULL,
    status      ENUM('pending','responded','closed') DEFAULT 'pending',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE SET NULL
)");

$pdo->exec("
CREATE TABLE ratings (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    name        VARCHAR(255),
    rating      INT NOT NULL,
    review      TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
)");

// ---------- Default admin ----------
// Login: admin / PearlNest2024
$adminPass = password_hash('PearlNest2024', PASSWORD_DEFAULT);
$pdo->exec("
INSERT INTO admins (username, email, password, name)
VALUES ('admin', 'admin@pearlnest.ug', '$adminPass', 'PearlNest Admin')");

// ---------- Seed properties ----------
$properties = [
    [
        'title'       => 'Nakasero Heights Studio',
        'type'        => 'studio',
        'description' => 'A modern, fully-furnished studio apartment in the heart of Nakasero. Features high-speed Wi-Fi, 24/7 security, and stunning city views. Ideal for young professionals seeking a premium urban lifestyle.',
        'location'    => 'Nakasero, Kampala',
        'district'    => 'Kampala Central',
        'address'     => 'Plot 14, Hill Road, Nakasero',
        'price'       => 850000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 42,
        'status'      => 'available',
        'is_featured' => 1,
        'amenities'   => 'Wi-Fi,Air Conditioning,Security,Parking,Water,Electricity,Furnished',
        'badge'       => 'VERIFIED',
        'rating'      => 4.8,
        'rating_count'=> 12,
    ],
    [
        'title'       => 'Kololo Executive 2BR',
        'type'        => '2br',
        'description' => 'Spacious 2-bedroom apartment in the prestigious Kololo neighbourhood. Surrounded by embassies and fine dining, this unit offers a tranquil yet connected lifestyle with large balconies and garden access.',
        'location'    => 'Kololo, Kampala',
        'district'    => 'Kampala Central',
        'address'     => 'Plot 5, Upper Kololo Terrace',
        'price'       => 2500000,
        'bedrooms'    => 2,
        'bathrooms'   => 2,
        'area_sqm'    => 110,
        'status'      => 'available',
        'is_featured' => 1,
        'amenities'   => 'Wi-Fi,Air Conditioning,Security,Parking,Garden,Balcony,Water,Electricity,DSTV',
        'badge'       => 'POPULAR',
        'rating'      => 4.9,
        'rating_count'=> 24,
    ],
    [
        'title'       => 'Ntinda Student Hostel – Private Room',
        'type'        => 'hostel_private',
        'description' => 'Affordable private hostel rooms a short boda ride from Makerere University and Kyambogo. Includes shared kitchen, laundry facilities, and a study lounge. Perfect for university students.',
        'location'    => 'Ntinda, Kampala',
        'district'    => 'Nakawa',
        'address'     => 'Ntinda Road, near Ntinda Complex',
        'price'       => 280000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 18,
        'status'      => 'available',
        'is_featured' => 1,
        'amenities'   => 'Wi-Fi,Security,Shared Kitchen,Laundry,Study Lounge,Water',
        'badge'       => null,
        'rating'      => 4.2,
        'rating_count'=> 31,
    ],
    [
        'title'       => 'Bugolobi Flat – 2 Bedrooms',
        'type'        => '2br',
        'description' => 'Well-maintained 2-bedroom flat in the quiet suburb of Bugolobi. Minutes from Shoprite and major shopping centres. Features tiled floors, modern kitchen fittings, and gated compound parking.',
        'location'    => 'Bugolobi, Kampala',
        'district'    => 'Nakawa',
        'address'     => 'Plot 22, Bugolobi Flats Road',
        'price'       => 1800000,
        'bedrooms'    => 2,
        'bathrooms'   => 1,
        'area_sqm'    => 85,
        'status'      => 'available',
        'is_featured' => 0,
        'amenities'   => 'Parking,Security,Water,Electricity,Tiled Floors',
        'badge'       => null,
        'rating'      => 4.5,
        'rating_count'=> 8,
    ],
    [
        'title'       => 'Muyenga Lake View 3BR',
        'type'        => '3br',
        'description' => 'Premium 3-bedroom residence in Muyenga with breathtaking views of Lake Victoria. Features a private rooftop terrace, home office nook, and resort-style compound. A rare find in Kampala.',
        'location'    => 'Muyenga, Kampala',
        'district'    => 'Makindye',
        'address'     => 'Tank Hill Road, Muyenga',
        'price'       => 3800000,
        'bedrooms'    => 3,
        'bathrooms'   => 2,
        'area_sqm'    => 160,
        'status'      => 'available',
        'is_featured' => 1,
        'amenities'   => 'Wi-Fi,Air Conditioning,Security,Parking,Rooftop,Lake View,Furnished,Generator',
        'badge'       => 'VERIFIED',
        'rating'      => 5.0,
        'rating_count'=> 6,
    ],
    [
        'title'       => 'Entebbe Road Self-Contained',
        'type'        => 'self_contained',
        'description' => 'Cosy self-contained unit on Entebbe Road, ideal for singles or couples. Has its own entrance, kitchenette, and bathroom. Landlord on-site for quick maintenance response.',
        'location'    => 'Entebbe Road, Kampala',
        'district'    => 'Rubaga',
        'address'     => 'Entebbe Road, near Kibuye Market',
        'price'       => 650000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 28,
        'status'      => 'available',
        'is_featured' => 0,
        'amenities'   => 'Security,Water,Electricity,Private Entrance',
        'badge'       => null,
        'rating'      => 4.0,
        'rating_count'=> 5,
    ],
    [
        'title'       => 'Mengo Students\' Hostel – Shared',
        'type'        => 'hostel_shared',
        'description' => 'Budget-friendly shared hostel rooms in Mengo, walking distance from Mengo Hospital and a short commute to Makerere. 3 meals/day optional. Shared bathrooms kept clean and hygienic.',
        'location'    => 'Mengo, Kampala',
        'district'    => 'Rubaga',
        'address'     => 'Mengo Hill Road',
        'price'       => 150000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 12,
        'status'      => 'available',
        'is_featured' => 0,
        'amenities'   => 'Security,Shared Bathrooms,Optional Meals,Wi-Fi',
        'badge'       => null,
        'rating'      => 3.8,
        'rating_count'=> 42,
    ],
    [
        'title'       => 'Kabalagala 1BR Apartment',
        'type'        => '1br',
        'description' => 'Vibrant 1-bedroom apartment in Kabalagala, Kampala\'s entertainment hub. Walking distance to restaurants, bars, and supermarkets. Great for expats and young professionals who love city nightlife.',
        'location'    => 'Kabalagala, Kampala',
        'district'    => 'Makindye',
        'address'     => 'Ggaba Road, Kabalagala',
        'price'       => 950000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 55,
        'status'      => 'rented',
        'is_featured' => 0,
        'amenities'   => 'Wi-Fi,Security,Parking,Water,Electricity',
        'badge'       => 'LAST AVAILABLE',
        'rating'      => 4.3,
        'rating_count'=> 19,
    ],
    [
        'title'       => 'Naguru Hilltop Studio',
        'type'        => 'studio',
        'description' => 'Compact but stylish studio on Naguru hill with panoramic Kampala views. New build with quality finishes, energy-saving appliances, and reliable UMEME backup power.',
        'location'    => 'Naguru, Kampala',
        'district'    => 'Nakawa',
        'address'     => 'Naguru Drive, off Portbell Road',
        'price'       => 700000,
        'bedrooms'    => 1,
        'bathrooms'   => 1,
        'area_sqm'    => 35,
        'status'      => 'available',
        'is_featured' => 0,
        'amenities'   => 'Wi-Fi,Generator,Security,Water,City View',
        'badge'       => null,
        'rating'      => 4.6,
        'rating_count'=> 9,
    ],
    [
        'title'       => 'Naalya Estate 3BR House',
        'type'        => '3br',
        'description' => 'Standalone 3-bedroom bungalow in Naalya Estate with large compound, garden, and children\'s play area. Perfect for families. Schools, supermarkets, and church are all within walking distance.',
        'location'    => 'Naalya, Kampala',
        'district'    => 'Wakiso',
        'address'     => 'Naalya Estate, Phase 2',
        'price'       => 2200000,
        'bedrooms'    => 3,
        'bathrooms'   => 2,
        'area_sqm'    => 180,
        'status'      => 'available',
        'is_featured' => 1,
        'amenities'   => 'Parking,Garden,Security,Water,Electricity,Children Play Area',
        'badge'       => 'POPULAR',
        'rating'      => 4.7,
        'rating_count'=> 14,
    ],
];

$imgSeeds = [
    1  => ['interior1','apartment1','room1'],
    2  => ['luxury1','kololo1','apartment2'],
    3  => ['hostel1','room2','dormitory1'],
    4  => ['flat1','apartment3','interior2'],
    5  => ['lakeview1','rooftop1','luxury2'],
    6  => ['selfcontained1','small1','studio2'],
    7  => ['hostel2','shared1','budget1'],
    8  => ['kabalagala1','apartment4','night1'],
    9  => ['naguru1','studio3','hilltop1'],
    10 => ['naalya1','house1','garden1'],
];

$stmtProp = $pdo->prepare("
    INSERT INTO properties
        (title,type,description,location,district,address,price,price_period,
         bedrooms,bathrooms,area_sqm,status,is_featured,amenities,badge,rating,rating_count)
    VALUES
        (:title,:type,:description,:location,:district,:address,:price,'month',
         :bedrooms,:bathrooms,:area_sqm,:status,:is_featured,:amenities,:badge,:rating,:rating_count)
");

$stmtImg = $pdo->prepare("
    INSERT INTO property_images (property_id, image_path, is_primary)
    VALUES (:property_id, :image_path, :is_primary)
");

foreach ($properties as $idx => $prop) {
    $stmtProp->execute($prop);
    $propertyId = (int)$pdo->lastInsertId();

    $seeds = $imgSeeds[$idx + 1] ?? ['property' . ($idx + 1)];
    foreach ($seeds as $i => $seed) {
        $stmtImg->execute([
            'property_id' => $propertyId,
            'image_path'  => "https://picsum.photos/seed/{$seed}/800/500",
            'is_primary'  => $i === 0 ? 1 : 0,
        ]);
    }
}

// Sample inquiries
$inquiries = [
    [1, 'Joseph Mwesigwa',   'joseph@example.com', '+256701234567', 'I am interested in this studio. Is it still available? When can I view it?'],
    [2, 'Sarah Nakato',      'sarah@example.com',  '+256782345678', 'Can the lease start from next month? I am relocating from Jinja.'],
    [3, 'Patrick Ssemanda',  'pat@example.com',    '+256753456789', 'Do you allow students to pay semester-by-semester?'],
    [5, 'Amina Nabukenya',   'amina@example.com',  '+256774567890', 'The lake view property looks perfect. Please send more photos.'],
];

$stmtInq = $pdo->prepare("
    INSERT INTO inquiries (property_id, name, email, phone, message)
    VALUES (:pid, :name, :email, :phone, :msg)
");
foreach ($inquiries as [$pid, $name, $email, $phone, $msg]) {
    $stmtInq->execute(['pid' => $pid, 'name' => $name, 'email' => $email, 'phone' => $phone, 'msg' => $msg]);
}

// Sample ratings
$reviews = [
    [1, 'Daniel Okello',   5, 'Excellent location and very responsive broker. Moved in within a week!'],
    [1, 'Grace Auma',      4, 'Good value for Nakasero. Quiet and clean. Highly recommended.'],
    [2, 'Robert Ssali',    5, 'Best apartment I have ever rented in Kampala. The views are stunning.'],
    [3, 'Mercy Namirembe', 4, 'Clean hostel, fast Wi-Fi, and the caretaker is very helpful.'],
    [5, 'Tendo Kavuma',    5, 'Waking up to lake views every morning is priceless. Truly luxury.'],
    [10,'Fatuma Nabirye',  5, 'Family-friendly and spacious. Kids love the garden.'],
];

$stmtRat = $pdo->prepare("
    INSERT INTO ratings (property_id, name, rating, review) VALUES (:pid, :name, :rating, :review)
");
foreach ($reviews as [$pid, $name, $rating, $review]) {
    $stmtRat->execute(['pid' => $pid, 'name' => $name, 'rating' => $rating, 'review' => $review]);
}

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>PearlNest Setup</title>';
echo '<style>body{font-family:sans-serif;max-width:600px;margin:60px auto;padding:20px;}
.ok{color:#16a34a;}.info{color:#1d4ed8;}</style></head><body>';
echo '<h1>&#127968; PearlNest — Setup Complete</h1>';
echo '<p class="ok">&#10003; Database <b>pearlnest</b> created successfully.</p>';
echo '<p class="ok">&#10003; All tables created.</p>';
echo '<p class="ok">&#10003; ' . count($properties) . ' sample properties inserted.</p>';
echo '<p class="info"><b>Admin credentials:</b><br>URL: <a href="http://localhost/lyton/public/admin/login">http://localhost/lyton/public/admin/login</a><br>';
echo 'Username: <code>admin</code> &nbsp; Password: <code>PearlNest2024</code></p>';
echo '<p class="info"><b>Public site:</b> <a href="http://localhost/lyton/public">http://localhost/lyton/public</a></p>';
echo '<p style="color:#dc2626;">&#9888; Delete or rename this file after setup!</p>';
echo '</body></html>';
