<?php
// backend/models/Apunte.php
class Apunte {
    private $conn;
    private $table = 'apuntes'; // usa minúsculas si tu tabla está en minúsculas

    public function __construct($db) { $this->conn = $db; }

    public function crear(array $data) : bool {
        $sql = "INSERT INTO {$this->table}
                (titulo, ruta_archivo, idUsuario, fecha_subida, materia,
                 descripcion, semestre, universidad, carrera, etiquetas)
                VALUES
                (:titulo, :ruta, :idUsuario, :fecha,
                 :materia, :descripcion, :semestre, :universidad, :carrera, :etiquetas)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':titulo' => $data['titulo'],
            ':ruta' => $data['ruta_archivo'],
            ':idUsuario' => $data['idUsuario'],
            ':fecha' => $data['fecha_subida'],
            ':materia' => $data['materia'],
            ':descripcion' => $data['descripcion'],
            ':semestre' => $data['semestre'],
            ':universidad' => $data['universidad'],
            ':carrera' => $data['carrera'],
            ':etiquetas' => $data['etiquetas'],
        ]);
    }

    // public function obtenerTodos() {
    //     $sql = "SELECT * FROM " . $this->table . " ORDER BY fecha_subida DESC";
    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->execute();
    //     return $stmt;
    // }
    public function obtenerTodos() {
        $sql = "SELECT 
                    a.*, 
                    CONCAT_WS(' ', u.nombre, u.apellido_paterno) AS nombre_usuario
                FROM 
                    " . $this->table . " a
                INNER JOIN 
                    usuarios u ON a.idUsuario = u.idUsuario
                ORDER BY 
                    a.fecha_subida DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function buscarApuntes($termino, $filtro) {
        
        // 1. La consulta base es la misma que obtenerTodos()
        $sql = "SELECT 
                    a.*, 
                    CONCAT_WS(' ', u.nombre, u.apellido_paterno) AS nombre_usuario
                FROM 
                    " . $this->table . " a
                INNER JOIN 
                    usuarios u ON a.idUsuario = u.idUsuario"; // El JOIN es clave para buscar por usuario

        $params = [];
        $searchTerm = '%' . $termino . '%';

        // 2. Construimos la cláusula WHERE dinámicamente
        if (!empty($termino)) {
            switch ($filtro) {
                case 'todo':
                    // Busca en título, descripción O etiquetas
                    $sql .= " WHERE (a.titulo LIKE ? OR a.descripcion LIKE ? OR a.etiquetas LIKE ?)";
                    array_push($params, $searchTerm, $searchTerm, $searchTerm);
                    break;
                case 'materias':
                    // Busca solo en materia
                    $sql .= " WHERE a.materia LIKE ?";
                    array_push($params, $searchTerm);
                    break;
                case 'usuarios':
                    // Busca en el nombre del usuario (usando tu CONCAT_WS)
                    $sql .= " WHERE CONCAT_WS(' ', u.nombre, u.apellido_paterno) LIKE ?";
                    array_push($params, $searchTerm);
                    break;
                default:
                    // Filtro no reconocido, no añade WHERE (o puedes manejarlo como 'todo')
                    $sql .= " WHERE (a.titulo LIKE ? OR a.descripcion LIKE ? OR a.etiquetas LIKE ?)";
                    array_push($params, $searchTerm, $searchTerm, $searchTerm);
                    break;
            }
        }
        // Si no hay término de búsqueda, $params estará vacío y no se añadirá WHERE
        // por lo que traerá todos los apuntes (igual que obtenerTodos)

        // 3. Añadimos el orden y ejecutamos
        $sql .= " ORDER BY a.fecha_subida DESC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt; // Devuelve el statement para el fetchAll() en el controlador

        } catch (PDOException $e) {
            // En caso de un error en la consulta
            error_log("Error en buscarApuntes: " . $e->getMessage()); // Buena práctica
            return false;
        }
    }

}
