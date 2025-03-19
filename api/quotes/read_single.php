public function read_single()
{
    // Create query
    $query = 'SELECT
                id,
                quote,
                author_id,
                category_id
              FROM
                ' . $this->table . '
              WHERE id = ?
              LIMIT 1';

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Bind ID
    $stmt->bindParam(1, $this->id);

    // Execute query
    if ($stmt->execute()) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Set properties
            $this->id = $row['id'];
            $this->quote = $row['quote'];
            $this->author_id = $row['author_id'];
            $this->category_id = $row['category_id'];
        } else {
            // No quote found
            http_response_code(404); // Not Found
            echo json_encode(["message" => "No Quotes Found"]);
            exit();
        }
    } else {
        // Query execution failed
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => "Error executing query"]);
        exit();
    }
}