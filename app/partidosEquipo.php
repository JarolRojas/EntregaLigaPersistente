<?php
require_once __DIR__ . '/../persistence/conf/database.php';
require_once __DIR__ . '/../persistence/DAO/PartidoDAO.php';
require_once __DIR__ . '/../persistence/DAO/EquipoDAO.php';
require_once __DIR__ . '/../model/Partido.php';
require_once __DIR__ . '/../model/Equipo.php';
require_once __DIR__ . '/../utils/SessionManager.php';
require_once __DIR__ . '/../utils/Validador.php';
new SessionManager();

$partidoDAO = new PartidoDAO();
$equipoDAO = new EquipoDAO();

// Obtener ID del equipo desde URL o sesión
$equipoId = isset($_GET['equipo_id']) && Validador::esIdValido($_GET['equipo_id'])
    ? (int)$_GET['equipo_id']
    : SessionManager::obtenerEquipoSeleccionado();

// Si no hay equipo_id, redirigir a equipos
if (!$equipoId) {
    header('Location: equipos.php');
    exit;
}

// Obtener datos del equipo
$equipo = $equipoDAO->obtenerPorId($equipoId);

if (!$equipo) {
    header('Location: equipos.php');
    exit;
}

// Establecer equipo en sesión
SessionManager::establecerEquipoSeleccionado($equipoId);

$titulo = 'Futbol Persistencia - Partidos de ' . htmlspecialchars($equipo->getNombre());

// Obtener jornadas disponibles
$jornadas = $partidoDAO->obtenerJornadas();

// Obtener jornada seleccionada
$jornadaSeleccionada = isset($_GET['jornada']) && Validador::esJornadaValida($_GET['jornada'])
    ? (int)$_GET['jornada']
    : null;

// Obtener partidos del equipo
if ($jornadaSeleccionada) {
    $partidos = $partidoDAO->obtenerPorEquipoYJornada($equipoId, $jornadaSeleccionada);
} else {
    $partidos = $partidoDAO->obtenerPorEquipo($equipoId);
}

// Función auxiliar para determinar si el equipo fue ganador
function determinarResultado($partido, $equipoId)
{
    if ($partido->getEquipoLocalId() === $equipoId) {
        // Es equipo local
        if ($partido->getResultado() === '1') return 'Ganó';
        if ($partido->getResultado() === 'X') return 'Empató';
        return 'Perdió';
    } else {
        // Es equipo visitante
        if ($partido->getResultado() === '2') return 'Ganó';
        if ($partido->getResultado() === 'X') return 'Empató';
        return 'Perdió';
    }
}

// Función auxiliar para determinar contrincante
function obtenerContrincante($partido, $equipoId)
{
    if ($partido->getEquipoLocalId() === $equipoId) {
        return $partido->getEquipoVisitante();
    } else {
        return $partido->getEquipoLocal();
    }
}

?>

<?php include '../templates/layout_header.php'; ?>

<h2 class="text-3xl font-bold text-gray-900 mb-6">Partidos de <?php echo htmlspecialchars($equipo->getNombre()); ?></h2>

<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filtrar por Jornada</h3>
    <form method="GET" action="" class="flex gap-4 items-end">
        <input type="hidden" name="equipo_id" value="<?php echo $equipoId; ?>">
        <div class="max-w-xs">
            <label for="jornada" class="block text-sm font-medium text-gray-700 mb-2">Jornada:</label>
            <select id="jornada" name="jornada" onchange="this.form.submit()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition">
                <option value="">-- Todas las jornadas --</option>
                <?php foreach ($jornadas as $jornada): ?>
                    <option value="<?php echo $jornada; ?>" <?php echo $jornada === $jornadaSeleccionada ? 'selected' : ''; ?>>
                        Jornada <?php echo $jornada; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Partidos <?php echo $jornadaSeleccionada ? 'Jornada ' . $jornadaSeleccionada : 'Totales'; ?> (<?php echo count($partidos); ?>)</h3>
    
    <?php if (empty($partidos)): ?>
        <p class="text-gray-500 py-8 text-center">No hay partidos registrados para este equipo.</p>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Jornada</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Condición</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Rival</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold text-gray-900">Resultado</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Estadio</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($partidos as $partido): ?>
                        <?php 
                            $esLocal = $partido->getEquipoLocalId() === $equipoId;
                            $condicion = $esLocal ? 'Local' : 'Visitante';
                            $resultado = determinarResultado($partido, $equipoId);
                            $contrincante = obtenerContrincante($partido, $equipoId);
                            $estadio = $esLocal ? $equipo->getEstadio() : $contrincante->getEstadio();
                        ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900"><?php echo $partido->getJornada(); ?></td>
                            <td class="px-6 py-4">
                                <span class="inline-block <?php echo $condicion === 'Local' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800'; ?> px-3 py-1 rounded text-sm font-medium">
                                    <?php echo $condicion; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4"><strong class="text-gray-900"><?php echo htmlspecialchars($contrincante->getNombre()); ?></strong></td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block <?php 
                                    if ($resultado === 'Ganó') {
                                        echo 'bg-green-100 text-green-800';
                                    } elseif ($resultado === 'Empató') {
                                        echo 'bg-yellow-100 text-yellow-800';
                                    } else {
                                        echo 'bg-red-100 text-red-800';
                                    }
                                ?> px-3 py-1 rounded font-semibold">
                                    <?php echo $resultado; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600"><?php echo htmlspecialchars($estadio); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../templates/layout_footer.php'; ?>