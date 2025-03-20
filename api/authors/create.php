<?php
    // Include headers
    include_once '../../config/headers.php';

    // Include necessary files.
    include_once '../../config/Database.php';
    include_once '../../models/Author.php';
    
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog post object
    $author = new Author($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"),true);

    // Validate input
    if (!isset($data['author']) || empty(trim($data['author']))) 
    {
        echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        exit();
    }

    $author->author = trim($data->author);
    
    // Set author data
    $author->author = trim($data['author']);

    // Create author
    if ($author->create()) 
    {
        echo json_encode(["status" => "success", "message" => "Author Created", "id" => $author->id]);
    } 
    
    else 
    {
        echo json_encode(["status" => "error", "message" => "Unable to create author"]);
    }

    exit();


