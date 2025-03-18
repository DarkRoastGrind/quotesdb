<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Quote.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate quote object
  $quote = new Quote($db);

  // quote read query
  $result = $quote->read();
  
  // Get row count
  $num = $result->rowCount();

// Check if any quotes
if($num > 0) 
{
  $quote_arr = ['data' => []];
  while($row = $result->fetch(PDO::FETCH_ASSOC)) 
  {
      extract($row);
      $quote_item = [
          'id' => $id,
          'quote' => $quote,
          'author_id' => $author_id,
          'category_id' => $category_id
      ];

      array_push($quote_arr['data'], $quote_item);

  }

  echo json_encode($quote_arr, JSON_PRETTY_PRINT);
} 

else 
{
  echo json_encode(['message' => 'No Quotes Found'], JSON_PRETTY_PRINT);
}

