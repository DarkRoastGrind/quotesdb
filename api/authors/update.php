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

  // Assign values.
  $author->id = (int) $data->id;
  $author->author = trim($data->author);

  // Check if author exists before updating
  $query = "SELECT id FROM authors WHERE id = :id";
  $stmt = $db->prepare($query);
  $stmt->bindParam(':id', $author->id);
  $stmt->execute();

  if ($stmt->rowCount() == 0) 
  {
      echo json_encode(["message" => "No authors Found"]);
      exit();
  }

// Attempt to update the author
if ($author->update()) 
{
    echo json_encode([
        "id" => $author->id,
        "author" => $author->author
    ]);
} 

else 
{
    echo json_encode(["message" => "Author Not Updated"]);
}

exit();