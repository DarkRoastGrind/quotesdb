<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();

$category = new Category($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || empty($data->id)) {
  echo json_encode(["message" => "Category not deleted"]);
  exit();
}

$category->id = $data->id;

if ($category->delete()) {
  echo json_encode((["message" => "Category deleted"]));
} else {
  echo json_encode((["message" => "Category not deleted"]));
}

exit();