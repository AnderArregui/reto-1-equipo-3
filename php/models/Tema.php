<?php 
require_once "Db.php";

class Tema {
    private $connection;
    private $table = "temas";

    public function __construct()
    {
        $this->getConection();
    }

    public function getConection()
    {
        $db = new db();
        $this->connection = $db->connection; // Accede a la conexión
    }

    public function crear($nombre, $caracteristica) {
        $query = "INSERT INTO " . $this->table . " (nombre, caracteristica) VALUES (?, ?)";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$nombre, $caracteristica]);
    }

    public function obtenerTodos()
    {
        $sql = "SELECT * FROM " . $this->table;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_tema = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
