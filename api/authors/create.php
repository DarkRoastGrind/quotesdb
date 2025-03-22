<?php
include_once '../init.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->author) || empty(trim($data->author)))
{
    echo json_encode(["message" => "Missing required fields"]);
    exit();
}

$author->author = trim($data->author);

if ($author->create())
{
    echo json_encode(["message" => "Author Created"]);
}
else
{
    echo json_encode(["message" => "Unable to create author"]);
}

exit();


