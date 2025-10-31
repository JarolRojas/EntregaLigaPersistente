<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) : 'Futbol Persistencia'; ?></title>
    <link rel="stylesheet" href="/FutbolPersistencia/assets/css/output.css">
</head>
<body class="bg-gray-50 text-gray-800">
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-blue-600">Futbol Persistencia</h1>
                <nav class="flex gap-6">
                    <a href="/FutbolPersistencia/app/equipos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'equipos.php' ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-600 transition'; ?>">Equipos</a>
                    <a href="/FutbolPersistencia/app/partidos.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'partidos.php' ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-600 transition'; ?>">Partidos</a>
                    <?php 
                        require_once __DIR__ . '/../utils/SessionManager.php';
                        $equipoId = SessionManager::obtenerEquipoSeleccionado();
                        if ($equipoId): 
                    ?>
                        <a href="/FutbolPersistencia/app/partidosEquipo.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'partidosEquipo.php' ? 'text-blue-600 font-semibold border-b-2 border-blue-600 pb-1' : 'text-gray-600 hover:text-blue-600 transition'; ?>">Partidos del Equipo</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <main class="bg-gray-50 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
