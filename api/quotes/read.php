<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();
$quote = new Quote($db);
$result = $quote->read();
$num = $result->rowCount();

if ($num > 0)
{
  $auth_arr = array();
  $auth_arr['data'] = array();

  while ($row = $result->fetch(PDO::FETCH_ASSOC))
  {
    extract($row);

    $auth_item = array(
      'id' => $id,
      'quote' => $quote,
      'author' => $author_id,
      'category' => $category_id
    );

    array_push($auth_arr['data'], $auth_item);
  }

  echo json_encode($auth_arr);

}

else
{
  echo json_encode(array('message' => 'No quote Found'));
}
