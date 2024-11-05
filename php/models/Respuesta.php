<?php
class Respuesta {
    private $db;

    private $connection;

    public function __construct() {
        $this->getConection();
    }

    public function getConection() {
        $db = new db();
        $this->connection = $db->connection;
    }

    public function crear($id_post, $id_usuario, $contenido, $ruta_media = null) {
        $query = "INSERT INTO respuestas (id_post, id_usuario, contenido, ruta_media)
            VALUES (:id_post, :id_usuario, :contenido, :ruta_media)";
        
        $stmt = $this->connection->prepare($query);
        $stmt->execute([
            'id_post' => $id_post,
            'id_usuario' => $id_usuario,
            'contenido' => $contenido,
            'ruta_media' => $ruta_media
        ]);
    }


    public function obtenerLikesPorUsuario($usuarioId) {
        $query = "SELECT r.*, t.caracteristica 
                  FROM respuestas r 
                  INNER JOIN likeUsuario lu ON r.id_respuesta = lu.id_respuesta
                  LEFT JOIN posts p ON r.id_post = p.id_post
                  LEFT JOIN temas t ON p.id_tema = t.id_tema
                  WHERE lu.id_usuario = :usuarioId";
                  
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error en la ejecución de la consulta: " . $e->getMessage();
            return [];
        }
    }
    
    public function obtenerPorPost($id_post) {
        $query = "SELECT r.*, 
                         u.nombre AS nombre_usuario, 
                         u.especialidad AS especialidad_usuario,
                         (SELECT COUNT(*) FROM likeUsuario lu WHERE lu.id_respuesta = r.id_respuesta) AS likes
                  FROM respuestas r 
                  JOIN usuarios u ON r.id_usuario = u.id_usuario 
                  WHERE r.id_post = ? 
                  ORDER BY r.fecha DESC";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id_post]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    

    public function incrementarLikes($id_respuesta) {
        $query = "UPDATE respuestas SET likes = likes + 1 WHERE id_respuesta = ?";
        $stmt = $this->connection->prepare($query);
        return $stmt->execute([$id_respuesta]);
    }

    public function respuestasPost($id_post) {
  
        $post = $this->obtenerPost($id_post); 

        $respuestas = $this->obtenerPorPost($id_post);

        $this->view = "respuestas"; 
        return [
            'post' => $post,
            'respuestas' => $respuestas
        ];
    }

    public function obtenerPost($id_post) {

        $query = "SELECT * FROM posts WHERE id_post = ?";
        $stmt = $this->connection->prepare($query);
        $stmt->execute([$id_post]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
