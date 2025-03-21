<?php
// Include headers
require_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$quote = new Quote($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
  echo json_encode(["message" => "Missing required fields"]);
  exit();
}

// Create quote
$quote->create();

