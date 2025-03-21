<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();
$author = new Author($db);

if (!isset($_GET['id']) || empty($_GET['id']))
{
  echo json_encode(["message" => "author_id Not Found"]);
  exit();
}

$author->id = (int) $_GET['id'];
$author->read_single();

if (isset($author->id) && isset($author->author))
{
  echo json_encode([
    "id" => $author->id,
    "author" => $author->author
  ]);
}

else
{
  echo json_encode(["message" => "author_id Not Found"]);
}

exit();