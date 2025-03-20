<?php
// Include headers
include_once '../../config/headers.php';

include_once '../../config/Database.php';
include_once '../../models/Author.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate category object
$author = new Author($db);

// Fetch Authors
$result = $author->read();
$num = $result->rowCount();

// Check if any authors exist
if ($num > 0) {
  $authors_arr = [];

  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $authors_arr[] = [
          'id' => $row['id'],
          'author' => $row['author']
      ];
  }

  echo json_encode($authors_arr);
} else {
  echo json_encode(["message" => "No Author Found"]);
}

exit();
