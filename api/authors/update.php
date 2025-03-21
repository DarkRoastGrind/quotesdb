<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

$author = new Author($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->id) || !isset($data->author)) {
    echo json_encode(["message" => "Missing Required Parameters"]);
    exit();
}

$author->id = $data->id;
$author->author = $data->author;

if ($author->update()) {
    echo json_encode([
        "id" => $author->id,
        "author" => $author->author
    ]);
} else {
    echo json_encode(["message" => "Unable to update author"]);
}

exit();