<?php
    // Include headers
    require_once '../../config/headers.php';

// Include necessary files
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate Quote object
$quote = new Quote($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if ID is provided
if (empty($data->id)) {
    // Ensure 'id' field is included in the response (null if missing)
    echo json_encode(["id" => null, "message" => "No Quotes Found"]);
    exit();
}

// Set ID for deletion
$quote->id = (int) $data->id;


// Check if the quote exists before attempting to delete
$quoteExistsQuery = 'SELECT id FROM quotes WHERE id = :id';
$stmt = $db->prepare($quoteExistsQuery);
$stmt->bindParam(':id', $quote->id);
$stmt->execute();

if ($stmt->rowCount() == 0) 
{
  // Quote does not exist, but return an 'id' field
  echo json_encode(["id" => $quote->id, "message" => "No Quotes Found"]);
  exit();
}

// Attempt to delete the quote
if ($quote->delete()) {
    // Return 'id' on success
    echo json_encode(['id' => $quote->id]);
    exit();
} else {
    // Return 'id' even in case of failure
    echo json_encode(['id' => $quote->id, 'message' => 'Quote Not Deleted']);
    exit();
}
