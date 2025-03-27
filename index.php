<?php
declare(strict_types = 1);
// Enable CORS and set Content-Type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Get the HTTP request method and request URI
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// Handle OPTIONS requests (preflight CORS requests)
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Remove query string (if any) from the request URI
$request_uri = strtok($request_uri, '?');

// Parse the request URI to determine the endpoint
$segments = explode('/', trim($request_uri, '/')); // Split URI into segments

echo "index.php is being executed<br>";
// Extract the first segment as the resource type (e.g., 'authors', 'quotes', 'categories')
if (!isset($segments[1]) || $segments[1] === '') {
    header('Content-Type: text/html');
    readfile(__DIR__ . '/index.html');
    exit();
}

$resource = $segments[1];

// Define base paths for resources
$base_path = __DIR__ . '/api';

// Route the request based on the resource (authors, quotes, categories)
switch ($resource) {
    case 'authors':
        require_once $base_path . '/authors/index.php';
        break;
    case 'quotes':
        require_once $base_path . '/quotes/index.php';
        break;
    case 'categories':
        require_once $base_path . '/categories/index.php';
        break;
    default:
        // If the resource is not found, return a 404 error
        echo json_encode(["message" => "Resource not found"]);
        break;
}