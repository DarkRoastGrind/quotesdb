<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Quote.php'; 
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $quote = new Quote($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  if (!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id)) {
      echo json_encode(["message" => "Missing required fields"]);
      exit();
  }
  
  $quote->quote = trim($data->quote);
  $quote->author_id = (int) $data->author_id;
  $quote->category_id = (int) $data->category_id;

    // Create quote
    $quote->create();

