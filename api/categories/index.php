<?php
include_once '../../config/headers.php';
include_once '../../config/Database.php';
include_once '../../models/Category.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'OPTIONS') {
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Origin, Accept, Content-Type, X-Requested-With');
    exit();
}

$database = new Database();
$db = $database->connect();

$category = new Category($db);

if ($method === 'GET') {
    if (isset($_GET['id'])) {
        $category->id = $_GET['id'];
        $result = $category->read_single();

        if ($result->rowCount() > 0) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            echo json_encode(['id' => $row['id'], 'category' => $row['category']]);
        } else {
            echo json_encode(['message' => 'category_id Not Found']);
        }
    } else {
        $result = $category->read();
        $num = $result->rowCount();

        if ($num > 0) {
            $cat_arr = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $cat_arr[] = ['id' => $row['id'], 'category' => $row['category']];
            }
            echo json_encode($cat_arr);
        } else {
            echo json_encode(['message' => 'No categories Found']);
        }
    }
    exit();
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->category) || empty(trim($data->category))) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }
    //********************************CHECK THIS************************************/
    $category->category = htmlspecialchars(strip_tags(trim($data->category)));

    if ($category->create()) {
        echo json_encode(['id' => $category->id, 'category' => $category->category]);
    } else {
        echo json_encode(["message" => "Unable to create category"]);
    }
    exit();
}

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"));

    if (!isset($data->id) || !isset($data->category) || empty(trim($data->category))) {
        echo json_encode(["message" => "Missing Required Parameters"]);
        exit();
    }

    $category->id = (int) $data->id;
    $category->category = htmlspecialchars(strip_tags(trim($data->category)));

    if ($category->update()) {
        echo json_encode(["id" => $category->id, "category" => $category->category]);
    } else {
        echo json_encode(["message" => "Unable to update category"]);
    }
    exit();
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id']) || empty($data['id'])) {
        echo json_encode(["message" => "No category found"]);
        exit();
    }

    $category->id = (int) $data['id'];

    if ($category->delete()) {
        echo json_encode(['id' => $category->id]);
    } else {
        echo json_encode(['id' => $category->id, 'message' => 'Category Not Deleted']);
    }
    exit();
}
?>