<?php


require_once __DIR__ . '/../persistence/conf/database.php';
require_once __DIR__ . '/../persistence/DAO/PartidoDAO.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../model/Partido.php';
require_once __DIR__ . '/../model/Equipo.php';
require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../utils/Validador.php';

new SessionManager();

$titulo = 'Futbol Persistencia - Partidos';
$partidoDAO = new PartidoDAO();
$equipoDAO = new EquipoDAO();
$mensaje = '';
$tipoMensaje = '';

// Obtener jornadas disponibles
$jornadas = $partidoDAO->obtenerJornadas();
if (empty($jornadas)) {
    $jornadas = [1, 2, 3];
}

// Obtener jornada seleccionada
$jornadaSeleccionada = isset($_GET['jornada']) && Validador::esJornadaValida($_GET['jornada']) 
    ? (int)$_GET['jornada'] 
    : (max($jornadas) ?? 1);

// Procesar formulario de nuevo partido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'crear') {
    try {
        $equipoLocalId = Validador::obtenerPost('equipo_local_id', 'int');
        $equipoVisitanteId = Validador::obtenerPost('equipo_visitante_id', 'int');
        $resultado = Validador::obtenerPost('resultado', 'string');
        $jornada = Validador::obtenerPost('jornada', 'int');
        

        if (!Validador::esIdValido($equipoLocalId)) {
            throw new Exception('Selecciona un equipo local válido');
        }
        
        if (!Validador::esIdValido($equipoVisitanteId)) {
            throw new Exception('Selecciona un equipo visitante válido');
        }
        
        if ($equipoLocalId === $equipoVisitanteId) {
            throw new Exception('Los equipos local y visitante no pueden ser iguales');
        }
        
        if (!Validador::esResultadoValido($resultado)) {
            throw new Exception('Selecciona un resultado válido (1, X, 2)');
        }
        
        if (!Validador::esJornadaValida($jornada)) {
            throw new Exception('La jornada debe ser un número mayor a 0');
        }
        
        $partido = new Partido($equipoLocalId, $equipoVisitanteId, $resultado, $jornada);
        $partidoDAO->insertar($partido);
        
        $mensaje = 'Partido registrado correctamente';
        $tipoMensaje = 'success';
        
    } catch (Exception $e) {
        $mensaje = 'Error: ' . $e->getMessage();
        $tipoMensaje = 'error';
    }
}

// Obtener partidos de la jornada seleccionada
$partidos = $partidoDAO->obtenerPorJornada($jornadaSeleccionada);
$equipos = $equipoDAO->obtenerTodos();

?>

<?php include '../templates/layout_header.php'; ?>

<h2 class="text-3xl font-bold text-gray-900 mb-6">Resultados de Partidos</h2>

<?php if ($mensaje): ?>
    <div class="mb-6 p-4 rounded-lg <?php echo $tipoMensaje === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'; ?>">
        <?php echo $mensaje; ?>
    </div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtrar por Jornada</h3>
    <form method="GET" action="" class="flex gap-4 items-end">
        <div class="max-w-xs">
            <label for="jornada" class="block text-sm font-medium text-gray-700 mb-2">Jornada:</label>
            <select id="jornada" name="jornada" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                <?php for ($i = 1; $i <= (max($jornadas) ?? 10); $i++): ?>
                    <option value="<?php echo $i; ?>" <?php echo $i === $jornadaSeleccionada ? 'selected' : ''; ?>>
                        Jornada <?php echo $i; ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Partidos Jornada <?php echo $jornadaSeleccionada; ?></h3>
    
    <?php if (empty($partidos)): ?>
        <p class="text-gray-500 py-8 text-center">No hay partidos en esta jornada.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Local</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Resultado</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Visitante</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estadio Local</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($partidos as $partido): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <strong class="text-gray-900"><?php echo htmlspecialchars($partido->getEquipoLocal()->getNombre()); ?></strong>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded font-semibold">
                                    <?php 
                                        $resultado = $partido->getResultado();
                                        $textoResultado = ($resultado === '1') ? 'Local' : (($resultado === '2') ? 'Visitante' : 'Empate');
                                        echo $textoResultado . ' (' . $resultado . ')';
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <strong class="text-gray-900"><?php echo htmlspecialchars($partido->getEquipoVisitante()->getNombre()); ?></strong>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($partido->getEquipoLocal()->getEstadio()); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Registrar Nuevo Partido</h3>
    <form method="POST" action="">
        <input type="hidden" name="action" value="crear">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="equipo_local_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo Local *</label>
                <select id="equipo_local_id" name="equipo_local_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <option value="">-- Selecciona un equipo --</option>
                    <?php foreach ($equipos as $equipo): ?>
                        <option value="<?php echo $equipo->getId(); ?>">
                            <?php echo htmlspecialchars($equipo->getNombre()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="equipo_visitante_id" class="block text-sm font-medium text-gray-700 mb-2">Equipo Visitante *</label>
                <select id="equipo_visitante_id" name="equipo_visitante_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <option value="">-- Selecciona un equipo --</option>
                    <?php foreach ($equipos as $equipo): ?>
                        <option value="<?php echo $equipo->getId(); ?>">
                            <?php echo htmlspecialchars($equipo->getNombre()); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="resultado" class="block text-sm font-medium text-gray-700 mb-2">Resultado *</label>
                <select id="resultado" name="resultado" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                    <option value="">-- Selecciona resultado --</option>
                    <option value="1">1 - Gana Local</option>
                    <option value="X">X - Empate</option>
                    <option value="2">2 - Gana Visitante</option>
                </select>
            </div>
            
            <div>
                <label for="jornada_nueva" class="block text-sm font-medium text-gray-700 mb-2">Jornada *</label>
                <input type="number" id="jornada_nueva" name="jornada" min="1" max="50" required value="<?php echo $jornadaSeleccionada; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
            </div>
        </div>
        
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Registrar Partido</button>
    </form>
</div>

<?php include '../templates/layout_footer.php'; ?>
