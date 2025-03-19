<?php
    class Quote{
        //DB Stuff
        private $conn;
        private $table = 'quotes';

        //Properties
        public $id;
        public $quote;
        public $author_id;
        public $category_id;

        //Constructor with DB
        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function create() 
        {
            // Check if author_id exists in the authors table
            $authorQuery = 'SELECT id FROM authors WHERE id = :author_id';
            $authorStmt = $this->conn->prepare($authorQuery);
            $authorStmt->bindParam(':author_id', $this->author_id);
            $authorStmt->execute();
        
            if ($authorStmt->rowCount() == 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                exit();
            }
        
            // Check if category_id exists in the categories table
            $categoryQuery = 'SELECT id FROM categories WHERE id = :category_id';
            $categoryStmt = $this->conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category_id', $this->category_id);
            $categoryStmt->execute();
        
            if ($categoryStmt->rowCount() == 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                exit();
            }
        
            // Create Query
            $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) 
                      VALUES (:quote, :author_id, :category_id)';
        
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':quote', $this->quote, PDO::PARAM_STR);
            $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        
            if ($stmt->execute()) 
            {
                $this->id = $this->conn->lastInsertId();
                echo json_encode([
                    'id' => $this->id,
                    'quote' => $this->quote,
                    'author_id' => $this->author_id,
                    'category_id' => $this->category_id
                ]);
                exit();
            }
        
            echo json_encode(['message' => 'Unable to create quote']);
            exit();
        }
        


        public function delete() 
        {
            // Create query
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
        
            // clean data
            $this->id = htmlspecialchars(strip_tags($this->id));
        
            // Bind Data
            $stmt-> bindParam(':id', $this->id);
        
            // Execute query
            if($stmt->execute()) 
            {
              return true;
            }
            /*
            // Print error if something goes wrong
            printf("Error: $s.\n", $stmt->error);
            */
            return false;
        }

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
                    echo json_encode(["message" => "No Quotes Found"]);
                    exit();
                }
            } else {
                // Query execution failed
                echo json_encode(["message" => "Error executing query"]);
                exit();
            }
        }


        // Read function
        public function read() 
        {
          // Create query
          $query = 'SELECT 
                        id,
                        quote,
                        author_id,
                        category_id
                    FROM 
                    ' . $this->table . '
                    ORDER BY
                        id';
          
          // Prepare statement
          $stmt = $this->conn->prepare($query);
    
          // Execute query
          $stmt->execute();
    
          return $stmt;
        }

        public function update() {
            // Check if author_id exists in the authors table
            $authorQuery = 'SELECT id FROM authors WHERE id = :author_id';
            $authorStmt = $this->conn->prepare($authorQuery);
            $authorStmt->bindParam(':author_id', $this->author_id);
            $authorStmt->execute();
        
            if ($authorStmt->rowCount() == 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                exit();
            }
        
            // Check if category_id exists in the categories table
            $categoryQuery = 'SELECT id FROM categories WHERE id = :category_id';
            $categoryStmt = $this->conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category_id', $this->category_id);
            $categoryStmt->execute();
        
            if ($categoryStmt->rowCount() == 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                exit();
            }
        
            // Check if the quote exists
            $quoteQuery = 'SELECT id FROM ' . $this->table . ' WHERE id = :id';
            $quoteStmt = $this->conn->prepare($quoteQuery);
            $quoteStmt->bindParam(':id', $this->id);
            $quoteStmt->execute();
        
            if ($quoteStmt->rowCount() == 0) {
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
            if ($stmt->execute()) {
                echo json_encode([
                    'id' => $this->id,
                    'quote' => $this->quote,
                    'author_id' => $this->author_id,
                    'category_id' => $this->category_id
                ]);
                exit();
            } else {
                echo json_encode(['message' => 'Quote Not Updated']);
                exit();
            }
        }
    }