<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();
$author = new Author($db);
$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || empty($data->id))
{
  echo json_encode(["message" => "Author not deleted"]);
  exit();
}

$author->id = (int) $data->id;

if ($author->delete())
{
  echo json_encode(["message" => "Author deleted"]);
}
else
{
  echo json_encode(["message" => "Author not deleted"]);
}

exit();