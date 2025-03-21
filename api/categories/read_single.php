<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();
$category = new Category($db);

if (!isset($_GET['id']) || empty($_GET['id']))
{
  echo json_encode(["message" => "category_id Not Found"]);
  exit();
}

$category->id = (int) $_GET['id'];
$category->read_single();

if (isset($category->id) && isset($category->category))
{
  echo json_encode([
    "id" => $category->id,
    "category" => $category->category
  ]);
}
else
{
  echo json_encode(["message" => "category_id Not Found"]);
}

exit();