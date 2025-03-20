<?php
    // Include headers
    require_once '../../config/headers.php';

    include_once '../../config/Database.php';
    include_once '../../models/Author.php';

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate author object
    $author = new Author($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Check if all required parameters are provided
    if (!isset($data->id) || !isset($data->author)) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Update author
    if ($author->update()) 
    {
        echo json_encode([
            "id" => $author->id,
            "author" => $author->author
        ]);
    } 

    else 
    {
        echo json_encode(["message" => "Unable to update author"]);
    }

    exit();