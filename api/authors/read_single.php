<?php
// Include headers
include_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Author.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog category object
$author = new Author($db);

// Validate and assign ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo json_encode(["message" => "author_id Not Found"]);
  exit();
}

$author->id = (int) $_GET['id'];

// Get post
$author->read_single();

// Ensure data exists
if (isset($author->id) && isset($author->author)) {
  echo json_encode([
      "id" => $author->id,
      "author" => $author->author
  ]);
} else {
  echo json_encode(["message" => "author_id Not Found"]);
}

exit();