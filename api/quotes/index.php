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
require_once '../../models/Quote.php';
require_once '../../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Quote model
$quote = new Quote($db);

// Set up the response headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $conditions = [];
    
    // Filter by ID
    if (isset($_GET['id'])) {
        $quote->id = $_GET['id'];
        $conditions[] = "id = " . $quote->id;
    }
    
    // Filter by author ID
    if (isset($_GET['author_id'])) {
        $quote->author_id = $_GET['author_id'];
        $conditions[] = "author_id = " . $quote->author_id;
    }
    
    // Filter by category ID
    if (isset($_GET['category_id'])) {
        $quote->category_id = $_GET['category_id'];
        $conditions[] = "category_id = " . $quote->category_id;
    }

    // Build the query
    $query = 'SELECT * FROM quotes';
    if (count($conditions) > 0) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    
    // Execute the query
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $quotes_arr = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $quotes_arr[] = [
            'id' => $id,
            'quote' => $quote,
            'author_id' => $author_id,
            'category_id' => $category_id
        ];
    }
    
    // Return results or a "No Quotes Found" message
    if (empty($quotes_arr)) {
        echo json_encode(["message" => "No Quotes Found"]);
    } else {
        echo json_encode($quotes_arr);
    }
} else {
    echo json_encode(["message" => "Method not allowed"]);
}

