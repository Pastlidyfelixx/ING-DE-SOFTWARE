<?php
session_start();
include "../conexion.php";

if (empty($_SESSION['activo'])) {
    header('location: ../');
    exit();
}

$id_empresa = $_SESSION['idempresa'];

// Crear la tabla de turnos si no existe
$create_table_query = "CREATE TABLE IF NOT EXISTS turnos (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    id_empresa INT NOT NULL,
    id_usuario INT NOT NULL,
    nombre_turno VARCHAR(100) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    status TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuarios) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($conexion, $create_table_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_turno = isset($_POST['id_turno']) ? mysqli_real_escape_string($conexion, $_POST['id_turno']) : '';
    $id_usuario = mysqli_real_escape_string($conexion, $_POST['id_usuario']);
    $nombre_turno = mysqli_real_escape_string($conexion, $_POST['nombre_turno']);
    $hora_inicio = mysqli_real_escape_string($conexion, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conexion, $_POST['hora_fin']);

    if (empty($id_usuario) || empty($nombre_turno) || empty($hora_inicio) || empty($hora_fin)) {
        $_SESSION['alert'] = 'Todos los campos son obligatorios.';
        header("Location: creacion_turnos.php");
        exit();
    }

    if (!empty($id_turno)) {
        // Actualizar turno existente
        $query = "UPDATE turnos SET id_usuario = '$id_usuario', nombre_turno = '$nombre_turno', hora_inicio = '$hora_inicio', hora_fin = '$hora_fin' WHERE id_turno = '$id_turno' AND id_empresa = '$id_empresa'";
        $message = "Turno actualizado con éxito.";
    } else {
        // Insertar nuevo turno
        $query = "INSERT INTO turnos (id_empresa, id_usuario, nombre_turno, hora_inicio, hora_fin) VALUES ('$id_empresa', '$id_usuario', '$nombre_turno', '$hora_inicio', '$hora_fin')";
        $message = "Turno registrado con éxito.";
    }

    $resultado = mysqli_query($conexion, $query);

    if ($resultado) {
        $_SESSION['alert_success'] = $message;
    } else {
        $_SESSION['alert_danger'] = "Error al guardar el turno: " . mysqli_error($conexion);
    }

    header("Location: creacion_turnos.php");
    exit();
}
?>
