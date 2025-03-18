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
            // Create Query
            $query = 'INSERT INTO ' .
              $this->table . '
            SET
                id = :id,
                quote = :quote,
                author_id = :author_id,
                category_id = :category_id';
        
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
        
            // Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        
            // Bind data
            $stmt-> bindParam(':id', $this->id);
            $stmt-> bindParam(':quote', $this->quote);
            $stmt-> bindParam(':author_id', $this->author_id);
            $stmt-> bindParam(':category_id', $this->category_id);
        
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

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            // Bind ID
            $stmt->bindParam(1, $this->id);

            // Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // set properties
            $this->id = $row['id'];
            $this->quote = $row['quote'];
            $this->author_id = $row['author_id'];
            $this->category_id = $row['category_id'];
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

        public function update() 
        {
            // Create Query
            $query = 'UPDATE ' .
            $this->table . '
            SET
            quote = :quote
            author_id = :author_id,
            category_id = :category_id
            WHERE
            id = :id';
            
            // Prepare Statement
            $stmt = $this->conn->prepare($query);
            
            // Clean data
            $this->quote = htmlspecialchars(strip_tags($this->quote));
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->author_id = htmlspecialchars(strip_tags($this->author_id));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
            
            // Bind data
            $stmt-> bindParam(':quote', $this->quote);
            $stmt-> bindParam(':id', $this->id);
            $stmt-> bindParam(':author_id', $this->author_id);
            $stmt-> bindParam(':category_id', $this->author_id);
            
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