<?php
require_once __DIR__ . '/utils/SessionManager.php';

new SessionManager();

$equipoId = SessionManager::obtenerEquipoSeleccionado();

// Si hay un equipo seleccionado, redirigir a partidos del equipo
if ($equipoId) {
    header('Location: /FutbolPersistencia/app/partidosEquipo.php');
} else {
    // Si no hay equipo, ir a equipos
    header('Location: /FutbolPersistencia/app/equipos.php');
}
exit;
?>
