<?php

require_once "models/Usuario.php";
require_once "models/Respuesta.php";

class UsuarioController {
    public $view;
    public $showLayout = true;
    public $respuesta;
    public $post;

    private $usuario;

    public function __construct() {
        $this->view = "login";
    }

    public function login() {
        $this->showLayout = false;
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];
    
            if (strtolower($username) === 'usuario eliminado') {
                echo "<script>alert('Este nombre de usuario no está permitido.');</script>";
                $this->view = "login";
                return;
            }
    
            if (preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]+/', $username)) {
                echo "<script>alert('El nombre de usuario no puede contener caracteres especiales.');</script>";
                $this->view = "login";
                return;
            }
    
            $this->usuario = new Usuario();
            $usuarioModel = $this->usuario->validateLogin($username, $password);
    
            if ($usuarioModel) {
                $_SESSION['usuario'] = [
                    'id_usuario' => $usuarioModel['id_usuario'],
                    'nombre' => $usuarioModel['nombre'],
                    'foto' => $usuarioModel['foto'],
                    'especialidad' => $usuarioModel['especialidad'],
                    'anios_empresa' => $usuarioModel['anios_empresa'],
                    'email' => $usuarioModel['email'],
                    'tipo' => $usuarioModel['tipo']
                ];
                setcookie("{$username}_note", "", time() - 3600, "/");
                setcookie("note", "", time() - 3600, "/"); 
    
                header("Location: index.php?controller=Inicio&action=inicio");
                exit();
            } else {
                echo "<script>alert('Usuario o contraseña incorrectos');</script>";
            }
        }
    
        $this->view = "login"; 
    }

    public function inicio() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?controller=usuario&action=login");
            exit();
        }

        $this->view = "inicio";
    }

    public function perfil() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?controller=usuario&action=login");
            exit();
        }

        $this->view = "perfil";

        $idUsuario = $_SESSION['usuario']['id_usuario'];
        $this->usuario = new Usuario();
        $usuario = $this->usuario->obtenerPorId($idUsuario);

        $this->post = new Post();
        $posts = $this->post->obtenerPostsPorUsuario($idUsuario);

        $this->respuesta = new Respuesta();
        $respuestas = $this->respuesta->obtenerRespuestasPorUsuario($idUsuario);
        
        return [
            'usuario' => $usuario,
            'posts' => $posts,
            'respuestas' => $respuestas
        ];
    }

    public function mostrarUsuario() {
        $this->usuario = new Usuario();
        $usuarios = $this->usuario->obtenerTodosLosUsuarios();
        $this->view = "mostrarusuario";
        return [
            'usuarios' => $usuarios
        ];
    }
    public function usuarioindividual() {
        if (!isset($_SESSION['usuario'])) {
            header("Location: index.php?controller=Usuario&action=mostrarUsuario");
            exit();
        }
    
        $id_usuario = $_GET['id_usuario'] ?? null;
        $this->usuario = new Usuario();
        $infoUsuario = $this->usuario->obtenerInfoUsuario($id_usuario);
    
        if (!$infoUsuario) {
            $this->view = "usuarioindividual";
            return ["mensaje" => "Usuario no encontrado"];
        }
    
        $preguntas = [];
        $respuestas = [];
        
        if ($_SESSION['usuario']['tipo'] === 'admin') {
            $preguntas = $this->usuario->obtenerPreguntasPorUsuario($id_usuario);
            $respuestas = $this->usuario->obtenerRespuestasPorUsuario($id_usuario);
            
        }
    
        $this->view = "usuarioindividual";
        return [
            'infoUsuario' => $infoUsuario,
            'preguntas' => $preguntas,
            'respuestas' => $respuestas
        ];
    }
    

    public function actualizarImagenPerfil() {
        try {
            if (!isset($_SESSION['usuario']['id_usuario'])) {
                echo json_encode(["success" => false, "message" => "Usuario no autenticado"]);
                return;
            }
    
            if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] == 0) {
                $targetDir = __DIR__ . "/../assets/images/perfil/";
                $fileName = uniqid() . "_" . basename($_FILES["fotoPerfil"]["name"]);
                $targetFilePath = $targetDir . $fileName;
                
                if (move_uploaded_file($_FILES["fotoPerfil"]["tmp_name"], $targetFilePath)) {

                    $relativePath = "/assets/images/perfil/" . $fileName;

                    $usuario = new Usuario();
                    $usuario->actualizarImagenPerfil($_SESSION['usuario']['id_usuario'], $relativePath);
                    $_SESSION['usuario']['foto'] = $relativePath;
    
                    echo json_encode(["success" => true, "newImageUrl" => $targetFilePath]);
                } else {
                    echo json_encode(["success" => false, "message" => "Error al mover el archivo."]);
                }
            } else {
                echo json_encode(["success" => false, "message" => "No se ha seleccionado ninguna imagen o hubo un error al subirla."]);
            }
        } catch (Exception $e) {
            echo json_encode(["success" => false, "message" => "Ocurrió un error: " . $e->getMessage()]);
        }
        $this->view="actualizarfotoperfil";
        exit();
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?controller=usuario&action=login");
        exit();
    }

    public function eliminarPregunta() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        $id_post = $_POST['id_post'] ?? null;
        if (!$id_post) {
            $_SESSION['error'] = "ID de pregunta no proporcionado";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->usuario = new Usuario();
        if ($this->usuario->eliminarPregunta($id_post)) {
            $_SESSION['success'] = "Pregunta eliminada correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar la pregunta";
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    public function eliminarRespuesta() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }
    
        $id_respuesta = $_POST['id_respuesta'] ?? null;
        
        
    
        if (!$id_respuesta) {
            $_SESSION['error'] = "ID de respuesta no proporcionado";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    
        $this->usuario = new Usuario();
        if ($this->usuario->eliminarRes($id_respuesta)) {
            $_SESSION['success'] = "Respuesta eliminada correctamente";
            
        } else {
            $_SESSION['error'] = "Error al eliminar la respuesta";
        }
    
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    
    

    public function eliminarUsuario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }

        $id_usuario = $_POST['id_usuario'] ?? null;
        print_r( $id_usuario);
        if (!$id_usuario) {
            $_SESSION['error'] = "ID de usuario no proporcionado";
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        $this->usuario = new Usuario();
        if ($this->usuario->eliminarUsuario($id_usuario)) {
            $_SESSION['success'] = "Usuario eliminado correctamente";
        } else {
            $_SESSION['error'] = "Error al eliminar el usuario";
        }

        header("Location: index.php?controller=Usuario&action=mostrarUsuario");
        exit();
    }

    public function crearNuevoUsuario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contrasena = $_POST['contrasena'] ?? '';
            $especialidad = $_POST['especialidad'] ?? '';
            $anios_empresa = $_POST['anios_empresa'] ?? 0;
            $foto= $_POST['foto'] ??'';
            $tipo = $_POST['tipo'] ?? 'usuario';
    
            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $targetDir = "/assets/images/perfil/";
                $fileName = uniqid() . "_" . basename($_FILES['foto']['name']);
                $targetFilePath = $targetDir . $fileName;
    
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $targetFilePath)) {
                    $foto = $targetFilePath;
                } else {
                    $_SESSION['error'] = "Error al subir la imagen.";
                    $this->view = "crearNuevoUsuario";
                    return;
                }
            }
    
            $this->usuario = new Usuario();
            if ($this->usuario->crearUsuario($nombre, $email, $contrasena, $especialidad, $anios_empresa, $tipo, $foto)) {
                $_SESSION['success'] = "Usuario creado correctamente";
                header("Location: index.php?controller=Usuario&action=mostrarUsuario");
                exit();
            } else {
                $_SESSION['error'] = "Error al crear el usuario";
            }
        }
    
        $this->view = "crearNuevoUsuario";
    }






    public function modificarUsuario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }
    
        $id_usuario = $_GET['id_usuario'] ?? null;
        if (!$id_usuario) {
            $_SESSION['error'] = "ID de usuario no proporcionado";
            header("Location: index.php?controller=Usuario&action=mostrarUsuario");
            exit();
        }
    
        $this->usuario = new Usuario();
        $usuario = $this->usuario->obtenerPorId($id_usuario);
    
        if (!$usuario) {
            $_SESSION['error'] = "Usuario no encontrado";
            header("Location: index.php?controller=Usuario&action=mostrarUsuario");
            exit();
        }
    
        $this->view = "modificarUsuario";
        return ['usuario' => $usuario];
    }
    
    public function actualizarUsuario() {
        if (!isset($_SESSION['usuario']) || $_SESSION['usuario']['tipo'] !== 'admin') {
            header("Location: index.php?controller=Usuario&action=login");
            exit();
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_POST['id_usuario'] ?? null;
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $especialidad = $_POST['especialidad'] ?? '';
            $anios_empresa = $_POST['anios_empresa'] ?? 0;
            $tipo = $_POST['tipo'] ?? 'usuario';
            $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
            $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';
    
            if ($nueva_contrasena !== $confirmar_contrasena) {
                $_SESSION['error'] = "Las contraseñas no coinciden";
                header("Location: index.php?controller=Usuario&action=modificarUsuario&id_usuario=" . $id_usuario);
                exit();
            }
    
            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $targetDir = "/assets/images/perfil/";
                $fileName = uniqid() . "_" . basename($_FILES['foto']['name']);
                $targetFilePath = $targetDir . $fileName;
    
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $targetFilePath)) {
                    $foto = $targetFilePath;
                } else {
                    $_SESSION['error'] = "Error al subir la imagen.";
                    header("Location: index.php?controller=Usuario&action=modificarUsuario&id_usuario=" . $id_usuario);
                    exit();
                }
            }
    
            $this->usuario = new Usuario();
            if ($this->usuario->actualizarUsuario($id_usuario, $nombre, $email, $especialidad, $anios_empresa, $tipo, $nueva_contrasena, $foto)) {
                $_SESSION['success'] = "Usuario actualizado correctamente";
                header("Location: index.php?controller=Usuario&action=usuarioindividual&id_usuario=" . $id_usuario);
                exit();
            } else {
                $_SESSION['error'] = "Error al actualizar el usuario";
                header("Location: index.php?controller=Usuario&action=modificarUsuario&id_usuario=" . $id_usuario);
                exit();
            }
        }
    }

    public function obtenerUsuariosPaginados() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 12;
        $offset = ($page - 1) * $limit;
    
        $this->usuario = new Usuario();
        $usuarios = $this->usuario->obtenerUsuariosPaginados($offset, $limit);
        
        $usuarios = array_filter($usuarios, function($usuario) {
            return $usuario['nombre'] !== "Usuario eliminado";
        });
    
        $totalUsuarios = $this->usuario->contarTotalUsuarios();
    
        $hasMore = ($page * $limit) < $totalUsuarios;
    
        header('Content-Type: application/json');
        echo json_encode([
            'usuarios' => array_values($usuarios),
            'hasMore' => $hasMore
        ]);
        exit;
    }

}

