<?php

class DB {
    private $conn;

    public function __construct() {
        $this->conn = new mysqli('localhost', 'meyrupanel', 'meyrupanel', 'meyrupanel');

        if ($this->conn->connect_error) {
            die('Bağlantı hatası: ' . $this->conn->connect_error);
        }
    }

    // Hazırlıklı sorgularla birlikte kullanılabilecek query metodu
    public function query($sql, $params = [], $types = "") {
        if (!empty($params)) {
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new mysqli_sql_exception($this->conn->error);
            }
            if (!empty($types)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            return $result;
        } else {
            $result = $this->conn->query($sql);
            if (!$result) {
                throw new mysqli_sql_exception($this->conn->error);
            }
            return $result;
        }
    }

    public function escape($str) {
        return $this->conn->real_escape_string($str);
    }

    public function getConn() {
        return $this->conn; // Bağlantıyı döndüren getter
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
