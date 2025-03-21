<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$db = $database->connect();
$quote = new Quote($db);

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $quote->id = $_GET['id'];
        $data = $quote->read_single();
        echo json_encode($data ?: ['message' => 'No Quotes Found']);
    } else {
        $result = $quote->read();
        echo json_encode($result->rowCount() > 0 ? $result->fetchAll(PDO::FETCH_ASSOC) : ['message' => 'No Quotes Found']);
    }
    exit();
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));
    if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    echo json_encode($quote->create() ? ['id' => $quote->id] : ["message" => "Unable to create quote"]);
    exit();
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));
    if (empty($data->id) || empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $quote->id = $data->id;
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    echo json_encode($quote->update() ? ['id' => $quote->id] : ["message" => "Quote Not Updated"]);
    exit();
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data['id'])) {
        echo json_encode(["message" => "No Quotes Found"]);
        exit();
    }

    $quote->id = (int) $data['id'];
    echo json_encode($quote->delete() ? ['id' => $quote->id] : ["message" => "Quote Not Deleted"]);
    exit();
}
