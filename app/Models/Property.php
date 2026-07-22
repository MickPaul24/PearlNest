<?php

namespace App\Models;

use App\Core\Model;

class Property extends Model
{
    private static array $typeLabels = [
        'hostel_shared'  => 'Hostel (Shared Room)',
        'hostel_private' => 'Hostel (Private Room)',
        'studio'         => 'Studio Apartment',
        '1br'            => '1 Bedroom Apartment',
        '2br'            => '2 Bedroom Apartment',
        '3br'            => '3 Bedroom Apartment',
        'self_contained' => 'Self-Contained Unit',
    ];

    public static function typeLabel(string $type): string
    {
        return self::$typeLabels[$type] ?? ucfirst($type);
    }

    public static function typeOptions(): array
    {
        return self::$typeLabels;
    }

    public function getAll(array $filters = [], int $page = 1, int $perPage = 9): array
    {
        [$where, $params] = $this->buildWhere($filters);

        $offset = ($page - 1) * $perPage;
        $order  = $this->buildOrder($filters['sort'] ?? '');

        $sql = "
            SELECT p.*,
                   (SELECT image_path FROM property_images WHERE property_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM properties p
            $where
            $order
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAll(array $filters = []): int
    {
        [$where, $params] = $this->buildWhere($filters);

        $sql  = "SELECT COUNT(*) FROM properties p $where";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    public function getFeatured(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*,
                   (SELECT image_path FROM property_images WHERE property_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM properties p
            WHERE p.is_featured = 1 AND p.status = 'available'
            /* Prefer recently-updated featured properties so admin-selected items appear immediately,
               then fall back to rating. */
            ORDER BY p.updated_at DESC, p.rating DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM properties WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $property = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$property) {
            return null;
        }

        $property['images']    = $this->getImages($id);
        $property['videos']    = $this->getVideos($id);
        $property['amenities'] = $property['amenities']
            ? array_map('trim', explode(',', $property['amenities']))
            : [];

        return $property;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO properties
                (title, type, description, location, district, address, price, price_period, touring_fee,
                 bedrooms, bathrooms, area_sqm, status, is_featured, amenities, badge)
            VALUES
                (:title, :type, :description, :location, :district, :address, :price, :price_period, :touring_fee,
                 :bedrooms, :bathrooms, :area_sqm, :status, :is_featured, :amenities, :badge)
        ");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $sets = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $stmt = $this->db->prepare("UPDATE properties SET $sets WHERE id = :id");
        $data[':id'] = $id;
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM properties WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE properties SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function incrementViews(int $id): void
    {
        $this->db->prepare("UPDATE properties SET views = views + 1 WHERE id = :id")
                 ->execute([':id' => $id]);
    }

    public function addImage(int $propertyId, string $imagePath, bool $isPrimary = false): int
    {
        if ($isPrimary) {
            $this->db->prepare("UPDATE property_images SET is_primary = 0 WHERE property_id = :pid")
                     ->execute([':pid' => $propertyId]);
        }
        $stmt = $this->db->prepare("
            INSERT INTO property_images (property_id, image_path, is_primary)
            VALUES (:pid, :path, :primary)
        ");
        $stmt->execute([':pid' => $propertyId, ':path' => $imagePath, ':primary' => (int)$isPrimary]);
        return (int)$this->db->lastInsertId();
    }

    public function deleteImage(int $imageId): bool
    {
        $stmt = $this->db->prepare("SELECT image_path FROM property_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && strpos($row['image_path'], 'uploads/') !== false) {
            $fullPath = __DIR__ . '/../../public/' . $row['image_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        $stmt = $this->db->prepare("DELETE FROM property_images WHERE id = :id");
        return $stmt->execute([':id' => $imageId]);
    }

    public function getImages(int $propertyId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM property_images WHERE property_id = :pid ORDER BY is_primary DESC, id ASC
        ");
        $stmt->execute([':pid' => $propertyId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getVideos(int $propertyId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM property_videos WHERE property_id = :pid");
        $stmt->execute([':pid' => $propertyId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addVideo(int $propertyId, string $videoPath, string $title = ''): void
    {
        $this->db->prepare("
            INSERT INTO property_videos (property_id, video_path, title)
            VALUES (:pid, :path, :title)
        ")->execute([':pid' => $propertyId, ':path' => $videoPath, ':title' => $title]);
    }

    public function deleteVideo(int $videoId): bool
    {
        $stmt = $this->db->prepare("SELECT video_path FROM property_videos WHERE id = :id");
        $stmt->execute([':id' => $videoId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($row && strpos($row['video_path'], 'uploads/') !== false) {
            $fullPath = __DIR__ . '/../../public/' . $row['video_path'];
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
        return $this->db->prepare("DELETE FROM property_videos WHERE id = :id")
                        ->execute([':id' => $videoId]);
    }

    public function updateRatingAvg(int $propertyId): void
    {
        $this->db->prepare("
            UPDATE properties p
            SET rating       = (SELECT COALESCE(AVG(rating), 0) FROM ratings WHERE property_id = :pid1),
                rating_count = (SELECT COUNT(*) FROM ratings WHERE property_id = :pid2)
            WHERE id = :id
        ")->execute([':pid1' => $propertyId, ':pid2' => $propertyId, ':id' => $propertyId]);
    }

    public function getStats(): array
    {
        $row = $this->db->query("
            SELECT
                COUNT(*)                                                    AS total,
                SUM(status = 'available')                                   AS available,
                SUM(status = 'rented')                                      AS rented,
                SUM(status = 'under_review')                                AS under_review,
                SUM(is_featured = 1)                                        AS featured
            FROM properties
        ")->fetch(\PDO::FETCH_ASSOC);

        return $row ?: [];
    }

    public function getDistricts(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT district FROM properties WHERE district IS NOT NULL ORDER BY district");
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    private function buildWhere(array $filters): array
    {
        $conditions = [];
        $params     = [];

        if (!empty($filters['admin'])) {
            // Admin sees all statuses
        } else {
            $conditions[] = "p.status = 'available'";
        }

        if (!empty($filters['type'])) {
            $conditions[] = "p.type = :type";
            $params[':type'] = $filters['type'];
        }

        if (!empty($filters['district'])) {
            $conditions[] = "p.district = :district";
            $params[':district'] = $filters['district'];
        }

        if (!empty($filters['min_price'])) {
            $conditions[] = "p.price >= :min_price";
            $params[':min_price'] = (float)$filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $conditions[] = "p.price <= :max_price";
            $params[':max_price'] = (float)$filters['max_price'];
        }

        if (!empty($filters['bedrooms'])) {
            $conditions[] = "p.bedrooms >= :bedrooms";
            $params[':bedrooms'] = (int)$filters['bedrooms'];
        }

        if (!empty($filters['min_rating'])) {
            $conditions[] = "p.rating >= :min_rating";
            $params[':min_rating'] = (float)$filters['min_rating'];
        }

        if (!empty($filters['search'])) {
            $conditions[] = "(p.title LIKE :search OR p.location LIKE :search OR p.district LIKE :search OR p.description LIKE :search)";
            $params[':search'] = '%' . $filters['search'] . '%';
        }

        if (!empty($filters['status'])) {
            $conditions[] = "p.status = :status";
            $params[':status'] = $filters['status'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return [$where, $params];
    }

    private function buildOrder(string $sort): string
    {
        return match($sort) {
            'price_asc'  => 'ORDER BY p.price ASC',
            'price_desc' => 'ORDER BY p.price DESC',
            'rating'     => 'ORDER BY p.rating DESC',
            'newest'     => 'ORDER BY p.created_at DESC',
            default      => 'ORDER BY p.is_featured DESC, p.rating DESC',
        };
    }
}
