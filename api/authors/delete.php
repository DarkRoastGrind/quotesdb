<?php
    // Include headers
    require_once '../../config/headers.php';

    include_once '../../config/Database.php';
    include_once '../../models/Author.php';
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog post object
    $author = new Author($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to UPDATE
    $author->id = $data->id;

    // Delete post
    if($author->delete()) 
    {
      echo json_encode((["message" => "Author deleted"]));
    } 

    else 
    {
      echo json_encode((["message" => "Author not deleted"]));
    }
