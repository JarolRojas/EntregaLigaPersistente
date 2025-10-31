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

    <h2>Resultados de Partidos</h2>
    
    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo $tipoMensaje; ?>">
            <?php echo $mensaje; ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Filtrar por Jornada</h3>
        <form method="GET" action="" style="display: flex; gap: 10px; align-items: flex-end;">
            <div style="flex: 1; max-width: 300px;">
                <label for="jornada">Jornada:</label>
                <select id="jornada" name="jornada" onchange="this.form.submit()">
                    <?php for ($i = 1; $i <= (max($jornadas) ?? 10); $i++): ?>
                        <option value="<?php echo $i; ?>" <?php echo $i === $jornadaSeleccionada ? 'selected' : ''; ?>>
                            Jornada <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
    
    <div class="card">
        <h3>Partidos Jornada <?php echo $jornadaSeleccionada; ?></h3>
        
        <?php if (empty($partidos)): ?>
            <p style="color: #999; padding: 20px; text-align: center;">No hay partidos en esta jornada.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Local</th>
                        <th>Resultado</th>
                        <th>Visitante</th>
                        <th>Estadio Local</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($partidos as $partido): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($partido->getEquipoLocal()->getNombre()); ?></strong>
                            </td>
                            <td style="text-align: center; font-weight: bold; font-size: 16px;">
                                <?php 
                                    $resultado = $partido->getResultado();
                                    $textoResultado = ($resultado === '1') ? 'Local' : (($resultado === '2') ? 'Visitante' : 'Empate');
                                    echo $textoResultado . ' (' . $resultado . ')';
                                ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($partido->getEquipoVisitante()->getNombre()); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($partido->getEquipoLocal()->getEstadio()); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <h3>Registrar Nuevo Partido</h3>
        <form method="POST" action="">
            <input type="hidden" name="action" value="crear">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="equipo_local_id">Equipo Local *</label>
                    <select id="equipo_local_id" name="equipo_local_id" required>
                        <option value="">-- Selecciona un equipo --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo->getId(); ?>">
                                <?php echo htmlspecialchars($equipo->getNombre()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="equipo_visitante_id">Equipo Visitante *</label>
                    <select id="equipo_visitante_id" name="equipo_visitante_id" required>
                        <option value="">-- Selecciona un equipo --</option>
                        <?php foreach ($equipos as $equipo): ?>
                            <option value="<?php echo $equipo->getId(); ?>">
                                <?php echo htmlspecialchars($equipo->getNombre()); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label for="resultado">Resultado *</label>
                    <select id="resultado" name="resultado" required>
                        <option value="">-- Selecciona resultado --</option>
                        <option value="1">1 - Gana Local</option>
                        <option value="X">X - Empate</option>
                        <option value="2">2 - Gana Visitante</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="jornada_nueva">Jornada *</label>
                    <input type="number" id="jornada_nueva" name="jornada" min="1" max="50" required value="<?php echo $jornadaSeleccionada; ?>">
                </div>
            </div>
            
            <button type="submit">Registrar Partido</button>
        </form>
    </div>

<?php include '../templates/layout_footer.php'; ?>
