<div class="containerContacto">
            <div id="textoContacto">
                <h2 class="contactanos">Contáctanos</h2>
                <h2 class="contactanos2">Contáctanos</h2>

                <h3 class="infoContacto">Comunícate con tu superior para consultas específicas. También puedes enviar un correo electrónico aquí.</h3>
            </div>

            <!-- Estamos usando un servidor para enviar formularios, cuando enviamos un formulario manda un correo a el asociado "En este caso en el de egoitz" para saber quien quiere contactar con nosotros. -->
            <form action="https://formsubmit.co/4c859de0c84b56f333f92b3191ccfa5b" method="POST" id="formContacto">
                <div class="nombreContacto">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" required>
                </div>
                <div class="emailContacto">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="asuntoContacto">
                    <label for="asunto">Asunto</label>
                    <input type="text" name="asunto" id="asunto" required>
                </div>
                <div class="mensajeContacto">
                    <label for="mensaje">Mensaje</label>
                    <textarea name="mensaje" id="mensaje"></textarea>
                </div>
                <button type="submit">Enviar</button>
            </form>
            <div id="imgContacto">
                <img src="/assets/images/ander.png" class="imgJefes imgAnder">
                <img src="/assets/images/egoitz.png" class="imgJefes imgEgoitz">
                <img src="/assets/images/inigo.png" class="imgJefes imgInigo">
            </div>
            <div class="fondo-iframe">
    <iframe src="https://my.spline.design/robotfollowcursorforlandingpage-28f88d64f1a11b48f72e818c866d85af/">
    </iframe>
    <div class="cobertura"></div> 
</div>

</div>
            <script src="/assets/js/imgContacto.js"></script>
</div>
