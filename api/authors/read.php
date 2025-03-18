<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Author.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate category object
  $author = new Author($db);

  // Category read query
  $result = $author->read();
  
  // Get row count
  $num = $result->rowCount();

  // Check if any authors
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
            'author' => $author
          );

          // Push to "data"
          array_push($auth_arr['data'], $auth_item);
        }

        // Turn to JSON & output
        echo json_encode($auth_arr);

  } 

  else 
  {
    // No Categories
    echo json_encode((['message' => 'No Author Found']));
  }
