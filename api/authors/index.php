<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Author.php';

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$db = $database->connect();
$author = new Author($db);

if ($method === 'OPTIONS')
{
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

if ($method === 'GET')
{
    if (isset($_GET['id']))
    {
        $author->id = $_GET['id'];
        $result = $author->read_single();

        if ($result->rowCount() > 0)
        {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['id' => $row['id'], 'author' => $row['author']]);
        }
        else
        {
            echo json_encode(['message' => 'author_id Not Found']);
        }
    }
    else
    {
        $result = $author->read();
        $num = $result->rowCount();

        if ($num > 0)
        {
            $authors_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC))
            {
                $authors_arr[] = ['id' => $row['id'], 'author' => $row['author']];
            }
            echo json_encode($authors_arr);
        }
        else
        {
            echo json_encode(['message' => 'No authors Found']);
        }
    }
    exit();
}

if ($method === 'POST')
{
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->author) || empty(trim($data->author)))
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $author->author = trim($data->author);

    if ($author->create())
    {
        echo json_encode(['id' => $author->id, 'author' => $author->author]);
    }
    else
    {
        echo json_encode(["message" => "Unable to create author"]);
    }

    exit();
}

if ($method === 'PUT')
{
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->author) || empty(trim($data->author)))
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $author->id = (int) $data->id;
    $author->author = trim($data->author);

    if ($author->update())
    {
        echo json_encode(["id" => $author->id, "author" => $author->author]);
    }
    else
    {
        echo json_encode(["message" => "Unable to update author"]);
    }
    exit();
}

if ($method === 'DELETE')
{
    $_DELETE = json_decode(file_get_contents("php://input"), true);

    if (!isset($_DELETE['id']) || empty($_DELETE['id']))
    {
        echo json_encode(["id" => null, "message" => "No authors Found"]);
        exit();
    }

    $author->id = (int) $_DELETE['id'];

    if ($author->delete())
    {
        echo json_encode(['id' => $author->id]);
    }
    else
    {
        echo json_encode(['id' => $author->id, 'message' => 'Author Not Deleted']);
    }
    exit();
}
?>