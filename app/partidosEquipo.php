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

<h2>Partidos de <?php echo htmlspecialchars($equipo->getNombre()); ?></h2>

<div class="card">
    <h3>Filtrar por Jornada</h3>
    <form method="GET" action="" style="display: flex; gap: 10px; align-items: flex-end;">
        <input type="hidden" name="equipo_id" value="<?php echo $equipoId; ?>">
        <div style="flex: 1; max-width: 300px;">
            <label for="jornada">Jornada:</label>
            <select id="jornada" name="jornada" onchange="this.form.submit()">
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

<div class="card">
    <h3>Partidos <?php echo $jornadaSeleccionada ? 'Jornada ' . $jornadaSeleccionada : 'Totales'; ?> (<?php echo count($partidos); ?>)</h3>
    
    <?php if (empty($partidos)): ?>
        <p style="color: #999; padding: 20px; text-align: center;">No hay partidos registrados para este equipo.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Jornada</th>
                    <th>Condición</th>
                    <th>Rival</th>
                    <th>Resultado</th>
                    <th>Estadio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($partidos as $partido): ?>
                    <?php 
                        $esLocal = $partido->getEquipoLocalId() === $equipoId;
                        $condicion = $esLocal ? 'Local' : 'Visitante';
                        $resultado = determinarResultado($partido, $equipoId);
                        $contrincante = obtenerContrincante($partido, $equipoId);
                        $estadio = $esLocal ? $equipo->getEstadio() : $contrincante->getEstadio();
                    ?>
                    <tr>
                        <td><?php echo $partido->getJornada(); ?></td>
                        <td><?php echo $condicion; ?></td>
                        <td><strong><?php echo htmlspecialchars($contrincante->getNombre()); ?></strong></td>
                        <td style="font-weight: bold; text-align: center;"><?php echo $resultado; ?></td>
                        <td><?php echo htmlspecialchars($estadio); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include '../templates/layout_footer.php'; ?>