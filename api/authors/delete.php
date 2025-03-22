<?php
include_once '../../authors/init.php';

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