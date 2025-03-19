<?php
    class Category{
        //DB Stuff
        private $conn;
        private $table = 'Categories';

        //Properties
        public $id;
        public $category;

        //Constructor with DB
        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function create() 
        {
            if (empty($this->category)) 
            {
                echo json_encode(["message" => "category field cannot be empty"]);
                exit();
            }

            // Create Query
            $query = 'INSERT INTO ' . $this->table . ' (category) 
                      VALUES (:category)';
        
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
        
            // Bind data
            $stmt->bindParam(':category', $this->category, PDO::PARAM_STR);

            if ($stmt->execute()) 
            {
                $this->id = $this->conn->lastInsertId();
                echo json_encode([
                    'id' => $this->id,
                    'category' => $this->category
                ]);

                exit();
            }
        
            echo json_encode(['message' => 'Unable to create category']);
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
                        category
                    FROM
                        ' . $this->table . '
                        WHERE id = ?
                        LIMIT 1';

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            // Bind ID
            $stmt->bindParam(1, $this->id);

            // Execute query
            if ($stmt->execute()) 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($row) 
                {
                    // Set properties
                    $this->id = $row['id'];
                    $this->category = $row['category'];
                } 

                else 
                {
                    // No category found
                    echo json_encode(["message" => "category_id Not Found"]);
                    exit();
                }

            } 

            else 
            {
                // Query execution failed
                echo json_encode(["message" => "Error executing query"]);
                exit();
            }
        }

        public function read() 
        {
          // Create query
          $query = 'SELECT 
                        id, 
                        category
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

        public function update() 
        {
            // Check if category ID exists before updating
            $checkQuery = 'SELECT id FROM ' . $this->table . ' WHERE id = :id';
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $checkStmt->execute();
        
            if ($checkStmt->rowCount() === 0) {
                echo json_encode(["message" => "Category Not Found"]);
                return false;
            }
        
            // Update Query (DO NOT update ID)
            $query = 'UPDATE ' . $this->table . ' 
                      SET category = :category
                      WHERE id = :id';
        
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
        
            // Clean data
            $this->id = (int) htmlspecialchars(strip_tags($this->id));
            $this->category = htmlspecialchars(strip_tags($this->category));
        
            // Bind data
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            $stmt->bindParam(':category', $this->category, PDO::PARAM_STR);
        
            // Execute query
            if ($stmt->execute() && $stmt->rowCount() > 0) {
                return true;
            } else {
                echo json_encode(["message" => "No Changes Made"]);
                return false;
            }
        }

    }