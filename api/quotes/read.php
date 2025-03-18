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
        // Cat array
        $auth_arr = array();
        $auth_arr['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) 
        {
          extract($row);

          $auth_item = array(
            'id' => $id,
            'quote' => $quote,
            'author' => $author_id,
            'category' => $category_id
          );

          // Push to "data"
          array_push($auth_arr['data'], $auth_item);
        }

        // Turn to JSON & output
        echo json_encode($auth_arr);

  } 

  else 
  {
        // No quotes
        echo json_encode(
          array('message' => 'No quote Found')
        );
  }
