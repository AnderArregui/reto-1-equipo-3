<?php

require_once "models/Respuesta.php";
require_once 'models/Tema.php';
require_once 'models/Post.php';
require_once "models/Usuario.php";

class PostController {
    public $view;
    public $showLayout = true; 
    private $respuestaModel;
    private $temaModel;
    private $usuarioModel;
    private $postModel;
    

    public function __construct() {
        $this->view = "respuestas"; 
        $this->respuestaModel = new Respuesta();
        $this->temaModel = new Tema();
        $this->postModel = new Post();
        $this->usuarioModel = new Usuario();

        $nombre_usuario = $_SESSION['usuario']['nombre'] ?? null;

        if ($nombre_usuario) {
            $id_usuario = $_SESSION['usuario']['id_usuario'];
            if (!$id_usuario) {
                $_SESSION['mensaje'] = "Usuario no encontrado en la base de datos.";
                header("Location: /reto-1-equipo-3/php/index.php");
                exit();
            }
        }
    }

    public function publicarRespuesta() {
        $contenido = $_POST['contenido'] ?? '';
        $id_post = $_POST['id_post'] ?? null;
        $ruta = null;
    
        if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['media']['name']);

            $rutaDestino = __DIR__ . '/../assets/images/' . $nombreArchivo;
            
            if (move_uploaded_file($_FILES['media']['tmp_name'], $rutaDestino)) {

                $ruta = '/reto-1-equipo-3/php/assets/images/' . $nombreArchivo;
            } else {
                echo "Error al mover el archivo.";
            }
        }

        if ($contenido && $id_post && isset($_SESSION['usuario'])) {
            $id_usuario = $_SESSION['usuario']['id_usuario'];
    
            if ($id_usuario) {
                $respuestaModel = new Respuesta();
                $respuestaModel->crear($id_post, $id_usuario, $contenido, $ruta);
    
                header("Location: /reto-1-equipo-3/php/index.php?controller=Post&action=respuestas&id_post=" . $id_post . "#nueva-respuesta");
                exit();
            } else {
                echo "Usuario no encontrado.";
            }
        } else {
            echo "Error: Datos incompletos.";
        }
    }
    
    public function crearPregunta() {
        $temas = $this->temaModel->obtenerTodos();
        $this->view = "crearPregunta";

            $usuario = new Usuario();
            $usuario = $_SESSION['usuario'];  

            return [
                'usuario' => $usuario,
                'temas' => $temas
        ];
    }
    
    public function crearPreguntas() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_tema = null;
            if (!empty($_POST['nuevo_tema'])) {
                $nuevo_tema = $_POST['nuevo_tema'];
                $this->temaModel->crear($nuevo_tema);
                $id_tema =  $this->temaModel->obtenerIdPorNombre($nuevo_tema);

                if (!$id_tema) {
                    $_SESSION['mensaje'] = "Error al crear el tema.";
                    header("Location: index.php?controller=Post&action=crearPregunta");
                    exit();
                }
            } elseif (!empty($_POST['temaSelect'])) {
                $id_tema = $_POST['temaSelect'];
            }
    
            $nombre_usuario = $_SESSION['usuario']['nombre'] ?? null;
            if (!$nombre_usuario) {
                $_SESSION['mensaje'] = "Usuario no encontrado en la sesión.";
                header("Location: index.php");
                exit();
            }
    
            
            $id_usuario = $_SESSION['usuario']['id_usuario'];
            var_dump($id_usuario, $id_tema, $_POST['pregunta']);
    
            if (!empty($_POST['pregunta']) && $id_tema && $id_usuario) {
                if ($this->postModel->crear($id_usuario, $id_tema, $_POST['pregunta'])) {
                    $_SESSION['mensaje'] = "";
                } else {
                    $_SESSION['mensaje'] = "Error al crear la pregunta.";
                }
            } else {
                $_SESSION['mensaje'] = "Debe seleccionar un tema o crear uno nuevo y escribir una pregunta.";
            }
    
            header("Location: /reto-1-equipo-3/php/index.php?controller=Inicio&action=inicio");
            exit();
        }
    
        $this->view = "crearPregunta";
        $temas = $this->temaModel->obtenerTemas();
        return ['temas' => $temas];
    }
    
    
    
    
    
    
    
   
    public function init($id_post) {
        $id_post = $_GET['id_post'] ?? null;

        if ($id_post) {
            $respuestaModel = new Respuesta();
            $postModel = new Post();
            $usuarioModel = new Usuario();

            $usuarioPost = $_SESSION['usuario'];
            $tema = $postModel->obtenerPorId($id_post);
            $post = $respuestaModel->obtenerPost($id_post);
            $respuestas = $respuestaModel->obtenerPorPost($id_post);

            $usuario = $_SESSION['usuario'];

        
            return [
                    'post' => $post,
                    'respuestas' => $respuestas,
                    'tema' => $tema,
                    'usuario' => $usuario,
                    'usuarioPost' => $usuarioPost
            ];
        }

        return [
                'post' => null,
                'respuestas' => [],
                'tema' => null,
                'usuario' => null,
                'usuarioPost' => null,
        ];
    }
}