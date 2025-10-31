<?php

require_once __DIR__ . '/../persistence/conf/database.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../model/Equipo.php';
require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../utils/Validador.php';

new SessionManager();

$titulo = 'Futbol Persistencia - Equipos';
$equipoDAO = new EquipoDAO();
$mensaje = '';
$tipoMensaje = '';

// Procesar formulario de nuevo equipo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    try {
        $nombre = Validador::obtenerPost('nombre', 'string');
        $estadio = Validador::obtenerPost('estadio', 'string');

        // Validaciones
        if (!$nombre) {
            throw new Exception('El nombre del equipo es requerido');
        }

        if (!Validador::esNombreEquipoValido($nombre)) {
            throw new Exception('El nombre debe tener entre 3 y 100 caracteres');
        }

        if (!$estadio) {
            throw new Exception('El estadio es requerido');
        }

        if (!Validador::esEstadioValido($estadio)) {
            throw new Exception('El estadio debe tener entre 3 y 150 caracteres');
        }

        $equipo = new Equipo($nombre, $estadio);
        $equipoDAO->insertar($equipo);

        $mensaje = 'Equipo "' . htmlspecialchars($nombre) . '" creado correctamente';
        $tipoMensaje = 'success';
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

// Obtener todos los equipos
$equipos = $equipoDAO->obtenerTodos();

?>

<?php include '../templates/layout_header.php'; ?>

<h2>Gestión de Equipos</h2>

<?php if ($mensaje): ?>
    <div class="alert alert-<?php echo $tipoMensaje; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="card">
    <h3>Agregar Nuevo Equipo</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="crear">

        <div class="form-group">
            <label for="nombre">Nombre del Equipo *</label>
            <input type="text" id="nombre" name="nombre" required maxlength="100"
                placeholder="Ej: Real Madrid" value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="estadio">Estadio *</label>
            <input type="text" id="estadio" name="estadio" required maxlength="150"
                placeholder="Ej: Santiago Bernabéu" value="<?php echo isset($_POST['estadio']) ? htmlspecialchars($_POST['estadio']) : ''; ?>">
        </div>

        <button type="submit">Crear Equipo</button>
    </form>
</div>

<div class="card">
    <h3>Equipos Registrados (<?php echo count($equipos); ?>)</h3>

    <?php if (empty($equipos)): ?>
        <p style="color: #999; padding: 20px; text-align: center;">No hay equipos registrados aún.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Estadio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($equipo->getNombre()); ?></strong></td>
                        <td><?php echo htmlspecialchars($equipo->getEstadio()); ?></td>
                        <td>
                            <a href="partidosEquipo.php?equipo_id=<?php echo $equipo->getId(); ?>" class="link">
                                Ver Partidos
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../templates/layout_footer.php'; ?>