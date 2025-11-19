<?php
class Rating {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function setRating($id_usuario, $id_apunte, $rating) {
        try {
            $query = "INSERT INTO ratings (id_usuario, id_apunte, rating) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE rating = VALUES(rating)";
            
            $stmt = $this->conn->prepare($query);
            return $stmt->execute([$id_usuario, $id_apunte, $rating]);
            
        } catch (PDOException $e) {
            error_log("Error en setRating: " . $e->getMessage());
            return false;
        }
    }

    public function getAverage($id_apunte) {
        try {
            $query = "SELECT 
                        COALESCE(AVG(rating), 0) AS average, 
                        COALESCE(COUNT(*), 0) AS total 
                      FROM ratings 
                      WHERE id_apunte = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id_apunte]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Error en getAverage: " . $e->getMessage());
            return ['average' => 0, 'total' => 0];
        }
    }

    public function getUserRating($id_usuario, $id_apunte) {
        try {
            $query = "SELECT rating FROM ratings 
                      WHERE id_usuario = ? AND id_apunte = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id_usuario, $id_apunte]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ? (int)$result['rating'] : 0;
            
        } catch (PDOException $e) {
            error_log("Error en getUserRating: " . $e->getMessage());
            return 0;
        }
    }
}
?>