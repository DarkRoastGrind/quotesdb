<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Quote object
$quote = new Quote($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if all required parameters are provided
if (!isset($data->id) || !isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
    echo json_encode(["message" => "Missing Required Parameters"]);
    exit();
}

// Assign values
$quote->id = (int) $data->id;
$quote->quote = trim($data->quote);
$quote->author_id = (int) $data->author_id;
$quote->category_id = (int) $data->category_id;

// Check if quote exists before updating
$query = "SELECT id FROM quotes WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $quote->id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(["message" => "No Quotes Found"]);
    exit();
}

// Check if author exists
$query = "SELECT id FROM authors WHERE id = :author_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':author_id', $quote->author_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(["message" => "author_id Not Found"]);
    exit();
}

// Check if category exists
$query = "SELECT id FROM categories WHERE id = :category_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':category_id', $quote->category_id);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo json_encode(["message" => "category_id Not Found"]);
    exit();
}

// Attempt to update the quote
if ($quote->update()) {
    echo json_encode([
        "id" => $quote->id,
        "quote" => $quote->quote,
        "author_id" => $quote->author_id,
        "category_id" => $quote->category_id
    ]);
} else {
    echo json_encode(["message" => "Quote Not Updated"]);
}

exit();