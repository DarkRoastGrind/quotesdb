<?php
class Author {
    private $conn;
    private $table = 'Authors';

    public $id;
    public $author;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        // Create Query
        $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $this->author, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete() {
        // Create query
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function read_single() {
        // Create query
        $query = 'SELECT id, author FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    public function read() {
        // Create query
        $query = 'SELECT id, author FROM ' . $this->table . ' ORDER BY id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function update() {
        // Update Query
        $query = 'UPDATE ' . $this->table . ' SET author = :author WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':author', $this->author, PDO::PARAM_STR);

        return $stmt->execute();
    }
}
?>