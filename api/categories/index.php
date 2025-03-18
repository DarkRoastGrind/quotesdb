<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}
// Include necessary models and database connection
require_once '../../models/Category.php';
require_once '../../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Category model
$category = new Category($db);

// Set up the response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Get a specific category by ID
        $category->id = $_GET['id'];
        $category->read_single();
        
        if ($category->id) {
            echo json_encode([
                'id' => $category->id,
                'category' => $category->category
            ]);
        } else {
            echo json_encode(["message" => "category_id Not Found"]);
        }
    } else {
        // Get all categories
        $result = $category->read();
        $categories_arr = [];
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $categories_arr[] = [
                'id' => $id,
                'category' => $category
            ];
        }
        
        echo json_encode($categories_arr);
    }
} else {
    echo json_encode(["message" => "Method not allowed"]);
}
