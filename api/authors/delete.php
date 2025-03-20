<?php
    // Include headers
    require_once '../../config/headers.php';

    // Include necessary files
    require_once '../../config/Database.php';
    require_once '../../models/Author.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate Author model
    $author = new Author($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Validate input
    if (!isset($data->id) || empty($data->id)) 
    {
        echo json_encode(["message" => "Author not deleted"]);
        exit();
    }

    // Set ID for deletion
    $author->id = (int) $data->id;

    // Attempt to delete
    if ($author->delete()) 
    {
        echo json_encode(["message" => "Author deleted"]);
    } 

    else 
    {
        echo json_encode(["message" => "Author not deleted"]);
    }

    exit();