<?php
session_start();
include "../conexion.php";

if (empty($_SESSION['activo']) || empty($_GET['id'])) {
    header('location: ../');
    exit();
}

$id_turno = $_GET['id'];
$id_empresa = $_SESSION['idempresa'];

$query = mysqli_query($conexion, "DELETE FROM turnos WHERE id_turno = $id_turno AND id_empresa = '$id_empresa'");

if ($query) {
    $_SESSION['alert_success'] = "Turno eliminado con éxito.";
} else {
    $_SESSION['alert_danger'] = "Error al eliminar el turno: " . mysqli_error($conexion);
}

header("Location: asignacion_turnos.php");
exit();
?>
