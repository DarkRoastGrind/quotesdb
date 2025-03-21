<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

$category = new Category($db);

$result = $category->read();
$num = $result->rowCount();

if ($num > 0) {
  $cat_arr = [];

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $cat_arr[] = [
      'id' => $row['id'],
      'category' => $row['category']
    ];
  }

  echo json_encode($cat_arr);
} else {
  echo json_encode(["message" => "No category Found"]);
}

exit();