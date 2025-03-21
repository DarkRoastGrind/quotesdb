<?php
// Include headers
require_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Category.php';
// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$category = new Category($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Set ID to UPDATE
$category->id = $data->id;

// Delete post
if ($category->delete()) {
  echo json_encode((["message" => "Category deleted"]));
} else {
  echo json_encode((["message" => "Category not deleted"]));
}
