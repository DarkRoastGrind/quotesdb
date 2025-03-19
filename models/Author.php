<?php
    class Author{
        //DB Stuff
        private $conn;
        private $table = 'authors';

        //Properties
        public $id;
        public $author;

        //Constructor with DB
        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function create() 
        {
            // Create Query
            $query = 'INSERT INTO ' . $this->table . ' (author) 
                      VALUES (:author)';
        
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
        
            // Bind data
            $stmt->bindParam(':author', $this->author, PDO::PARAM_STR);

            if ($stmt->execute()) 
            {
                $this->id = $this->conn->lastInsertId();
                echo json_encode([
                    'id' => $this->id,
                    'author' => $this->author
                ]);

                exit();
            }
        
            echo json_encode(['message' => 'Unable to create author']);
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
                        author
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
                    $this->author = $row['author'];
                } 

                else 
                {
                    // No author found
                    echo json_encode(["message" => "author_id Not Found"]);
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
                        author
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
            // Create Query
            $query = 'UPDATE ' .
            $this->table . '
            SET
            id = :id,
            author = :author
            WHERE
            id = :id';
            
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->author = htmlspecialchars(strip_tags($this->author));
            $this->id = htmlspecialchars(strip_tags($this->id));
            
            // Bind data
            $stmt-> bindParam(':author', $this->author);
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

    }