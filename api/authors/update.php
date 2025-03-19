<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: PUT');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Author.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $author = new Author($db);

// Get raw posted data
$data = json_decode(file_get_contents("php://input"));

// Check if all required parameters are provided
if (!isset($data->id) || !isset($data->author)) 
{
    echo json_encode(["message" => "Missing Required Parameters"]);
    exit();
}

// Update quote
$author->update();