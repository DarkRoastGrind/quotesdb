<?php
// Enable CORS and set response type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Handle OPTIONS request (CORS preflight)
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

// Handle GET requests
if ($method === 'GET') {
    $conditions = [];
    
    // Filter by ID
    if (isset($_GET['id'])) {
        $quote->id = $_GET['id'];
        $stmt = $quote->read_single(); // Assuming this method fetches a single quote by ID

        if ($stmt) {
            echo json_encode([
                'id' => $quote->id,
                'quote' => $quote->quote,
                'author' => $quote->author_id,
                'category' => $quote->category_id
            ]);
        } else {
            echo json_encode(["message" => "No Quotes Found"]);
        }
        exit();
    }
    
    // Filter by author ID
    if (isset($_GET['author'])) {
        $quote->author_id = $_GET['author'];
        $conditions[] = "author_id = " . $quote->author_id;
    }

    // Filter by category ID
    if (isset($_GET['category'])) {
        $quote->category_id = $_GET['category'];
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
            'author' => $author_id,
            'category' => $category_id
        ];
    }

    // Return results or a "No Quotes Found" message
    if (empty($quotes_arr)) {
        echo json_encode(["message" => "No Quotes Found"]);
    } else {
        echo json_encode($quotes_arr);
    }
    exit();
}

// Handle POST requests (Create a new Quote)
if ($method === 'POST') {
    // Ensure required parameters are present
    if (!isset($_POST['quote']) || !isset($_POST['author_id']) || !isset($_POST['category_id'])) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set Quote data
    $quote->quote = $_POST['quote'];
    $quote->author_id = $_POST['author_id'];
    $quote->category_id = $_POST['category_id'];

    // Attempt to create the quote
    if ($quote->create()) {
        echo json_encode([
            'id' => $quote->id,
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
        ]);
    } else {
        echo json_encode(["message" => "Unable to create quote"]);
    }
    exit();
}

// Handle PUT requests (Update an existing Quote)
if ($method === 'PUT') {
    // Ensure required parameters are present
    parse_str(file_get_contents("php://input"), $_PUT); // Parse the raw PUT data

    if (!isset($_PUT['id']) || !isset($_PUT['quote']) || !isset($_PUT['author_id']) || !isset($_PUT['category_id'])) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set updated Quote data
    $quote->id = $_PUT['id'];
    $quote->quote = $_PUT['quote'];
    $quote->author_id = $_PUT['author_id'];
    $quote->category_id = $_PUT['category_id'];

    // Attempt to update the quote
    if ($quote->update()) {
        echo json_encode([
            'id' => $quote->id,
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
        ]);
    } else {
        echo json_encode(["message" => "No Quotes Found"]);
    }
    exit();
}

// Handle DELETE requests (Delete a Quote)
if ($method === 'DELETE') {
    // Ensure required ID is present
    parse_str(file_get_contents("php://input"), $_DELETE); // Parse the raw DELETE data

    if (!isset($_DELETE['id'])) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set Quote ID for deletion
    $quote->id = $_DELETE['id'];

    // Attempt to delete the quote
    if ($quote->delete()) {
        echo json_encode(['id' => $quote->id]);
    } else {
        echo json_encode(["message" => "No Quotes Found"]);
    }
    exit();
}

// If the request method is not allowed
echo json_encode(["message" => "Method not allowed"]);
