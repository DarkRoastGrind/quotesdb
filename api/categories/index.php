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
require_once '../../models/Category.php';
require_once '../../config/Database.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

// Instantiate the Category model
$category = new Category($db);

// Handle GET requests
if ($method === 'GET') 
{
    // Check if an ID is passed for single category retrieval
    if (isset($_GET['id'])) 
    {
        // Fetch single category
        $category->id = $_GET['id'];
        $category->read_single();  // Assuming this method fetches a single category by ID

        // Return category as JSON
        if ($category->id && $category->category) 
        {
            echo json_encode([
                'id' => $category->id,
                'category' => $category->category
            ]);
        } 
        
        else 
        {
            echo json_encode(['message' => 'category_id Not Found']);
        }
    } 

    else 
    {
        // Fetch all categorys
        $result = $category->read();  // Assuming this method fetches all categorys
        $num = $result->rowCount();

        // Check if any categorys exist
        if ($num > 0) 
        {
            $categories_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) 
            {
                extract($row);
                $categories_arr[] = [
                    'id' => $id,
                    'category' => $category
                ];
            }
            // Return all categories as JSON
            echo json_encode($categoriess_arr);
        } 

        else 
        {
            echo json_encode(['message' => 'No categories Found']);
        }
    }

    exit();
}
