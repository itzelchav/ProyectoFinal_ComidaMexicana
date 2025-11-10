<?php
// conexion.php
// Cambia estos valores por los de tu panel de AwardSpace
$host = "fdb1034.awardspace.net";
$user = "4667265_editorial";
$pass = "JsALI5cC21S[#Ht/";
$dbname = "4667265_editorial";

$conexion = new mysqli($host, $user, $pass, $dbname);

// Si hay error, no matamos el script con die(); en su lugar
// dejamos $conexion como null y guardamos el mensaje en $conexion_error
if ($conexion->connect_error) {
    $conexion_error = "Error al conectar con la base de datos: " . $conexion->connect_error;
    // cerrar objeto si existe
    $conexion = null;
} else {
    $conexion_error = null;
    // establecer charset utf8mb4
    $conexion->set_charset("utf8mb4");
}
?>