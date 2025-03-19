<?php 
  // Headers
  header('Access-Control-Allow-Origin: *');
  header('Content-Type: application/json');

  include_once '../../config/Database.php';
  include_once '../../models/Category.php';

  // Instantiate DB & connect
  $database = new Database();
  $db = $database->connect();

  // Instantiate category object
  $category = new Category($db);

  // Category read query
  $result = $category->read();
  
  // Get row count
  $num = $result->rowCount();

  // Check if any categorys
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
            'category' => $category
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
    echo json_encode((["message" => "No category Found"]));
  }
