<?php
class Database
{
    private $host = 'localhost';
    private $db_name = 'quotesdb';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function connect()
    {
        $this-> conn = null;

        try 
        {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        catch (PDOException $e)
        {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}


/*
class Database
{
    // DB PARAMS
    private $host = 'localhost';
    private $port = '5433';
    private $db_name = 'quotesdb';
    private $username = 'postgres';
    private $password = 'postgres';
    private $conn;

    // DB CONNECT
    public function connect()
    {
        $this-> conn = null;
        $dsn = "pgsql:host={$this->host};port={$this->port};dbname={$this->db_name}";

        try 
        {
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttributes(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }

        catch (PDOException $e)
        {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
*/
