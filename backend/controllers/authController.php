<?php
// authController.php - Controlador de autenticación

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../models/Usuario.php';

session_start();

$database = new Database();
$db = $database->getConnection();
$usuario = new Usuario($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';
$data = json_decode(file_get_contents("php://input"));

switch ($action) {
    case 'register':
        registrarUsuario($usuario, $data);
        break;
    
    case 'login':
        loginUsuario($usuario, $data);
        break;
    
    case 'logout':
        logoutUsuario();
        break;
    
    default:
        http_response_code(400);
        echo json_encode(array("message" => "Acción no válida"));
        break;
}

function registrarUsuario($usuario, $data) {
    if (
        !empty($data->nombre) &&
        !empty($data->apellido_paterno) &&
        !empty($data->apellido_materno) &&
        !empty($data->fecha_nacimiento) &&
        !empty($data->escuela) &&
        !empty($data->grado_academico) &&
        !empty($data->correo) &&
        !empty($data->contrasena)
    ) {
        // Validar formato de correo
        if (!filter_var($data->correo, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "Formato de correo inválido"
            ));
            return;
        }

        $usuario->correo = $data->correo;

        // Verificar si el correo ya existe
        if ($usuario->correoExiste()) {
            http_response_code(400);
            echo json_encode(array(
                "success" => false,
                "message" => "El correo ya está registrado"
            ));
            return;
        }

        // Asignar valores
        $usuario->nombre = $data->nombre;
        $usuario->apellido_paterno = $data->apellido_paterno;
        $usuario->apellido_materno = $data->apellido_materno;
        $usuario->fecha_nacimiento = $data->fecha_nacimiento;
        $usuario->escuela = $data->escuela;
        $usuario->idGrado_Academico = $data->grado_academico;
        $usuario->contrasena = $data->contrasena;

        // Crear usuario
        if ($usuario->crear()) {
            http_response_code(201);
            echo json_encode(array(
                "success" => true,
                "message" => "Usuario registrado exitosamente"
            ));

            
        } else {
            http_response_code(500);
            echo json_encode(array(
                "success" => false,
                "message" => "No se pudo registrar el usuario"
            ));
        }
    } else {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Datos incompletos"
        ));
    }
}

function loginUsuario($usuario, $data) {
    if (!empty($data->correo) && !empty($data->contrasena)) {
        $usuario->correo = $data->correo;
        $usuario->contrasena = $data->contrasena;

        if ($usuario->login()) {
            // Crear sesión
            $_SESSION['usuario_id'] = $usuario->idUsuario;
            $_SESSION['nombre'] = $usuario->nombre;
            $_SESSION['apellido_paterno'] = $usuario->apellido_paterno;
            $_SESSION['correo'] = $usuario->correo;

            http_response_code(200);
            echo json_encode(array(
                "success" => true,
                "message" => "Login exitoso",
                "data" => array(
                    "idUsuario" => $usuario->idUsuario,
                    "nombre" => $usuario->nombre,
                    "apellido_paterno" => $usuario->apellido_paterno,
                    "apellido_materno" => $usuario->apellido_materno,
                    "escuela" => $usuario->escuela
                )
            ));

            
        } else {
            http_response_code(401);
            echo json_encode(array(
                "success" => false,
                "message" => "Correo o contraseña incorrectos"
            ));
        }
    } else {
        http_response_code(400);
        echo json_encode(array(
            "success" => false,
            "message" => "Datos incompletos"
        ));
    }
}

function logoutUsuario() {
    session_destroy();
    http_response_code(200);
    echo json_encode(array(
        "success" => true,
        "message" => "Sesión cerrada exitosamente"
    ));
}
?>