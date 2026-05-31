<?php

namespace App\Models;

use App\Core\Model;

class Rating extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO ratings (property_id, name, rating, review)
            VALUES (:property_id, :name, :rating, :review)
        ");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getByPropertyId(int $propertyId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM ratings WHERE property_id = :pid ORDER BY created_at DESC
        ");
        $stmt->execute([':pid' => $propertyId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countByPropertyId(int $propertyId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM ratings WHERE property_id = :pid");
        $stmt->execute([':pid' => $propertyId]);
        return (int)$stmt->fetchColumn();
    }
}
