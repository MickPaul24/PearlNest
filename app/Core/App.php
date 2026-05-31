<?php

namespace App\Core;

class App
{
    protected array $params = [];

    public function __construct()
    {
        $url = $this->parseUrl();

        $controllerName = !empty($url[0]) ? ucfirst(array_shift($url)) . 'Controller' : 'HomeController';
        $methodName = !empty($url[0]) ? array_shift($url) : 'index';
        $this->params = $url;

        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            http_response_code(404);
            echo "Controller {$controllerName} not found.";
            exit;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $methodName)) {
            http_response_code(404);
            echo "Method {$methodName} not found in controller {$controllerName}.";
            exit;
        }

        call_user_func_array([$controller, $methodName], $this->params);
    }

    private function parseUrl(): array
    {
        $url = $_SERVER['REQUEST_URI'] ?? '';
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $path = preg_replace('#^' . preg_quote($basePath, '#') . '#', '', $url);
        $path = parse_url($path, PHP_URL_PATH);
        $path = trim($path, '/');

        return $path ? explode('/', $path) : [];
    }
}
