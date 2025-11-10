<?php
require_once "conexion.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Comida Típica de México</title>
  <link rel="stylesheet" href="estilos.css">

  <!-- Tipografía y iconos -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header>
    <h1><i class="fa-solid fa-pepper-hot"></i> Comida Típica de México</h1>
    <p>Sabores, tradición y cultura que nos unen</p>
  </header>

  <nav class="navbar">
    <a href="#inicio" class="activo">Inicio</a>
    <a href="#comentario">Comentarios</a>
    <a href="#platillos">Platillos</a>
    <a href="#contacto">Contacto</a>
  </nav>

  <main>
    <!-- ================= INICIO ================= -->
    <section id="inicio" class="intro">
      <img src="comida_mexicana.jpg" alt="Comida mexicana tradicional" class="intro-img">
      <div class="intro-text">
        <h2>Bienvenido</h2>
        <p>
          La cocina mexicana es Patrimonio Cultural Inmaterial de la Humanidad por su historia, 
          técnicas y diversidad de ingredientes. Aquí podrás conocer algunos de los platillos más representativos del país, 
          compartir tus opiniones y dejar tus propios comentarios sobre la riqueza gastronómica que define a México.
        </p>
      </div>
    </section>
          
    <section id="carrusel-seccion">
            <div class="carrusel-contenedor">
                <div class="carrusel-imagenes">
                    <img src="tamales1.jpg" alt="Tamales tradicionales" class="carruselimg">
                    <img src="chiles-rellenos1.jpg" alt="Chiles rellenos" class="carruselimg">
                    <img src="cochinita-pibil.jpg" alt="Cochinita Pibil" class="carruselimg">
                    <img src="tacos-al-pastor1.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="marranito.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="Enchiladas.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="aguachile.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="gorditas.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="guacamole.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="conchas.jpg" alt="Tacos al Pastor" class="carruselimg">
                    <img src="gorditasnata.jpg" alt="Tacos al Pastor" class="carruselimg">
                </div>
                        
            </div>
	</section>

    <!-- ================= PLATILLOS DESTACADOS ================= -->
    <section id="destacados">
  <h2><i class="fa-solid fa-utensils"></i> Platillos destacados</h2>
  <div class="lista-platillos">
    <?php
    $sql = "SELECT * FROM platillo";
    $resultado = $conexion->query($sql);

    if ($resultado && $resultado->num_rows > 0) {
      while ($p = $resultado->fetch_assoc()) {
        echo "<div class='platillo'>
                <h3>{$p['nombre']}</h3>";
        if (!empty($p['imagen'])) {
          echo "<img src='{$p['imagen']}' alt='{$p['nombre']}' class='imagen-platillo'>";
        }
        echo "<p><strong>Región:</strong> {$p['region']}</p>
              <p>{$p['descripcion']}</p>
              <form method='POST' action=''>
                <input type='hidden' name='platillo_nombre' value='{$p['nombre']}'>
                <label>Tu nombre:</label>
                <input type='text' name='nombre_usuario' required>
                <label>Correo:</label>
                <input type='email' name='correo' required>
                <label>Carrera:</label>
                <input type='text' name='carrera'>
                <label>Comentario:</label>
                <textarea name='comentario' rows='3' required></textarea>
                <button type='submit' name='comentar_platillo'><i class='fa-solid fa-comment'></i> Comentar</button>
              </form>";

        // === Mostrar comentarios asociados ===
        $platillo_nombre = $p['nombre'];
        $comentarios_sql = $conexion->prepare("
          SELECT usuario.nombre_usuario, comentario.texto
          FROM comentario
          INNER JOIN usuario ON comentario.id_usuario = usuario.id_usuario
          INNER JOIN platillo ON comentario.id_platillo = platillo.id_platillo
          WHERE platillo.nombre = ?
          ORDER BY comentario.id_comentario DESC
        ");
        $comentarios_sql->bind_param("s", $platillo_nombre);
        $comentarios_sql->execute();
        $res = $comentarios_sql->get_result();

        if ($res && $res->num_rows > 0) {
          echo "<div class='comentarios'><h4>Comentarios recientes:</h4>";
          while ($fila = $res->fetch_assoc()) {
            echo "<p><strong>{$fila['nombre_usuario']}:</strong> {$fila['texto']}</p>";
          }
          echo "</div>";
        } else {
          echo "<p class='no-comentarios'>Aún no hay comentarios para este platillo.</p>";
        }

        echo "</div>"; // cierre del platillo
      }
    } else {
      echo "<p>No hay platillos registrados todavía.</p>";
    }
    ?>
  </div>
</section>


    <!-- ================= COMENTARIO GENERAL ================= -->
    <section id="comentario">
      <h2><i class="fa-solid fa-user"></i> Deja tu comentario general</h2>
      <form method="POST" action="">
        <label>Nombre:</label>
        <input type="text" name="nombre_usuario" required>

        <label>Correo electrónico:</label>
        <input type="email" name="correo" required>

        <label>Carrera universitaria:</label>
        <input type="text" name="carrera">

        <label>Comentario general sobre la comida mexicana:</label>
        <textarea name="comentario_general" rows="4" required></textarea>

        <button type="submit" name="guardar_general"><i class="fa-solid fa-paper-plane"></i> Enviar</button>
      </form>
      <?php
      if (isset($_POST['guardar_general']) && $conexion) {
          $nombre = trim($_POST['nombre_usuario']);
          $correo = trim($_POST['correo']);
          $carrera = trim($_POST['carrera']);
          $comentario = trim($_POST['comentario_general']);

          $check = $conexion->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
          $check->bind_param("s", $correo);
          $check->execute();
          $res = $check->get_result();

          if ($res && $res->num_rows > 0) {
              $u = $res->fetch_assoc();
              $id_usuario = $u['id_usuario'];
          } else {
              $add_user = $conexion->prepare("INSERT INTO usuario (nombre_usuario, correo, carrera) VALUES (?, ?, ?)");
              $add_user->bind_param("sss", $nombre, $correo, $carrera);
              $add_user->execute();
              $id_usuario = $conexion->insert_id;
          }

          $stmt = $conexion->prepare("INSERT INTO comentario (texto, id_usuario, id_platillo) VALUES (?, ?, NULL)");
          $stmt->bind_param("si", $comentario, $id_usuario);
          $stmt->execute();

          echo "<p class='ok'>✅ Comentario guardado correctamente.</p>";
      }
      ?>
    </section>

    <!-- ================= GUARDAR COMENTARIOS DE PLATILLOS ================= -->
    <?php
    if (isset($_POST['comentar_platillo']) && $conexion) {
        $nombre = trim($_POST['nombre_usuario']);
        $correo = trim($_POST['correo']);
        $carrera = trim($_POST['carrera']);
        $comentario = trim($_POST['comentario']);
        $nombre_platillo = trim($_POST['platillo_nombre']);

        // Verificar usuario
        $check_user = $conexion->prepare("SELECT id_usuario FROM usuario WHERE correo = ?");
        $check_user->bind_param("s", $correo);
        $check_user->execute();
        $res_user = $check_user->get_result();

        if ($res_user && $res_user->num_rows > 0) {
            $u = $res_user->fetch_assoc();
            $id_usuario = $u['id_usuario'];
        } else {
            $add_user = $conexion->prepare("INSERT INTO usuario (nombre_usuario, correo, carrera) VALUES (?, ?, ?)");
            $add_user->bind_param("sss", $nombre, $correo, $carrera);
            $add_user->execute();
            $id_usuario = $conexion->insert_id;
        }

        // Buscar platillo
        $busca_platillo = $conexion->prepare("SELECT id_platillo FROM platillo WHERE nombre = ?");
        $busca_platillo->bind_param("s", $nombre_platillo);
        $busca_platillo->execute();
        $res_platillo = $busca_platillo->get_result();

        if ($res_platillo && $res_platillo->num_rows > 0) {
            $p = $res_platillo->fetch_assoc();
            $id_platillo = $p['id_platillo'];
        } else {
            $nuevo = $conexion->prepare("INSERT INTO platillo (nombre, descripcion, region, imagen) VALUES (?, '', '', '')");
            $nuevo->bind_param("s", $nombre_platillo);
            $nuevo->execute();
            $id_platillo = $conexion->insert_id;
        }

        // Guardar comentario
        $stmt = $conexion->prepare("INSERT INTO comentario (texto, id_usuario, id_platillo) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $comentario, $id_usuario, $id_platillo);
        $stmt->execute();

        echo "<p class='ok'>💬 Comentario guardado correctamente en el platillo <strong>$nombre_platillo</strong>.</p>";
    }
    ?>
          <!-- ================= AGREGAR PLATILLO ================= -->
<section id="agregar-platillo">
  <h2><i class="fa-solid fa-plus"></i> Agregar un nuevo platillo</h2>
  <form method="POST" enctype="multipart/form-data">
    <label>Nombre del platillo:</label>
    <input type="text" name="nombre_platillo" required>

    <label>Región o estado de origen:</label>
    <input type="text" name="region">

    <label>Descripción o receta:</label>
    <textarea name="descripcion" rows="3" required></textarea>

    <label>Imagen (opcional):</label>
    <input type="file" name="imagen" accept="image/*">

    <button type="submit" name="guardar_platillo"><i class="fa-solid fa-save"></i> Guardar platillo</button>
  </form>
</section>


    <!-- ================= CONTACTO ================= -->
    <section id="contacto">
      <h2><i class="fa-solid fa-envelope"></i> Contacto</h2>
      <p>Si deseas conocer más sobre este proyecto o compartir recetas, puedes escribir a:</p>
      <p><strong>Itzel</strong> — Estudiante de Diseño Digital de Medios Interactivos</p>
      <p><i class="fa-solid fa-envelope"></i> itzel.chavez039@gmail.com</p>
    </section>
          
    <section id="ficha-personal">
      <h2><i class="fa-solid fa-id-card"></i> Sobre la creadora</h2>
      <div class="ficha-contenedor">
        <img src="ficha_itzel.png" alt="Ficha personal de Itzel Chávez" class="ficha-img">
      </div>
    </section>
  </main>

  <footer>
    <p>© 2025 Proyecto Final de Itzel | Comida Típica de México</p>
  </footer>
        <?php
// === GUARDAR NUEVO PLATILLO ===
if (isset($_POST['guardar_platillo']) && $conexion) {
    $nombre = trim($_POST['nombre_platillo']);
    $region = trim($_POST['region']);
    $descripcion = trim($_POST['descripcion']);

    // Si el usuario sube una imagen, la guardamos en la carpeta /imagenes
    $ruta_imagen = "";
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
        $nombre_archivo = basename($_FILES["imagen"]["name"]);
        $ruta_destino = "imagenes/" . $nombre_archivo;

        // Crear carpeta si no existe (AwardSpace lo permite)
        if (!is_dir("imagenes")) mkdir("imagenes", 0777, true);

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) {
            $ruta_imagen = $ruta_destino;
        }
    }

    // Insertar en la base de datos
    $sql = $conexion->prepare("INSERT INTO platillo (nombre, descripcion, region, imagen) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss", $nombre, $descripcion, $region, $ruta_imagen);
    $sql->execute();

    echo "<p class='ok'>🍽️ Platillo <strong>$nombre</strong> agregado correctamente.</p>";
}
?>
        
        <script>
        let indiceImagen = 0;
        const carruselImagenes = document.querySelector('.carrusel-imagenes');
        const totalImagenes = carruselImagenes.children.length;

        function mostrarImagen(n) {
            indiceImagen = n;
            if (indiceImagen >= totalImagenes) {
                indiceImagen = 0;
            }
            if (indiceImagen < 0) {
                indiceImagen = totalImagenes - 1;
            }
            // Mueve el contenedor de imágenes. Cada imagen ocupa el 100% del ancho del contenedor del carrusel.
            const desplazamiento = -indiceImagen * 100;
            carruselImagenes.style.transform = `translateX(${desplazamiento}%)`;
        }

        function moverCarrusel(n) {
            mostrarImagen(indiceImagen + n);
        }

        // Auto-play del carrusel (opcional, puedes omitir esta parte si no lo quieres automático)
        setInterval(() => {
            moverCarrusel(1);
        }, 3000); // Cambia de imagen cada 5 segundos

        // Inicializa el carrusel
        mostrarImagen(0);
    </script>

</body>
</html>
