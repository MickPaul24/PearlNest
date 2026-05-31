<?php

namespace App\Core;

use PDO;

abstract class Model
{
    protected PDO $db;

    public function __construct()
    {
        static $pdo = null;
        if ($pdo === null) {
            $config   = require __DIR__ . '/../../config/config.php';
            $database = new Database($config['db']);
            $pdo      = $database->getConnection();
        }
        $this->db = $pdo;
    }
}
