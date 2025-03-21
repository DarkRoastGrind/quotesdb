<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$database = new Database();
$db = $database->connect();
$quote = new Quote($db);
$data = json_decode(file_get_contents("php://input"));

if (empty($data->id))
{
    echo json_encode(["id" => null, "message" => "No Quotes Found"]);
    exit();
}

$quote->id = (int) $data->id;
$quoteExistsQuery = 'SELECT id FROM quotes WHERE id = :id';
$stmt = $db->prepare($quoteExistsQuery);
$stmt->bindParam(':id', $quote->id);
$stmt->execute();

if ($stmt->rowCount() == 0)
{
    echo json_encode(["id" => $quote->id, "message" => "No Quotes Found"]);
    exit();
}

if ($quote->delete())
{
    echo json_encode(['id' => $quote->id]);
    exit();
}
else
{
    echo json_encode(['id' => $quote->id, 'message' => 'Quote Not Deleted']);
    exit();
}
