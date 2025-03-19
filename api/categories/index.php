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
            $cat_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) 
            {
                extract($row);
                $cat_arr[] = [
                    'id' => $id,
                    'category' => $category
                ];
            }
            // Return all categories as JSON
            echo json_encode($cat_arr);
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
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->category) || empty(trim($data->category))) 
    {
        echo json_encode(["message" => "Missing or empty 'category' field"]);
        exit();
    }

    $category->category = trim($data->category);

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
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || empty($data['id'])) 
    {
        echo json_encode(["message" => "No category found"]);
        exit();
    }

    $category->id = (int) $data['id'];

    if ($category->delete()) 
    {
        echo json_encode(['id' => $category->id]);
    } 
    else 
    {
        echo json_encode(['id' => $category->id, 'message' => 'Category Not Deleted']);
    }
    exit();
}
