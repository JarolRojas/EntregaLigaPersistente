<?php

class SessionManager
{

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Establece un valor en la sesión
     * @param string $clave
     * @param mixed $valor
     * @return void
     */
    public static function establecer(string $clave, mixed $valor): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION[$clave] = $valor;
    }


    /**
     * Obtiene un valor de la sesión
     * @param string $clave
     * @param mixed $predeterminado
     * @return mixed
     */
    public static function obtener(string $clave, mixed $predeterminado = null): mixed
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION[$clave] ?? $predeterminado;
    }


    /**
     * Verifica si una clave existe en la sesión
     * @param string $clave
     * @return bool
     */
    public static function existe(string $clave): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION[$clave]);
    }

    /**
     * Elimina una clave de la sesión
     * @param string $clave
     * @return void
     */
    public static function eliminar(string $clave): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION[$clave]);
    }

    /**
     * Limpia toda la sesión
     * @return void
     */
    public static function limpiar(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    /**
     * Obtiene el ID del equipo seleccionado en la sesión
     * @return int|null
     */
    public static function obtenerEquipoSeleccionado(): ?int
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION['equipo_id'] ?? null;
        return $id !== null ? (int)$id : null;
    }

    /**
     * Establece el ID del equipo seleccionado en la sesión
     * @param int $equipoId
     * @return void
     */
    public static function establecerEquipoSeleccionado(int $equipoId): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['equipo_id'] = $equipoId;
    }

    /**
     * Limpia el equipo seleccionado de la sesión
     * @return void
     */
    public static function limpiarEquipoSeleccionado(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['equipo_id']);
    }
}
