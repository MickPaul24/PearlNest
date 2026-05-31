<?php

namespace App\Models;

class User
{
    public function getSampleUser(): array
    {
        return [
            'name' => 'Lyton User',
            'email' => 'user@example.com',
            'role' => 'Visitor',
        ];
    }
}
