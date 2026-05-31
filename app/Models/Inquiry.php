<?php

namespace App\Models;

use App\Core\Model;

class Inquiry extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO inquiries (property_id, name, email, phone, message)
            VALUES (:property_id, :name, :email, :phone, :message)
        ");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt   = $this->db->prepare("
            SELECT i.*, p.title AS property_title
            FROM inquiries i
            LEFT JOIN properties p ON i.property_id = p.id
            ORDER BY i.created_at DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit',  $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAll(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
    }

    public function countPending(): int
    {
        return (int)$this->db->query("SELECT COUNT(*) FROM inquiries WHERE status = 'pending'")->fetchColumn();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT i.*, p.title AS property_title
            FROM inquiries i
            LEFT JOIN properties p ON i.property_id = p.id
            WHERE i.id = :id
        ");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare("UPDATE inquiries SET status = :status WHERE id = :id");
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare("DELETE FROM inquiries WHERE id = :id")
                        ->execute([':id' => $id]);
    }
}
