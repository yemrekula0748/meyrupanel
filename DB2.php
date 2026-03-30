<?php
class DB {
    private $host = 'localhost';
    private $user = 'satispanel';
    private $password = 'satispanel';
    private $database = 'satispanel';
    public $connection;

    // Constructor: Veritabanı bağlantısını oluşturur
    public function __construct() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database);

        // Bağlantı hatasını kontrol et
        if ($this->connection->connect_error) {
            die("Veritabanı bağlantısı başarısız: " . $this->connection->connect_error);
        }
    }

    // Sorgu çalıştırma metodu
    public function query($sql) {
        $result = $this->connection->query($sql);
        if (!$result) {
            die("Sorgu hatası: " . $this->connection->error);
        }
        return $result;
    }

    // Veri güvenliği için veriyi temizler
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    // Destructor: Bağlantıyı kapatır
    public function __destruct() {
        $this->connection->close();
    }
}
?>
