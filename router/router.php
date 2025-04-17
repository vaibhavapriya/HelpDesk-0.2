<?php
require_once __DIR__ . '/../controller/HomeController.php'; 
require_once __DIR__ . '/../controller/UserController.php'; 
require_once __DIR__ . '/../controller/TicketController.php'; 
require_once __DIR__ . '/../controller/AdminController.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../services/AuthMiddleware.php';
require_once __DIR__ . '/../services/AuthRole.php';
require_once __DIR__ . '/../services/CheckTicket.php';
require_once __DIR__ . '/../services/Logger.php';

use app\services\Logger;
use app\controller\HomeController;
use app\controller\UserController;
use app\controller\TicketController;
use app\controller\AdminController;
use app\services\AuthMiddleware;
use app\services\AuthRole;
use app\services\CheckTicket;

class Router {
    private array $routes = [];
    private string $basePath = '';
    private Logger $logger;
    private $db;
    private $database;

    public function __construct(string $basePath = '') {
        $this->basePath = $basePath;
        $database = new Database();
        $this->db = $database->connect();
        $this->logger = new Logger($this->db);
    }

    public function add(string $path, string $controllerClass, string $method, array $middleware = []): void {
        $this->routes[$path] = [
            'controller' => $controllerClass,
            'method' => $method,
            'middleware' => $middleware
        ];
    }

    private function removeBasePath(string $path): string
    {
        if (strpos($path, $this->basePath) === 0) {
            $path = substr($path, strlen($this->basePath));
        }
        return $path ?: '/';
    }

    public function dispatch(string $uri, string $httpMethod): void {

        try {
            $path = parse_url($uri, PHP_URL_PATH);
            $path = $this->removeBasePath($path);
        
            if (!array_key_exists($path, $this->routes)) {
                http_response_code(404);
                echo "404 - Page Not Found<br>";
                echo "PATH: $path<br>";
                $msg = "404 - Route not found for path: $path";
                $this->logger->log($msg, __FILE__, __LINE__);
                return;
            }
        
            $route = $this->routes[$path];
    
        
            // Execute middleware
            foreach ($route['middleware'] as $middlewareClass) {
                if (class_exists($middlewareClass)) {
                    $middleware = new $middlewareClass($this->db);
        
                    if (method_exists($middleware, 'handle')) {
                        $middleware->handle();
                    } elseif (is_callable($middleware)) {
                        $middleware();
                    } else {
                        http_response_code(500);
                        $msg = "500 - Middleware '$middlewareClass' is not callable.";
                        $this->logger->log($msg, __FILE__, __LINE__);
                        echo "$msg";
                        return;
                    }
                } else {
                    http_response_code(500);
                    echo "500 - Middleware class '$middlewareClass' not found.";
                    return;
                }
            }
        
            // Load controller and call method
            $controllerClass = $route['controller'];
            $method = $route['method'];
        
            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "500 - Controller '$controllerClass' not found.";
                return;
            }

            $controller = new $controllerClass($this->db);
        
            if (!method_exists($controller, $method)) {
                http_response_code(500);
                $msg = "500 - Method '$method' not found in '$controllerClass'.";
                $this->logger->log($msg, __FILE__, __LINE__);
                echo "$msg";
                return;
            }
            call_user_func([$controller, $method]);

        } catch (Exception $e) {
            http_response_code(500);
            $this->logger->log($e->getMessage(), __FILE__, __LINE__);
            echo "500 - Internal Server Error";
        }
    }
}

