<?php

namespace App\Models;

use App\Core\Model;

class Admin extends Model
{
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function verifyPassword(array $admin, string $password): bool
    {
        return password_verify($password, $admin['password']);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $stmt = $this->db->prepare("UPDATE admins SET password = :password WHERE id = :id");
        return $stmt->execute([':password' => password_hash($newPassword, PASSWORD_DEFAULT), ':id' => $id]);
    }

    public function updateProfile(int $id, array $data): bool
    {
        $stmt = $this->db->prepare("UPDATE admins SET name = :name, email = :email WHERE id = :id");
        return $stmt->execute([':name' => $data['name'], ':email' => $data['email'], ':id' => $id]);
    }
}
