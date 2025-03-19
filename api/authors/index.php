<?php
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

// Include necessary models and database connection
include_once '../../models/Author.php';
include_once '../../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Author model
$author = new Author($db);

// Handle GET requests
if ($method === 'GET') 
{
    // Check if an ID is passed for single author retrieval
    if (isset($_GET['id'])) 
    {
        // Fetch single Author
        $author->id = $_GET['id'];
        $author->read_single();  // Assuming this method fetches a single author by ID

        // Return Author as JSON
        if ($author->id && $author->author) 
        {
            echo json_encode([
                'id' => $author->id,
                'author' => $author->author
            ]);
        } 
        
        else 
        {
            echo json_encode(['message' => 'author_id Not Found']);
        }
    } 

    else 
    {
        // Fetch all authors
        $result = $author->read();  // Assuming this method fetches all authors
        $num = $result->rowCount();

        // Check if any authors exist
        if ($num > 0) 
        {
            $authors_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) 
            {
                extract($row);
                $authors_arr[] = [
                    'id' => $id,
                    'author' => $author
                ];
            }
            // Return all authors as JSON
            echo json_encode($authors_arr);
        } 

        else 
        {
            echo json_encode(['message' => 'No authors Found']);
        }
    }

    exit();
}

// Handle POST requests (Create a new author)
if ($method === 'POST') 
{
    // Get raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (empty($data->author)) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Ensure required parameters are present
    if (!isset($data->author) || empty(trim($data->author))) {
        echo json_encode(["message" => "Missing or empty 'author' field"]);
        exit();
    }

    // Set author data
    $author->author = trim($data->author);

    // Attempt to create the author
    if ($author->create()) 
    {
        echo json_encode([
            'id' => $author->id,
            'author' => $author->author
        ]);
    } 

    else 
    {
        echo json_encode(["message" => "Unable to create author"]);
    }
    exit();
}

// Handle PUT requests (Update an existing Author)
if ($method === 'PUT') 
{
    // Get raw PUT data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (!isset($data->id) || !isset($data->author) || empty(trim($data->author))) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set updated author data
    $author->id = (int) $data->id;
    $author->author = trim($data->author);

    // Attempt to update the author
    if ($author->update()) 
    {
        echo json_encode([
            "id" => $author->id,
            "author" => $author->author
        ]);
    } 

    else 
    {
        echo json_encode(["message" => "Unable to update author"]);
    }
    exit();
}

// Handle DELETE requests (Delete an author)
if ($method === 'DELETE') 
{
    // Get raw DELETE data
    $_DELETE = json_decode(file_get_contents("php://input"), true);

    if (!isset($_DELETE['id']) || empty($_DELETE['id'])) 
    {
        echo json_encode(["id" => null, "message" => "No authors Found"]);
        exit();
    }

    $author->id = (int) $_DELETE['id'];
    

    // Attempt to delete the author
    if ($author->delete()) 
    {
        // Return the 'id' field on success
        echo json_encode(['id' => $author->id]);
        exit();
    } 

    else 
    {
        // Include the 'id' field even in case of failure
        echo json_encode(['id' => $author->id, 'message' => 'author Not Deleted']);
        exit();
    }
}