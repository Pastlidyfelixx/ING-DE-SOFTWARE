<?php
session_start();
include "../conexion.php";
include "includes/header.php";
$id_empresa=$_SESSION['idempresa'];
?>
<head>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</head>
<div class="card">
    <div class="card-body">
        <form action="guardar_turno.php" method="post" autocomplete="off" id="formulario_turno">       
            <input type="hidden" id="id_turno" name="id_turno">
            <div class="row">
                <!-- Empleado -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="id_usuario">Empleado</label>
                        <select id="id_usuario" class="form-control" name="id_usuario" required>
                            <option value="" disabled selected>Seleccionar Empleado</option>
                            <?php
                            $query_empleados = mysqli_query($conexion, "SELECT id_usuarios, nombres, apellido1 FROM usuarios WHERE id_empresa = '$id_empresa' AND status = 1");
                            while ($empleado = mysqli_fetch_assoc($query_empleados)) {
                                echo "<option value='{$empleado['id_usuarios']}'>{$empleado['nombres']} {$empleado['apellido1']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <!-- Nombre del Turno -->
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="nombre_turno">Nombre del Turno</label>
                        <input type="text" class="form-control" placeholder="Ej: Turno de Mañana" name="nombre_turno" id="nombre_turno" required>
                    </div>
                </div>
                 <!-- Horario -->
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="hora_inicio">Hora Inicio</label>
                        <input type="time" class="form-control" name="hora_inicio" id="hora_inicio" required>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="hora_fin">Hora Fin</label>
                        <input type="time" class="form-control" name="hora_fin" id="hora_fin" required>
                    </div>
                </div>
            </div>
            <!-- Botones -->
            <input type="submit" value="Guardar Turno" class="btn btn-primary" id="btnAccion">
            <input type="button" value="Cancelar" class="btn btn-secondary" id="btnNuevo" onclick="limpiarFormulario()">
        </form>
    </div>
</div>


<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered mt-2" id="tblTurnos">
        <thead class="thead-dark">
            <tr>
                <th>Empleado</th>
                <th>Nombre del Turno</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $query_turnos = mysqli_query($conexion, "SELECT t.*, u.nombres, u.apellido1 FROM turnos t INNER JOIN usuarios u ON t.id_usuario = u.id_usuarios WHERE t.id_empresa = '$id_empresa'");
            while ($data = mysqli_fetch_assoc($query_turnos)) {
                $estado = ($data['status'] == 1) ? '<span class="badge badge-success">Activo</span>' : '<span class="badge badge-danger">Inactivo</span>';
                ?>
                <tr>
                    <td><?php echo $data['nombres'] . ' ' . $data['apellido1']; ?></td>
                    <td><?php echo $data['nombre_turno']; ?></td>
                    <td><?php echo $data['hora_inicio']; ?></td>
                    <td><?php echo $data['hora_fin']; ?></td>
                    <td><?php echo $estado; ?></td>
                    <td>
                        <a href="#" onclick="editarTurno(<?php echo $data['id_turno']; ?>, <?php echo $data['id_usuario']; ?>, '<?php echo $data['nombre_turno']; ?>', '<?php echo $data['hora_inicio']; ?>', '<?php echo $data['hora_fin']; ?>')" class="btn btn-success btn-sm">
                            <i class='fas fa-edit'></i>
                        </a>
                        <form action="eliminar_turno.php?id=<?php echo $data['id_turno']; ?>" method="post" class="d-inline confirmar">
                            <button type="submit" class="btn btn-danger btn-sm"><i class='fas fa-trash-alt'></i></button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal de Confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirmar Acción</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea modificar el estado de este tipo contrato?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmBtn">Modificar Estado</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar tipo contrato -->
<div class="modal fade" id="editarModal" tabindex="-1" role="dialog" aria-labelledby="editarModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarModalLabel">Editar tipo contrato</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="actualizar_tipocontrato.php" method="post" id="formEditar">
                    <input type="hidden" id="idEditar" name="id">
                    <div class="form-group">
                        <label for="nombreEditar">Descripcion</label>
                        <input type="text" class="form-control" id="nombreEditar" name="nombre" required>
                    </div>
                    <input type="submit" value="Actualizar" class="btn btn-primary">
                </form>
            </div>
        </div>
    </div>
</div>


<!-- Modal de Alerta -->
<div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="alertModalLabel">Alerta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="alertMessage">Este es un mensaje de alerta.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery y Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
// Variable para guardar el ID del tipo de contrato
let tipocontratoId;

// Función para abrir el modal de confirmación
function abrirModalConfirmacion(id) {
    tipocontratoId = id; // Guardar el ID del garzón
    $('#confirmModal').modal('show'); // Mostrar el modal
}

// Función para confirmar el cambio de estado
$('#confirmBtn').on('click', function() {
    if (tipocontratoId) {
        // Redirigir a cambiar_estado.php con el ID del garzón
        window.location.href = `cambiar_estado.php?id=${tipocontratoId}`;
    }
});

// Función para abrir el modal de edición
function editartipocontrato(id, nombre, estado) {
    // Rellenar el formulario de edición
    $('#idEditar').val(id);
    $('#nombreEditar').val(nombre);
    $('#editarModal').modal('show'); // Mostrar el modal de edición
}

// Función para limpiar el formulario
function limpiar() {
    $('#formulario')[0].reset(); // Restablecer el formulario
}

// Función para mostrar alerta
function mostrarAlerta(mensaje) {
    $('#alertMessage').text(mensaje); // Rellenar el mensaje de alerta
    $('#alertModal').modal('show'); // Mostrar el modal de alerta
}
</script>

<?php include_once "includes/footer.php"; ?>