<?php
/**
 * API Front Controller (Single Entry Point)
 * All requests to /api/* are routed through this file
 * 
 * Apache .htaccess rewrites all requests to this file.
 * The router then matches the HTTP method + URI to a controller action.
 */

// ─── CORS Headers ───────────────────────────────────────
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ─── Load Dependencies ──────────────────────────────────
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/helpers/Response.php';
require_once __DIR__ . '/helpers/JWTHelper.php';

// ─── Parse Request ──────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];

// Get the request URI relative to /api/
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';

// Remove query string
$uri = parse_url($requestUri, PHP_URL_PATH);

// Remove base path prefix
if (str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath));
}

// Clean up the URI
$uri = '/' . trim($uri, '/');

// ─── Route Definitions ──────────────────────────────────
// Format: [METHOD, URI Pattern, Controller, Action]
// {id} is replaced with a regex capture group
$routes = [
    // Auth
    ['POST',   '/auth/register',  'AuthController',    'register'],
    ['POST',   '/auth/login',     'AuthController',    'login'],

    // Products
    ['GET',    '/products',       'ProductController',  'index'],
    ['GET',    '/products/{id}',  'ProductController',  'show'],
    ['POST',   '/products',       'ProductController',  'store'],
    ['PUT',    '/products/{id}',  'ProductController',  'update'],
    ['DELETE', '/products/{id}',  'ProductController',  'destroy'],

    // Cart
    ['GET',    '/cart',            'CartController',    'index'],
    ['POST',   '/cart',            'CartController',    'store'],
    ['PUT',    '/cart/{id}',       'CartController',    'update'],
    ['DELETE', '/cart/{id}',       'CartController',    'destroy'],

    // Orders
    ['GET',    '/orders',          'OrderController',   'index'],
    ['GET',    '/orders/{id}',     'OrderController',   'show'],
    ['POST',   '/orders',          'OrderController',   'store'],
    ['PUT',    '/orders/{id}',     'OrderController',   'update'],

    // Users (Admin)
    ['GET',    '/users',           'UserController',    'index'],
    ['DELETE', '/users/{id}',      'UserController',    'destroy'],
];

// ─── Route Matching ─────────────────────────────────────
$matched = false;

foreach ($routes as [$routeMethod, $routePattern, $controllerName, $action]) {
    // Convert {id} to regex
    $regex = preg_replace('/\{(\w+)\}/', '(\d+)', $routePattern);
    $regex = '#^' . $regex . '$#';

    if ($method === $routeMethod && preg_match($regex, $uri, $matches)) {
        $matched = true;

        // Load controller
        $controllerFile = __DIR__ . '/controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            Response::serverError("Controller not found: $controllerName");
        }

        require_once $controllerFile;
        $controller = new $controllerName();

        // Call action with captured parameters (e.g., {id})
        if (count($matches) > 1) {
            $params = array_slice($matches, 1);
            $params = array_map('intval', $params);
            call_user_func_array([$controller, $action], $params);
        } else {
            $controller->$action();
        }

        break;
    }
}

// ─── 404 Not Found ──────────────────────────────────────
if (!$matched) {
    Response::error("Endpoint not found: $method $uri", 404);
}
