<?php
include_once '../../authors/init.php';

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