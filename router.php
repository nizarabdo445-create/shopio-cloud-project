<?php
/**
 * PHP Built-in Server Router
 * Only used for local development (php -S)
 * In production, Nginx handles routing
 */

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Route /api/* requests to the API front controller
if (str_starts_with($path, '/api/') || $path === '/api') {
    require __DIR__ . '/api/index.php';
    return true;
}

// Serve static files (images, CSS, JS)
if (file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
    return false; // Let PHP built-in server handle it
}

// Default: return 404
http_response_code(404);
echo json_encode(['error' => 'Not found']);
