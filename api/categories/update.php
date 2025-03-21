<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$database = new Database();
$db = $database->connect();
$category = new Category($db);
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->category))
{
    echo json_encode(["message" => "Missing Required Parameters"]);
    exit();
}

$category->id = $data->id;
$category->category = $data->category;

if ($category->update())
{
    echo json_encode([
        "id" => $category->id,
        "category" => $category->category
    ]);
}
else
{
    echo json_encode(["message" => "Unable to update category"]);
}

exit();