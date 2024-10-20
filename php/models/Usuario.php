<?php
class Usuario {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function crear($foto, $nombre, $contrasena) {
        $query = "INSERT INTO Usuarios (foto, nombre, contrasena) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($query);
        $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
        return $stmt->execute([$foto, $nombre, $hashed_password]);
    }
    
    public function obtenerPorId($id) {
        $query = "SELECT * FROM Usuarios WHERE id_usuario = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function autenticar($nombre, $contrasena) {
        $query = "SELECT * FROM Usuarios WHERE nombre = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$nombre]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
            return $usuario;
        }
        return false;
    }
}