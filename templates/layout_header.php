<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Futbol Persistencia'; ?></title>
</head>
<body>
    <header>
        <div class="container">
            <h1>Futbol Persistencia</h1>
            <nav class="nav">
                <a href="/FutbolPersistencia/app/equipos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'equipos.php' ? 'active' : ''; ?>">Equipos</a>
                <a href="/FutbolPersistencia/app/partidos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'partidos.php' ? 'active' : ''; ?>">Partidos</a>
                <?php 
                    require_once __DIR__ . '/../utils/SessionManager.php';
                    $equipoId = SessionManager::obtenerEquipoSeleccionado();
                    if ($equipoId): 
                ?>
                    <a href="/FutbolPersistencia/app/partidosEquipo.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'partidosEquipo.php' ? 'active' : ''; ?>">Partidos del Equipo</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
