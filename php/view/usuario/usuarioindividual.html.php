<?php
    $infoUsuario = $dataToView['data']['infoUsuario'];

    // Función para formatear el tiempo transcurrido
    function formatearTiempo($minutos) {
        if ($minutos < 60) {
            return $minutos . ' minuto' . ($minutos !== 1 ? 's' : '');
        } elseif ($minutos < 1440) { // menos de 24 horas
            $horas = floor($minutos / 60);
            return $horas . ' hora' . ($horas !== 1 ? 's' : '');
        } else { // más de 24 horas
            $dias = floor($minutos / 1440);
            return $dias . ' día' . ($dias !== 1 ? 's' : '');
        }
    }
?>

<div class="usuario-info">
    <img class="imagenUsuario" src="<?php echo isset($infoUsuario['foto']) ? htmlspecialchars($infoUsuario['foto']) : 'https://via.placeholder.com/150'; ?>" alt="Imagen de <?php echo isset($infoUsuario['nombre']) ? htmlspecialchars($infoUsuario['nombre']) : 'Usuario desconocido'; ?>">
    <h2><?php echo isset($infoUsuario['nombre']) ? htmlspecialchars($infoUsuario['nombre']) : 'Nombre no disponible'; ?></h2>
    <p>Email: <?php echo isset($infoUsuario['email']) ? htmlspecialchars($infoUsuario['email']) : 'No disponible'; ?></p>
    <p>Especialidad: <?php echo isset($infoUsuario['especialidad']) ? htmlspecialchars($infoUsuario['especialidad']) : 'No disponible'; ?></p>
    <p>Años en la empresa: <?php echo isset($infoUsuario['anios_empresa']) ? htmlspecialchars($infoUsuario['anios_empresa']) : 'No disponible'; ?></p>
    
    <p>Tiempo desde la última interaccion: 
        <?php 
            $totalRespuestas = isset($infoUsuario['total_respuestas']) ? $infoUsuario['total_respuestas'] : 0;
            if ($totalRespuestas === 0) {
                echo "No ha posteado ninguna respuesta.";
            } else {
                $minutosDesdeUltimaRespuesta = isset($infoUsuario['minutos_desde_ultima_respuesta']) ? $infoUsuario['minutos_desde_ultima_respuesta'] : 0;
                echo formatearTiempo($minutosDesdeUltimaRespuesta);
            }
        ?>
    </p>

    <div class="stats">
        <div class="stat-item">
            <span><?php echo isset($infoUsuario['total_preguntas']) ? htmlspecialchars($infoUsuario['total_preguntas']) : '0'; ?></span>
            <p>Preguntas</p>
        </div>
        <div class="stat-item">
            <span><?php echo isset($infoUsuario['total_respuestas']) ? htmlspecialchars($infoUsuario['total_respuestas']) : '0'; ?></span>
            <p>Respuestas</p>
        </div>
        <div class="stat-item">
            <span><?php echo isset($infoUsuario['total_likes']) ? htmlspecialchars($infoUsuario['total_likes']) : '0'; ?></span>
            <p>Likes</p>
        </div>
    </div>
</div>



<?php if ($_SESSION['usuario']['tipo'] === 'admin'): ?>
    <div class="admin-section">
        <h3>Preguntas del Usuario</h3>
        <?php $preguntas = $dataToView['data']['preguntas']; ?>
        <?php if (!empty($preguntas)): ?>
            <?php foreach ($preguntas as $pregunta): ?>
                <div class="preguntas">
                <div class="pregunta">
                    <a href="index.php?controller=Post&action=pregunta&id_post=<?php echo $pregunta['id_post']; ?>">
                        <p class="contenidoPregunta"><?php echo htmlspecialchars($pregunta['contenido']); ?></p>
                    </a>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($pregunta['fecha']); ?></p>
                    <form action="index.php?controller=Usuario&action=eliminarPregunta" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta pregunta y todas sus respuestas?');">
                        <input type="hidden" name="id_post" value="<?php echo $pregunta['id_post']; ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Este usuario no tiene preguntas.</p>
        <?php endif; ?>

        <h3>Respuestas del Usuario</h3>
        <?php $respuestas = $dataToView['data']['respuestas']; ?>
        <?php if (!empty($respuestas)): ?>
            <?php foreach ($respuestas as $respuesta): ?>
                <div class="respuestas">
                <div class="respuesta" style="width:90%">
                    <a href="index.php?controller=Post&action=respuestas&id_post=<?php echo $respuesta['id_post']; ?>">
                        <p class="contenidoRespuesta"><?php echo htmlspecialchars($respuesta['contenido']); ?></p>
                    </a>
                    <p><strong>Fecha:</strong> <?php echo htmlspecialchars($respuesta['fecha']); ?></p>
                    <form action="index.php?controller=Usuario&action=eliminarRespuesta" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta respuesta?');">
                        <input type="hidden" name="id_respuesta" value="<?php echo $respuesta['id_respuesta']; ?>">
                        <button type="submit">Eliminar</button>
                    </form>
                </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Este usuario no tiene respuestas.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>
