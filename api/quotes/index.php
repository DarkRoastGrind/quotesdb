<?php
// Disable error reporting to prevent HTML errors from being output
ini_set('display_errors', 0);
error_reporting(0);

// Enable CORS and set response type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

// Handle OPTIONS request (CORS preflight)
if ($method === 'OPTIONS') 
{
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Include necessary models and database connection
include_once '../../config/Database.php';
include_once '../../models/Quote.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Quote model
$quote = new Quote($db);

// Handle GET requests
if ($method === 'GET') 
{
    // Check if an ID is passed for single quote retrieval
    if (isset($_GET['id'])) 
    {
        // Fetch single quote
        $quote->id = $_GET['id'];
        $quote->read_single();  // Assuming this method fetches a single quote by ID

        // Return quote as JSON
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
        // Fetch all quotes
        $result = $quote->read();  // Assuming this method fetches all quotes
        $num = $result->rowCount();

        // Check if any quotes exist
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
            // Return all quotes as JSON
            echo json_encode($quotes_arr);
        } 

        else 
        {
            echo json_encode(['message' => 'No Quotes Found']);
        }
    }

    exit();
}

// Handle POST requests (Create a new Quote)
if ($method === 'POST') 
{
    // Get raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) 
    {
        echo json_encode([
            "message" => "Missing Required Parameters"
        ]);
        exit();
    }

    // Set Quote data
    $quote->quote = $data->quote;
    $quote->author_id = $data->author_id;
    $quote->category_id = $data->category_id;

    // Attempt to create the quote
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


// Handle PUT requests (Update an existing Quote)
if ($_SERVER['REQUEST_METHOD'] === 'PUT') 
{
    // Get raw PUT data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (empty($data->id) || empty($data->quote) || empty($data->author_id) || empty($data->category_id)) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set updated Quote data
    $quote->id = (int) $data->id;
    $quote->quote = trim($data->quote);
    $quote->author_id = (int) $data->author_id;
    $quote->category_id = (int) $data->category_id;

    // Attempt to update the quote
    $quote->update();
    exit();
}


// Handle DELETE requests (Delete a Quote)
if ($method === 'DELETE') 
{
    // Get raw DELETE data
    parse_str(file_get_contents("php://input"), $_DELETE);

    // Ensure ID is passed for deletion
    if (empty($_DELETE['id'])) 
    {
        // Return 'id' as null if the quote ID is missing
        echo json_encode(["id" => null, "message" => "No Quotes Found"]);
        exit();
    }

    // Set Quote ID for deletion
    $quote->id = $_DELETE['id'];

    // Attempt to delete the quote
    if ($quote->delete()) 
    {
        // Return the 'id' field on success
        echo json_encode(['id' => $quote->id]);
        exit();
    } 
    else 
    {
        // Include the 'id' field even in case of failure
        echo json_encode(['id' => $quote->id, 'message' => 'Quote Not Deleted']);
        exit();
    }
}



