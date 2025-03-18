<?php
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');
  header('Access-Control-Allow-Methods: POST');
  header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization,X-Requested-With');

  include_once '../../config/Database.php';
  include_once '../../models/Quote.php';
  
  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate blog post object
  $quote = new Quote($db);

  // Get raw posted data
  $data = json_decode(file_get_contents("php://input"));

  $quote->id = $data->id;
  $quote->quote = $data->quote;
  $quote->author_id = $data->author_id;
  $quote->category_id = $data->category_id;

// Validate data
if(empty($data->quote) || empty($data->author_id) || empty($data->category_id)) 
{
  echo json_encode(['message' => 'Missing Required Data'], JSON_PRETTY_PRINT);
  exit();
}

// Proceed with creating the quote...
// Create Quote
if($quote->create()) 
{
  echo json_encode(['message' => 'Quote Created'], JSON_PRETTY_PRINT);
} 

else 
{
  echo json_encode(['message' => 'Quote Not Created'], JSON_PRETTY_PRINT);
}