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
        // Set the quote ID from the query string
        $quote->id = $_GET['id'];
        $stmt = $quote->read_single();

        // Check if any rows are returned
        if ($stmt->rowCount() > 0)
        {
            // Fetch the single quote data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // Return the quote as JSON
            echo json_encode([
                'id' => $row['id'],
                'quote' => $row['quote'],
                'author' => $row['author_id'],
                'category' => $row['category_id']
            ]);
        }
        else
        {
            // No quote found, return the error message
            echo json_encode(['message' => 'No Quotes Found']);
        }
    }
    else
    {
        // Handle case where no specific quote ID is provided
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

    if (!isset($data->quote) || !isset($data->author_id) || !isset($data->category_id) ||
        empty(trim($data->quote)) || empty($data->author_id) || empty($data->category_id))
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $quote->quote = htmlspecialchars(strip_tags(trim($data->quote)));
    $quote->author_id = (int) $data->author_id;
    $quote->category_id = (int) $data->category_id;


    // Check if author_id exists
    $authorCheck = $db->prepare("SELECT id FROM authors WHERE id = :author_id");
    $authorCheck->bindParam(':author_id', $quote->author_id);
    $authorCheck->execute();

    if ($authorCheck->rowCount() == 0)
    {
        echo json_encode(["message" => "author_id Not Found"]);
        exit();
    }

    // Check if category_id exists
    $categoryCheck = $db->prepare("SELECT id FROM categories WHERE id = :category_id");
    $categoryCheck->bindParam(':category_id', $quote->category_id);
    $categoryCheck->execute();

    if ($categoryCheck->rowCount() == 0)
    {
        echo json_encode(["message" => "category_id Not Found"]);
        exit();
    }

    if ($quote->create())
    {
        echo json_encode(['id' => $quote->id, 'quote' => $quote->quote, 'author_id' => $quote->author_id, 'category_id' => $quote->category_id]);
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

    // Check if required parameters are missing
    if (empty($data->id) || empty($data->quote) || empty($data->author_id) || empty($data->category_id))
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set the properties of the quote object
    $quote->id = (int) $data->id;
    $quote->quote = trim($data->quote);
    $quote->author_id = (int) $data->author_id;
    $quote->category_id = (int) $data->category_id;

    // Check if the author_id exists in the authors table
    $authorCheck = $db->prepare("SELECT id FROM authors WHERE id = :author_id");
    $authorCheck->bindParam(':author_id', $quote->author_id);
    $authorCheck->execute();

    if ($authorCheck->rowCount() == 0)
    {
        echo json_encode(["message" => "author_id Not Found"]);
        exit();
    }

    // Check if the category_id exists in the categories table
    $categoryCheck = $db->prepare("SELECT id FROM categories WHERE id = :category_id");
    $categoryCheck->bindParam(':category_id', $quote->category_id);
    $categoryCheck->execute();

    if ($categoryCheck->rowCount() == 0)
    {
        echo json_encode(["message" => "category_id Not Found"]);
        exit();
    }

    // Check if the quote exists in the quotes table
    $quoteCheck = $db->prepare("SELECT id FROM quotes WHERE id = :id");
    $quoteCheck->bindParam(':id', $quote->id);
    $quoteCheck->execute();

    if ($quoteCheck->rowCount() == 0)
    {
        echo json_encode(["message" => "No Quotes Found"]);
        exit();
    }

    // Attempt to update the quote
    if ($quote->update())
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
        echo json_encode(['message' => 'Quote Not Updated']);
    }
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

    // Check if the quote exists before attempting to delete
    $quoteCheck = $db->prepare("SELECT id FROM quotes WHERE id = :id");
    $quoteCheck->bindParam(':id', $quote->id);
    $quoteCheck->execute();

    if ($quoteCheck->rowCount() == 0)
    {
        echo json_encode(["id" => null, "message" => "No Quotes Found"]);
        exit();
    }

    if ($quote->delete())
    {
        echo json_encode(['id' => $quote->id]);
    }
    else
    {
        echo json_encode(['id' => $quote->id, 'message' => 'Quote Not Deleted']);
    }

    exit();
}



