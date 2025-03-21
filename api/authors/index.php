<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$database = new Database();
$db = $database->connect();

$author = new Author($db);
function sendResponse($data, $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Handle OPTIONS request (CORS preflight)
if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

// Handle GET request (Fetch authors)
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $author->id = $_GET['id'];
        $result = $author->read_single();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            sendResponse(['id' => $row['id'], 'author' => $row['author']]);
        } else {
            sendResponse(['message' => 'author_id Not Found'], 404);
        }
    } else {
        $result = $author->read();
        $num = $result->rowCount();

        if ($num > 0) {
            $authors_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $authors_arr[] = ['id' => $row['id'], 'author' => $row['author']];
            }
            sendResponse($authors_arr);
        } else {
            sendResponse(['message' => 'No authors Found'], 404);
        }
    }
}

// Handle POST request (Create a new author)
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->author) || empty(trim($data->author))) {
        sendResponse(["message" => "Missing Required Parameters"], 400);
    }

    $author->author = trim($data->author);

    if ($author->create()) {
        sendResponse(['id' => $author->id, 'author' => $author->author]);
    } else {
        sendResponse(["message" => "Unable to create author"], 500);
    }
}

// Handle PUT request (Update an existing author)
if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->author) || empty(trim($data->author))) {
        sendResponse(["message" => "Missing Required Parameters"], 400);
    }

    $author->id = (int) $data->id;
    $author->author = trim($data->author);

    if ($author->update()) {
        sendResponse(["id" => $author->id, "author" => $author->author]);
    } else {
        sendResponse(["message" => "Unable to update author"], 500);
    }
}

// Handle DELETE requests (Delete an author)
if ($method === 'DELETE') {
    $_DELETE = json_decode(file_get_contents("php://input"), true);

    if (!isset($_DELETE['id']) || empty($_DELETE['id'])) {
        sendResponse(["id" => null, "message" => "No authors Found"], 404);
    }

    $author->id = (int) $_DELETE['id'];

    if ($author->delete()) {
        sendResponse(['id' => $author->id]);
    } else {
        sendResponse(['id' => $author->id, 'message' => 'Author Not Deleted'], 500);
    }
}

?>