<?php
// Include headers
require_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Category.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate category object
$category = new Category($db);

// Category read query
$result = $category->read();

// Get row count
$num = $result->rowCount();

// Check if any categories
if ($num > 0) {
  // Cat array
  $categories_arr = array();
  $categories_arr['data'] = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    extract($row);

    $categories_item = array(
      'id' => $id,
      'category' => $category
    );

    // Push to "data"
    array_push($categories_arr['data'], $categories_item);
  }

  // Turn to JSON & output
  echo json_encode($categoriesh_arr);

} else {
  // No Categories
  echo json_encode((["message" => "No category Found"]));
}
