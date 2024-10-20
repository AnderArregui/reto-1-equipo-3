<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Login</title>
</head>
<body>
    <div class="logo">
        <img src="./src/logo sin fondo.png" alt="logo">
    </div>

    <!-- Formulario de login -->
    <form id="loginForm" method="post" action="index.php">
    <h1>Iniciar sesión</h1>
    <h1>Usuario</h1>
    <input type="text" name="username" id="username" placeholder="Username" required>
    <h1>Contraseña</h1>
    <input type="password" name="password" id="password" placeholder="Password" required>
    <div class="botones">
        <button type="submit">Login</button>
    </div>
</form>

<?php
// Conectar a la base de datos
require_once '../config/config.php';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Asegúrate de que las claves existan en el array $_POST
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $username = $_POST['username'];
            $password = $_POST['password']; 

            // Consultar la base de datos
            $stmt = $conn->prepare("SELECT * FROM Usuarios WHERE nombre = :username AND contrasena = :password");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
            $stmt->execute();

            // Si encuentra un resultado
            session_start(); // Inicia la sesión

        // Supón que has verificado las credenciales
        if ($stmt->rowCount() > 0) {
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC); // Obtener datos del usuario
// Almacena el nombre de usuario en la sesión
$_SESSION['usuario'] = $usuario['nombre']; // Asegúrate de que 'nombre' es el campo correcto
    header("Location: ../view/layout/inicio/inicio.php");
    exit();
} else {
    echo "<script>alert('Usuario o contraseña incorrectos');</script>";
}

        } else {
            echo "<script>alert('Faltan datos de usuario o contraseña');</script>";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


</body>
</html>