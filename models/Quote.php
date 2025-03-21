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

    private function exists($table, $column, $value)
    {
        $query = "SELECT id FROM $table WHERE $column = :value";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':value', $value);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create()
    {
        if (!$this->exists('authors', 'id', $this->author_id)) return false;
        if (!$this->exists('categories', 'id', $this->category_id)) return false;

        $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) 
                  VALUES (:quote, :author_id, :category_id)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $this->quote, PDO::PARAM_STR);
        $stmt->bindParam(':author_id', $this->author_id, PDO::PARAM_INT);
        $stmt->bindParam(':category_id', $this->category_id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    public function delete()
    {
        if (!$this->exists($this->table, 'id', $this->id)) return false;
        
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function read_single()
    {
        $query = 'SELECT id, quote, author_id, category_id FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function read()
    {
        $query = 'SELECT id, quote, author_id, category_id FROM ' . $this->table . ' ORDER BY id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function update()
    {
        if (!$this->exists($this->table, 'id', $this->id)) return false;
        if (!$this->exists('authors', 'id', $this->author_id)) return false;
        if (!$this->exists('categories', 'id', $this->category_id)) return false;

        $query = 'UPDATE ' . $this->table . ' SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quote', $this->quote);
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':author_id', $this->author_id);
        $stmt->bindParam(':category_id', $this->category_id);
        return $stmt->execute();
    }
}