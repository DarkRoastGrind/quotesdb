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

else
{
    echo json_encode(["message" => "Method not allowed"]);
}
