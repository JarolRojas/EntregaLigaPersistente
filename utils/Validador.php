<?php

/**
 * Clase Validador
 * Proporciona métodos para validar datos de entrada
 */
class Validador
{

    /**
     * Valida que una cadena no esté vacía
     * 
     * @param string $valor Valor a validar
     * @return bool
     */
    public static function noEstaVacio(string $valor): bool
    {
        return !empty(trim($valor));
    }

    /**
     * Valida la longitud mínima de una cadena
     * 
     * @param string $valor Valor a validar
     * @param int $minimo Longitud mínima
     * @return bool
     */
    public static function longitudMinima(string $valor, int $minimo): bool
    {
        return strlen(trim($valor)) >= $minimo;
    }

    /**
     * Valida la longitud máxima de una cadena
     * 
     * @param string $valor Valor a validar
     * @param int $maximo Longitud máxima
     * @return bool
     */
    public static function longitudMaxima(string $valor, int $maximo): bool
    {
        return strlen(trim($valor)) <= $maximo;
    }

    /**
     * Valida un rango de longitud
     * 
     * @param string $valor Valor a validar
     * @param int $minimo Longitud mínima
     * @param int $maximo Longitud máxima
     * @return bool
     */
    public static function longitudRango(string $valor, int $minimo, int $maximo): bool
    {
        $length = strlen(trim($valor));
        return $length >= $minimo && $length <= $maximo;
    }

    /**
     * Valida que sea un número entero
     * 
     * @param mixed $valor Valor a validar
     * @return bool
     */
    public static function esEntero(mixed $valor): bool
    {
        return filter_var($valor, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Valida que sea un número
     * 
     * @param mixed $valor Valor a validar
     * @return bool
     */
    public static function esNumero(mixed $valor): bool
    {
        return is_numeric($valor);
    }

    /**
     * Valida que sea un email válido
     * 
     * @param string $email Email a validar
     * @return bool
     */
    public static function esEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida que sea una URL válida
     * 
     * @param string $url URL a validar
     * @return bool
     */
    public static function esURL(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Valida un resultado de quiniela (1, X, 2)
     * 
     * @param string $resultado Resultado a validar
     * @return bool
     */
    public static function esResultadoValido(string $resultado): bool
    {
        return in_array($resultado, ['1', 'X', '2'], true);
    }

    /**
     * Valida que sea un número de jornada válido
     * 
     * @param mixed $jornada Jornada a validar
     * @return bool
     */
    public static function esJornadaValida(mixed $jornada): bool
    {
        return self::esEntero($jornada) && intval($jornada) > 0;
    }

    /**
     * Valida un ID de equipo
     * 
     * @param mixed $id ID a validar
     * @return bool
     */
    public static function esIdValido(mixed $id): bool
    {
        return self::esEntero($id) && intval($id) > 0;
    }

    /**
     * Sanitiza una cadena para evitar inyecciones
     * 
     * @param string $valor Valor a sanitizar
     * @return string
     */
    public static function sanitizar(string $valor): string
    {
        return htmlspecialchars(trim($valor), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Valida que un nombre de equipo sea válido
     * 
     * @param string $nombre Nombre a validar
     * @return bool
     */
    public static function esNombreEquipoValido(string $nombre): bool
    {
        return self::noEstaVacio($nombre) &&
            self::longitudRango($nombre, 3, 100);
    }

    /**
     * Valida que un nombre de estadio sea válido
     * 
     * @param string $estadio Estadio a validar
     * @return bool
     */
    public static function esEstadioValido(string $estadio): bool
    {
        return self::noEstaVacio($estadio) &&
            self::longitudRango($estadio, 3, 150);
    }

    /**
     * Obtiene un valor POST de forma segura
     * 
     * @param string $clave Clave del valor
     * @param string $tipo Tipo de validación esperado (string, int, email, etc.)
     * @return mixed|null
     */
    public static function obtenerPost(string $clave, string $tipo = 'string'): mixed
    {
        if (!isset($_POST[$clave])) {
            return null;
        }

        $valor = $_POST[$clave];

        switch ($tipo) {
            case 'int':
                return filter_var($valor, FILTER_VALIDATE_INT);
            case 'email':
                return filter_var($valor, FILTER_VALIDATE_EMAIL);
            case 'url':
                return filter_var($valor, FILTER_VALIDATE_URL);
            case 'string':
            default:
                return self::sanitizar($valor);
        }
    }

    /**
     * Obtiene un valor GET de forma segura
     * 
     * @param string $clave Clave del valor
     * @param string $tipo Tipo de validación esperado (string, int, email, etc.)
     * @return mixed|null
     */
    public static function obtenerGet(string $clave, string $tipo = 'string'): mixed
    {
        if (!isset($_GET[$clave])) {
            return null;
        }

        $valor = $_GET[$clave];

        switch ($tipo) {
            case 'int':
                return filter_var($valor, FILTER_VALIDATE_INT);
            case 'email':
                return filter_var($valor, FILTER_VALIDATE_EMAIL);
            case 'url':
                return filter_var($valor, FILTER_VALIDATE_URL);
            case 'string':
            default:
                return self::sanitizar($valor);
        }
    }
}
