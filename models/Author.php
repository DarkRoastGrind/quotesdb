<?php
class Author
{
    private $conn;
    private $table = 'Authors';

    public $id;
    public $author;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':author', $this->author, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function delete()
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        return $stmt->execute();
    }

    public function read_single()
    {
        $query = 'SELECT id, author FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }

    public function read()
    {
        $query = 'SELECT id, author FROM ' . $this->table . ' ORDER BY id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        $query = 'UPDATE ' . $this->table . ' SET author = :author WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        $stmt->bindParam(':author', $this->author, PDO::PARAM_STR);
        return $stmt->execute();
    }

}
?>