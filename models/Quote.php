<?php
class Quote
{
    private $conn;
    private $table = 'quotes';

    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) 
                      VALUES (:quote, :author_id, :category_id)';
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':quote', $this->quote, PDO::PARAM_STR);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function delete()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function read_single()
    {
        $query = 'SELECT id, quote, author_id, category_id FROM ' . $this->table . ' WHERE id = :id LIMIT 1';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetch(PDO::FETCH_ASSOC); // Return data or false if not found
    }

    public function read()
    {
        $query = 'SELECT id, quote, author_id, category_id FROM ' . $this->table . ' ORDER BY id';
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
    
        return $stmt; // Return PDOStatement
    }

    public function update()
    {
        // Check if author_id exists in the authors table
        $authorQuery = 'SELECT id FROM authors WHERE id = :author_id';
        $authorStmt = $this->conn->prepare($authorQuery);
        $authorStmt->bindParam(':author_id', $this->author_id);
        $authorStmt->execute();

        if ($authorStmt->rowCount() == 0)
        {
            echo json_encode(['message' => 'author_id Not Found']);
            exit();
        }

        // Check if category_id exists in the categories table
        $categoryQuery = 'SELECT id FROM categories WHERE id = :category_id';
        $categoryStmt = $this->conn->prepare($categoryQuery);
        $categoryStmt->bindParam(':category_id', $this->category_id);
        $categoryStmt->execute();

        if ($categoryStmt->rowCount() == 0)
        {
            echo json_encode(['message' => 'category_id Not Found']);
            exit();
        }

        // Check if the quote exists
        $quoteQuery = 'SELECT id FROM ' . $this->table . ' WHERE id = :id';
        $quoteStmt = $this->conn->prepare($quoteQuery);
        $quoteStmt->bindParam(':id', $this->id);
        $quoteStmt->execute();

        if ($quoteStmt->rowCount() == 0)
        {
            echo json_encode(['message' => 'No Quotes Found']);
            exit();
        }

        // Create Query
        $query = 'UPDATE ' . $this->table . '
                      SET quote = :quote,
                          author_id = :author_id,
                          category_id = :category_id
                      WHERE id = :id';

        // Prepare Statement
        $stmt = $this->conn->prepare($query);

        // Clean data
        $this->quote = $this->quote ? htmlspecialchars(strip_tags($this->quote)) : '';
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $this->author_id = (int) htmlspecialchars(strip_tags($this->author_id));
        $this->category_id = (int) htmlspecialchars(strip_tags($this->category_id));

        // Bind data
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);

        // Execute query
        if ($stmt->execute())
        {
            echo json_encode([
                'id' => $this->id,
                'quote' => $this->quote,
                'author_id' => $this->author_id,
                'category_id' => $this->category_id
            ]);
            exit();
        }
        else
        {
            echo json_encode(['message' => 'Quote Not Updated']);
            exit();
        }
    }
}