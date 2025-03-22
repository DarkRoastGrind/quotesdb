<?php
include_once '../../authors/init.php';

$result = $author->read();
$num = $result->rowCount();

if ($num > 0)
{
  $authors_arr = [];

  while ($row = $result->fetch(PDO::FETCH_ASSOC))
  {
    $authors_arr[] = [
      'id' => $row['id'],
      'author' => $row['author']
    ];
  }

  echo json_encode($authors_arr);
}
else
{
  echo json_encode(["message" => "No Author Found"]);
}

exit();
