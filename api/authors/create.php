<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

$author = new Author($db);

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->author) || empty(trim($data->author))) {
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

$author->author = trim($data->author);

if ($author->create()) {
    echo json_encode(["message" => "Author Created"]);
} else {
    echo json_encode(["message" => "Unable to create author"]);
}

exit();


