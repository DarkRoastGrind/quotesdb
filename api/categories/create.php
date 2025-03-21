<?php
// Include headers
include_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Category.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate blog post object
$category = new Category($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->category) || empty(trim($data->category))) {
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

$category->category = trim($data->category);

// Create category
if ($category->create()) {
    echo json_encode(["message" => "Category Created"]);
} else {
    echo json_encode(["message" => "Unable to create category"]);
}

exit();


