<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

$method = $_SERVER['REQUEST_METHOD'];
$database = new Database();
$db = $database->connect();
$quote = new Quote($db);

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
        $quote->id = $_GET['id'];
        $quote->read_single();

        if ($quote->id && $quote->quote)
        {
            echo json_encode([
                'id' => $quote->id,
                'quote' => $quote->quote,
                'author' => $quote->author_id,
                'category' => $quote->category_id
            ]);
        }

        else
        {
            echo json_encode(['message' => 'No Quotes Found']);
        }

    }

    else
    {
        $result = $quote->read();
        $num = $result->rowCount();

        if ($num > 0)
        {
            $quotes_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC))
            {
                extract($row);
                $quotes_arr[] = [
                    'id' => $id,
                    'quote' => $quote,
                    'author' => $author_id,
                    'category' => $category_id
                ];
            }
            echo json_encode($quotes_arr);
        }

        else
        {
            echo json_encode(['message' => 'No Quotes Found']);
        }
    }

    exit();
}

if ($method === 'POST')
{
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->quote) || empty($data->author_id) || empty($data->category_id))
    {
        echo json_encode([
            "message" => "Missing Required Parameters"
        ]);
        exit();
    }

    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    if ($quote->create())
    {
        echo json_encode([
            'id' => $quote->id,
            'quote' => $quote->quote,
            'author_id' => $quote->author_id,
            'category_id' => $quote->category_id
        ]);
    }

    else
    {
        echo json_encode(["message" => "Unable to create quote"]);
    }

    exit();
}

if ($method === 'PUT')
{
    $data = json_decode(file_get_contents("php://input"));

    if (empty($data->id) || empty($data->quote) || empty($data->author_id) || empty($data->category_id))
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $quote->id = (int) $data->id;
    $quote->quote = trim($data->quote);
    $quote->author_id = (int) $data->author_id;
    $quote->category_id = (int) $data->category_id;

    $quote->update();

    exit();
}

if ($method === 'DELETE')
{
    $_DELETE = json_decode(file_get_contents("php://input"), true);

    if (!isset($_DELETE['id']) || empty($_DELETE['id']))
    {
        echo json_encode(["id" => null, "message" => "No Quotes Found"]);
        exit();
    }

    $quote->id = (int) $_DELETE['id'];

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
}



