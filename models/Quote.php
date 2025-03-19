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

        public function create($data)
        {
            if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
                echo json_encode(["message" => "Missing Required Parameters"]);
                exit();
            }

            // Check if author_id exists in authors table
            $authorQuery = 'SELECT id FROM authors WHERE id = :author_id';
            $authorStmt = $this->conn->prepare($authorQuery);
            $authorStmt->bindParam(':author_id', $data->author_id);
            $authorStmt->execute();
            if ($authorStmt->rowCount() == 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                exit();
            }
        
            // Check if category_id exists in categories table
            $categoryQuery = 'SELECT id FROM categories WHERE id = :category_id';
            $categoryStmt = $this->conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category_id', $data->category_id);
            $categoryStmt->execute();
            if ($categoryStmt->rowCount() == 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                exit();
            }
        
            // Proceed with the insert if checks pass
            $query = 'INSERT INTO quotes (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quote', $data->quote);
            $stmt->bindParam(':author_id', $data->author_id);
            $stmt->bindParam(':category_id', $data->category_id);
        
            if ($stmt->execute()) {
                echo json_encode(["message" => "Quote created"]);
            } else {
                echo json_encode(["message" => "Error creating quote"]);
            }
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
            if ($stmt->execute()) 
            {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
                if ($row) {
                    // Set properties
                    $this->id = $row['id'];
                    $this->quote = $row['quote'];
                    $this->author_id = $row['author_id'];
                    $this->category_id = $row['category_id'];
                } 

                else 
                {
                    echo json_encode(['message' => 'No Quotes Found']);
                    exit();
                }
            } 
            
            else 
            {
                echo json_encode(['message' => 'Error executing query']);
                exit();
            }
        }


        // Read function
        public function read() 
        {
            $query = 'SELECT id, quote, author_id, category_id FROM ' . $this->table . ' ORDER BY id';
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $num = $stmt->rowCount();
            
            if ($num > 0) {
                $quotes_arr = array();
                $quotes_arr['quotes'] = array();
                
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    extract($row);
                    $quote_item = array(
                        'id' => $id,
                        'quote' => $quote,
                        'author_id' => $author_id,
                        'category_id' => $category_id
                    );
                    array_push($quotes_arr['quotes'], $quote_item);
                }
                echo json_encode($quotes_arr);
            } else {
                echo json_encode(['message' => 'No Quotes Found']);
            }
        }

        public function update($data)
        {
            if (empty($data->quote) || empty($data->author_id) || empty($data->category_id)) {
                echo json_encode(["message" => "Missing Required Parameters"]);
                exit();
            }
            
            // Check if author_id exists in authors table
            $authorQuery = 'SELECT id FROM authors WHERE id = :author_id';
            $authorStmt = $this->conn->prepare($authorQuery);
            $authorStmt->bindParam(':author_id', $data->author_id);
            $authorStmt->execute();
            if ($authorStmt->rowCount() == 0) {
                echo json_encode(['message' => 'author_id Not Found']);
                exit();
            }
        
            // Check if category_id exists in categories table
            $categoryQuery = 'SELECT id FROM categories WHERE id = :category_id';
            $categoryStmt = $this->conn->prepare($categoryQuery);
            $categoryStmt->bindParam(':category_id', $data->category_id);
            $categoryStmt->execute();
            if ($categoryStmt->rowCount() == 0) {
                echo json_encode(['message' => 'category_id Not Found']);
                exit();
            }
        
            // Proceed with the update if checks pass
            $query = 'UPDATE quotes SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id';
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quote', $data->quote);
            $stmt->bindParam(':author_id', $data->author_id);
            $stmt->bindParam(':category_id', $data->category_id);
            $stmt->bindParam(':id', $data->id);
        
            if ($stmt->execute()) {
                echo json_encode(["message" => "Quote updated"]);
            } else {
                echo json_encode(["message" => "Error updating quote"]);
            }
        } 

    }