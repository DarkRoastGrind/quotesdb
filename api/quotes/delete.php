<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

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
if (empty($data->id)) 
{
    // Include the 'id' field in the response (null if missing)
    echo json_encode(["id" => null, "message" => "No Quotes Found"]);
    exit();
}

// Set ID for deletion
$quote->id = $data->id;

// Attempt to delete the quote
if ($quote->delete()) 
{
    // Return the 'id' field on success
    echo json_encode(['id' => $quote->id]);
    exit();
} 

else 
{
    // Include the 'id' field even in case of failure
    echo json_encode(['id' => $quote->id, 'message' => 'Quote Not Deleted']);
    exit();
}
