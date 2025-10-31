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

<h2 class="text-3xl font-bold text-gray-900 mb-6">Gestión de Equipos</h2>

<?php if ($mensaje): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $tipoMensaje === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Agregar Nuevo Equipo</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="crear">

        <div class="mb-4">
            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Equipo *</label>
            <input type="text" id="nombre" name="nombre" required maxlength="100"
                placeholder="Ej: Real Madrid" 
                value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <div class="mb-6">
            <label for="estadio" class="block text-sm font-medium text-gray-700 mb-1">Estadio *</label>
            <input type="text" id="estadio" name="estadio" required maxlength="150"
                placeholder="Ej: Santiago Bernabéu" 
                value="<?php echo isset($_POST['estadio']) ? htmlspecialchars($_POST['estadio']) : ''; ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Crear Equipo</button>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Equipos Registrados (<?php echo count($equipos); ?>)</h3>

    <?php if (empty($equipos)): ?>
        <p class="text-gray-500 py-8 text-center">No hay equipos registrados aún.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Nombre</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estadio</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($equipos as $equipo): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4"><strong class="text-gray-900"><?php echo htmlspecialchars($equipo->getNombre()); ?></strong></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($equipo->getEstadio()); ?></td>
                            <td class="px-6 py-4">
                                <a href="partidosEquipo.php?equipo_id=<?php echo $equipo->getId(); ?>" class="text-blue-600 hover:text-blue-800 font-medium transition">
                                    Ver Partidos
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../templates/layout_footer.php'; ?>