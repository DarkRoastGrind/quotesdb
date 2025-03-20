<?php
    // Include headers
    require_once '../../config/headers.php';

  include_once '../../config/Database.php';
  include_once '../../models/Category.php';
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $category = new Category($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  // Check if all required parameters are provided
  if (!isset($data->id) || !isset($data->category)) 
  {
      echo json_encode(["message" => "Missing Required Parameters"]);
      exit();
  }

  // Update category
  if ($category->update()) 
  {
      echo json_encode([
          "id" => $category->id,
          "category" => $category->category
      ]);
  } 

  else 
  {
      echo json_encode(["message" => "Unable to update category"]);
  }

  exit();