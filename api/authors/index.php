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
require_once '../../models/Author.php';
require_once '../../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Author model
$author = new Author($db);

// Set up the response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['id'])) {
        // Get a specific author by ID
        $author->id = $_GET['id'];
        $author->read_single();
        
        if ($author->id) {
            echo json_encode([
                'id' => $author->id,
                'author' => $author->author
            ]);
        } else {
            echo json_encode(["message" => "author_id Not Found"]);
        }
    } else {
        // Get all authors
        $result = $author->read();
        $authors_arr = [];
        
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $authors_arr[] = [
                'id' => $id,
                'name' => $name
            ];
        }
        
        echo json_encode($authors_arr);
    }
} else {
    echo json_encode(["message" => "Method not allowed"]);
}
