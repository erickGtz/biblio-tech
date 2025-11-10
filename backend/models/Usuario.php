<?php
// Usuario.php - Modelo para gestionar usuarios

class Usuario {
    private $conn;
    private $table_usuarios = 'Usuarios';
    private $table_credenciales = 'Usuarios_Credenciales';

    public $idUsuario;
    public $nombre;
    public $apellido_paterno;
    public $apellido_materno;
    public $fecha_nacimiento;
    public $escuela;
    public $idGrado_Academico;
    public $correo;
    public $contrasena;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear nuevo usuario
    public function crear() {
        try {
            // Iniciar transacción
            $this->conn->beginTransaction();

            // Insertar en tabla Usuarios
            $query = "INSERT INTO " . $this->table_usuarios . " 
                      (nombre, apellido_paterno, apellido_materno, fecha_nacimiento, escuela, idGrado_Academico) 
                      VALUES (:nombre, :apellido_paterno, :apellido_materno, :fecha_nacimiento, :escuela, :idGrado_Academico)";

            $stmt = $this->conn->prepare($query);

            // Limpiar datos
            $this->nombre = htmlspecialchars(strip_tags($this->nombre));
            $this->apellido_paterno = htmlspecialchars(strip_tags($this->apellido_paterno));
            $this->apellido_materno = htmlspecialchars(strip_tags($this->apellido_materno));
            $this->escuela = htmlspecialchars(strip_tags($this->escuela));

            // Vincular parámetros
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':apellido_paterno', $this->apellido_paterno);
            $stmt->bindParam(':apellido_materno', $this->apellido_materno);
            $stmt->bindParam(':fecha_nacimiento', $this->fecha_nacimiento);
            $stmt->bindParam(':escuela', $this->escuela);
            $stmt->bindParam(':idGrado_Academico', $this->idGrado_Academico);

            if (!$stmt->execute()) {
                $this->conn->rollBack();
                return false;
            }

            // Obtener el ID del usuario insertado
            $idUsuario = $this->conn->lastInsertId();

            // Insertar credenciales
            $query2 = "INSERT INTO " . $this->table_credenciales . " 
                       (Usuario_idUsuario, correo, contrasena) 
                       VALUES (:idUsuario, :correo, :contrasena)";

            $stmt2 = $this->conn->prepare($query2);

            // Hash de la contraseña
            $hash_contrasena = password_hash($this->contrasena, PASSWORD_DEFAULT);

            $stmt2->bindParam(':idUsuario', $idUsuario);
            $stmt2->bindParam(':correo', $this->correo);
            $stmt2->bindParam(':contrasena', $hash_contrasena);

            if (!$stmt2->execute()) {
                $this->conn->rollBack();
                return false;
            }

            // Confirmar transacción
            $this->conn->commit();
            return true;

        } catch(Exception $e) {
            $this->conn->rollBack();

            // --- LÍNEA DE DEBUGGING ---
            // Esto nos dirá el error exacto de SQL
            http_response_code(500); // Sigue siendo un error 500
            echo json_encode(array(
                "success" => false,
                "message" => "Error de SQL al crear: " . $e->getMessage()
            ));
            exit();
        }
    }

    // Verificar si el correo ya existe
    public function correoExiste() {
        $query = "SELECT Usuario_idUsuario FROM " . $this->table_credenciales . " WHERE correo = :correo LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    // Login de usuario
    public function login() {
        $query = "SELECT u.*, uc.contrasena 
                  FROM " . $this->table_usuarios . " u
                  INNER JOIN " . $this->table_credenciales . " uc 
                  ON u.idUsuario = uc.Usuario_idUsuario
                  WHERE uc.correo = :correo
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();

            // Verificar contraseña
            if (password_verify($this->contrasena, $row['contrasena'])) {
                $this->idUsuario = $row['idUsuario'];
                $this->nombre = $row['nombre'];
                $this->apellido_paterno = $row['apellido_paterno'];
                $this->apellido_materno = $row['apellido_materno'];
                $this->escuela = $row['escuela'];
                $this->idGrado_Academico = $row['idGrado_Academico'];
                return true;
            }
        }

        return false;
    }
}
?>