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
include_once '../../models/Category.php';
include_once '../../config/Database.php';

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
            echo json_encode($categories_arr);
        } 

        else 
        {
            echo json_encode(['message' => 'No categories Found']);
        }
    }

    exit();
}

// Handle POST requests (Create a new category)
if ($method === 'POST') 
{
    // Get raw POST data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (empty($data->category)) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Ensure required parameters are present
    if (!isset($data->category) || empty(trim($data->category))) {
        echo json_encode(["message" => "Missing or empty 'category' field"]);
        exit();
    }

    // Set category data
    $category->category = trim($data->category);

    // Attempt to create the category
    if ($category->create()) 
    {
        echo json_encode([
            'id' => $category->id,
            'category' => $category->category
        ]);
    } 

    else 
    {
        echo json_encode(["message" => "Unable to create category"]);
    }
    exit();
}

// Handle PUT requests (Update an existing category)
if ($method === 'PUT') 
{
    // Get raw PUT data
    $data = json_decode(file_get_contents("php://input"));

    // Ensure required parameters are present
    if (!isset($data->id) || !isset($data->category) || empty(trim($data->category))) 
    {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    // Set updated category data
    $category->id = (int) $data->id;
    $category->category = trim($data->category);

    // Attempt to update the category
    if ($category->update()) 
    {
        echo json_encode([
            "id" => $category->id,
            "category" => $category->category
        ]);
    } 

    else 
    {
        echo json_encode(["message" => "Unable to update category"]);
    }
    exit();
}

// Handle DELETE requests (Delete an category)
if ($method === 'DELETE') 
{
    // Get raw DELETE data
    $_DELETE = json_decode(file_get_contents("php://input"), true);

    if (!isset($_DELETE['id']) || empty($_DELETE['id'])) 
    {
        echo json_encode(["id" => null, "message" => "No categoryies Found"]);
        exit();
    }

    $category->id = (int) $_DELETE['id'];
    

    // Attempt to delete the category
    if ($category->delete()) 
    {
        // Return the 'id' field on success
        echo json_encode(['id' => $category->id]);
        exit();
    } 

    else 
    {
        // Include the 'id' field even in case of failure
        echo json_encode(['id' => $category->id, 'message' => 'category Not Deleted']);
        exit();
    }
}