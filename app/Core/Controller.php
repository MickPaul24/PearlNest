<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        $config = require __DIR__ . '/../../config/config.php';
        $baseUrl = $config['app']['base_url'];
        $data['baseUrl']  = $baseUrl;
        $data['appName']  = $config['app']['name'];
        $data['flash']    = $_SESSION['flash'] ?? [];
        $_SESSION['flash'] = [];
        $basePath = rtrim(parse_url($baseUrl, PHP_URL_PATH), '/');
        $data['imgUrl'] = function(?string $path) use ($basePath): string {
            if (!$path) return '';
            return str_starts_with($path, 'http') ? $path : $basePath . '/' . $path;
        };

        extract($data, EXTR_SKIP);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    protected function redirect(string $path): void
    {
        $config  = require __DIR__ . '/../../config/config.php';
        $baseUrl = $config['app']['base_url'];
        header('Location: ' . $baseUrl . '/' . ltrim($path, '/'));
        exit;
    }

    protected function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    protected function isAdminLoggedIn(): bool
    {
        return isset($_SESSION['admin_id']);
    }

    protected function requireAdmin(): void
    {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('admin/login');
        }
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
