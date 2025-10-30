<?php
require_once __DIR__ . '/../conf/database.php';
require_once __DIR__ . '/../../model/Equipo.php';

class EquipoDAO
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtiene todos los equipos de la base de datos
     * 
     * @return array Array de objetos Equipo
     */
    public function obtenerTodos(): array
    {
        $query = 'SELECT id, nombre, estadio, fecha_creacion FROM equipos ORDER BY nombre ASC';
        $datos = $this->db->fetchAll($query);

        $equipos = [];
        foreach ($datos as $fila) {
            $equipos[] = $this->mapearFila($fila);
        }

        return $equipos;
    }

    /**
     * Obtiene un equipo por su ID
     * 
     * @param int $id ID del equipo
     * @return Equipo|null
     */
    public function obtenerPorId(int $id): ?Equipo
    {
        $query = 'SELECT id, nombre, estadio, fecha_creacion FROM equipos WHERE id = ?';
        $fila = $this->db->fetchOne($query, [$id], 'i');

        return $fila ? $this->mapearFila($fila) : null;
    }

    /**
     * Obtiene un equipo por su nombre
     * 
     * @param string $nombre Nombre del equipo
     * @return Equipo|null
     */
    public function obtenerPorNombre(string $nombre): ?Equipo
    {
        $query = 'SELECT id, nombre, estadio, fecha_creacion FROM equipos WHERE nombre = ?';
        $fila = $this->db->fetchOne($query, [$nombre], 's');

        return $fila ? $this->mapearFila($fila) : null;
    }

    /**
     * Inserta un nuevo equipo en la base de datos
     * 
     * @param Equipo $equipo Objeto Equipo a insertar
     * @return bool True si la inserción fue exitosa
     */
    public function insertar(Equipo $equipo): bool
    {
        // Validar que el equipo no exista
        if ($this->obtenerPorNombre($equipo->getNombre()) !== null) {
            throw new \Exception('El equipo ' . htmlspecialchars($equipo->getNombre()) . ' ya existe en la base de datos');
        }

        $query = 'INSERT INTO equipos (nombre, estadio) VALUES (?, ?)';

        try {
            $this->db->executeQuery($query, [
                $equipo->getNombre(),
                $equipo->getEstadio()
            ], 'ss');

            $equipo->setId($this->db->getLastInsertId());
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error al insertar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un equipo existente
     * 
     * @param Equipo $equipo Objeto Equipo a actualizar
     * @return bool True si la actualización fue exitosa
     */
    public function actualizar(Equipo $equipo): bool
    {
        if ($equipo->getId() <= 0) {
            throw new \Exception('El ID del equipo no es válido');
        }

        $query = 'UPDATE equipos SET nombre = ?, estadio = ? WHERE id = ?';

        try {
            $this->db->executeQuery($query, [
                $equipo->getNombre(),
                $equipo->getEstadio(),
                $equipo->getId()
            ], 'ssi');

            return $this->db->getAffectedRows() > 0;
        } catch (\Exception $e) {
            throw new \Exception('Error al actualizar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un equipo de la base de datos
     * 
     * @param int $id ID del equipo a eliminar
     * @return bool True si la eliminación fue exitosa
     */
    public function eliminar(int $id): bool
    {
        $query = 'DELETE FROM equipos WHERE id = ?';

        try {
            $this->db->executeQuery($query, [$id], 'i');
            return $this->db->getAffectedRows() > 0;
        } catch (\Exception $e) {
            throw new \Exception('Error al eliminar equipo: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el total de equipos en la base de datos
     * 
     * @return int
     */
    public function obtenerTotal(): int
    {
        $query = 'SELECT COUNT(*) as total FROM equipos';
        $resultado = $this->db->fetchOne($query);
        return $resultado['total'] ?? 0;
    }

    /**
     * Mapea una fila de la BD a un objeto Equipo
     * 
     * @param array $fila Fila de la base de datos
     * @return Equipo
     */
    private function mapearFila(array $fila): Equipo
    {
        $equipo = new Equipo(
            $fila['nombre'],
            $fila['estadio'],
            (int)$fila['id'],
            $fila['fecha_creacion']
        );

        return $equipo;
    }
}
