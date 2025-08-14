<?php
class Router {
    private $routes = [];
    
    public function add($path, $controller, $method) {
        $this->routes[$path] = ['controller' => $controller, 'method' => $method];
    }
    
    public function route($url) {
        // Nettoyer l'URL
        $url = strtok($url, '?'); // Remove query parameters
        $url = rtrim($url, '/'); // Remove trailing slash
        if (empty($url)) $url = '/';
        
        if (isset($this->routes[$url])) {
            $route = $this->routes[$url];
            if (file_exists($route['controller'])) {
                require_once $route['controller'];
                $controllerName = $this->getControllerName($route['controller']);
                $controller = new $controllerName();
                $method = $route['method'];
                $controller->$method();
            } else {
                $this->notFound();
            }
        } else {
            // Try to match dynamic routes
            if (preg_match('/^\/service\/(\d+)$/', $url, $matches)) {
                require_once 'controllers/ServiceController.php';
                $controller = new ServiceController();
                $controller->show($matches[1]);
            } elseif (preg_match('/^\/admin\/message\/(\d+)$/', $url, $matches)) {
                require_once 'controllers/AdminController.php';
                $controller = new AdminController();
                $controller->messageDetail();
            } elseif ($url === '/' || $url === '') {
                require_once 'controllers/HomeController.php';
                $controller = new HomeController();
                $controller->index();
            } else {
                $this->notFound();
            }
        }
    }
    
    private function getControllerName($path) {
        $fileName = basename($path, '.php');
        return $fileName;
    }
    
    private function notFound() {
        http_response_code(404);
        echo "Page not found";
    }
}
?>