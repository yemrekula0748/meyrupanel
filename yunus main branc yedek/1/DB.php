<?php

class DB {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'satispanel', 'satispanel', 'satispanel');

        if ($this->conn->connect_error) {
            die('Bağlantı hatası: ' . $this->conn->connect_error);
        }
    }

    public function query($sql) {
        $result = $this->conn->query($sql);
        if (!$result) {
            throw new mysqli_sql_exception($this->conn->error);
        }
        return $result;
    }

    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    public function numRows($result) {
        return mysqli_num_rows($result);
    }

    public function fetchAssoc($result) {
        return mysqli_fetch_assoc($result);
    }

    public function __destruct() {
        $this->conn->close();
    }
}
?>
