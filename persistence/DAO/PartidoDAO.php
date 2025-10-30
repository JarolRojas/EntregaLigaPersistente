<?php
require_once __DIR__ . '/../conf/database.php';
require_once __DIR__ . '/../../model/Partido.php';
require_once __DIR__ . '/../../model/Equipo.php';
require_once __DIR__ . '/EquipoDAO.php';

class PartidoDAO
{
    private Database $db;
    private EquipoDAO $equipoDAO;
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->equipoDAO = new EquipoDAO();
    }

    /**
     * Obtiene todos los partidos de la base de datos
     * 
     * @return array Array de objetos Partido
     */
    public function obtenerTodos(): array
    {
        $query = 'SELECT id, equipo_local_id, equipo_visitante_id, resultado, jornada, fecha_creacion 
                  FROM partidos ORDER BY jornada DESC, id DESC';
        $datos = $this->db->fetchAll($query);

        $partidos = [];
        foreach ($datos as $fila) {
            $partidos[] = $this->mapearFila($fila);
        }

        return $partidos;
    }

    /**
     * Obtiene un partido por su ID
     * 
     * @param int $id ID del partido
     * @return Partido|null
     */
    public function obtenerPorId(int $id): ?Partido
    {
        $query = 'SELECT id, equipo_local_id, equipo_visitante_id, resultado, jornada, fecha_creacion 
                  FROM partidos WHERE id = ?';
        $fila = $this->db->fetchOne($query, [$id], 'i');

        return $fila ? $this->mapearFila($fila) : null;
    }

    /**
     * Obtiene todos los partidos de una jornada específica
     * 
     * @param int $jornada Número de jornada
     * @return array Array de objetos Partido
     */
    public function obtenerPorJornada(int $jornada): array
    {
        $query = 'SELECT id, equipo_local_id, equipo_visitante_id, resultado, jornada, fecha_creacion 
                  FROM partidos WHERE jornada = ? ORDER BY id ASC';
        $datos = $this->db->fetchAll($query, [$jornada], 'i');

        $partidos = [];
        foreach ($datos as $fila) {
            $partidos[] = $this->mapearFila($fila);
        }

        return $partidos;
    }

    /**
     * Obtiene todos los partidos de un equipo específico (como local o visitante)
     * 
     * @param int $equipoId ID del equipo
     * @return array Array de objetos Partido
     */
    public function obtenerPorEquipo(int $equipoId): array
    {
        $query = 'SELECT id, equipo_local_id, equipo_visitante_id, resultado, jornada, fecha_creacion 
                  FROM partidos 
                  WHERE equipo_local_id = ? OR equipo_visitante_id = ? 
                  ORDER BY jornada DESC, id DESC';
        $datos = $this->db->fetchAll($query, [$equipoId, $equipoId], 'ii');

        $partidos = [];
        foreach ($datos as $fila) {
            $partidos[] = $this->mapearFila($fila);
        }

        return $partidos;
    }

    /**
     * Obtiene todos los partidos de un equipo en una jornada específica
     * 
     * @param int $equipoId ID del equipo
     * @param int $jornada Número de jornada
     * @return array Array de objetos Partido
     */
    public function obtenerPorEquipoYJornada(int $equipoId, int $jornada): array
    {
        $query = 'SELECT id, equipo_local_id, equipo_visitante_id, resultado, jornada, fecha_creacion 
                  FROM partidos 
                  WHERE (equipo_local_id = ? OR equipo_visitante_id = ?) AND jornada = ?
                  ORDER BY id ASC';
        $datos = $this->db->fetchAll($query, [$equipoId, $equipoId, $jornada], 'iii');

        $partidos = [];
        foreach ($datos as $fila) {
            $partidos[] = $this->mapearFila($fila);
        }

        return $partidos;
    }

    /**
     * Obtiene todas las jornadas disponibles
     * 
     * @return array Array de números de jornada
     */
    public function obtenerJornadas(): array
    {
        $query = 'SELECT DISTINCT jornada FROM partidos ORDER BY jornada ASC';
        $datos = $this->db->fetchAll($query);

        $jornadas = [];
        foreach ($datos as $fila) {
            $jornadas[] = $fila['jornada'];
        }

        return $jornadas;
    }

    /**
     * Obtiene la máxima jornada registrada
     * 
     * @return int Número de la máxima jornada
     */
    public function obtenerMaximaJornada(): int
    {
        $query = 'SELECT MAX(jornada) as max_jornada FROM partidos';
        $resultado = $this->db->fetchOne($query);
        return $resultado['max_jornada'] ?? 0;
    }

    /**
     * Verifica si dos equipos ya han jugado en una jornada específica
     * 
     * @param int $equipoLocalId ID del equipo local
     * @param int $equipoVisitanteId ID del equipo visitante
     * @param int $jornada Número de jornada
     * @return bool True si ya existe un partido entre ellos en esa jornada
     */
    public function existePartido(int $equipoLocalId, int $equipoVisitanteId, int $jornada): bool
    {
        $query = 'SELECT id FROM partidos 
                  WHERE equipo_local_id = ? AND equipo_visitante_id = ? AND jornada = ?';
        $resultado = $this->db->fetchOne($query, [$equipoLocalId, $equipoVisitanteId, $jornada], 'iii');

        return $resultado !== null;
    }

    /**
     * Verifica si dos equipos ya han jugado en alguna jornada
     * 
     * @param int $equipoLocalId ID del equipo local
     * @param int $equipoVisitanteId ID del equipo visitante
     * @return bool True si ya existe un partido entre ellos en cualquier jornada
     */
    public function equiposHanJugado(int $equipoLocalId, int $equipoVisitanteId): bool
    {
        $query = 'SELECT id FROM partidos 
                  WHERE (equipo_local_id = ? AND equipo_visitante_id = ?) 
                  OR (equipo_local_id = ? AND equipo_visitante_id = ?)';
        $resultado = $this->db->fetchOne(
            $query,
            [$equipoLocalId, $equipoVisitanteId, $equipoVisitanteId, $equipoLocalId],
            'iiii'
        );

        return $resultado !== null;
    }

    /**
     * Inserta un nuevo partido en la base de datos
     * 
     * @param Partido $partido Objeto Partido a insertar
     * @return bool True si la inserción fue exitosa
     */
    public function insertar(Partido $partido): bool
    {
        // Validaciones
        if ($partido->getEquipoLocalId() === $partido->getEquipoVisitanteId()) {
            throw new \Exception('Un equipo no puede jugar contra sí mismo');
        }

        if (!in_array($partido->getResultado(), ['1', 'X', '2'])) {
            throw new \Exception('El resultado debe ser 1, X o 2');
        }

        if ($this->existePartido($partido->getEquipoLocalId(), $partido->getEquipoVisitanteId(), $partido->getJornada())) {
            throw new \Exception('Ya existe un partido entre estos equipos en esta jornada');
        }

        $query = 'INSERT INTO partidos (equipo_local_id, equipo_visitante_id, resultado, jornada) 
                  VALUES (?, ?, ?, ?)';

        try {
            $this->db->executeQuery($query, [
                $partido->getEquipoLocalId(),
                $partido->getEquipoVisitanteId(),
                $partido->getResultado(),
                $partido->getJornada()
            ], 'iisi');

            $partido->setId($this->db->getLastInsertId());
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error al insertar partido: ' . $e->getMessage());
        }
    }

    /**
     * Actualiza un partido existente
     * 
     * @param Partido $partido Objeto Partido a actualizar
     * @return bool True si la actualización fue exitosa
     */
    public function actualizar(Partido $partido): bool
    {
        if ($partido->getId() <= 0) {
            throw new \Exception('El ID del partido no es válido');
        }

        if (!in_array($partido->getResultado(), ['1', 'X', '2'])) {
            throw new \Exception('El resultado debe ser 1, X o 2');
        }

        $query = 'UPDATE partidos SET resultado = ? WHERE id = ?';

        try {
            $this->db->executeQuery($query, [
                $partido->getResultado(),
                $partido->getId()
            ], 'si');

            return $this->db->getAffectedRows() > 0;
        } catch (\Exception $e) {
            throw new \Exception('Error al actualizar partido: ' . $e->getMessage());
        }
    }

    /**
     * Elimina un partido de la base de datos
     * 
     * @param int $id ID del partido a eliminar
     * @return bool True si la eliminación fue exitosa
     */
    public function eliminar(int $id): bool
    {
        $query = 'DELETE FROM partidos WHERE id = ?';

        try {
            $this->db->executeQuery($query, [$id], 'i');
            return $this->db->getAffectedRows() > 0;
        } catch (\Exception $e) {
            throw new \Exception('Error al eliminar partido: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene el total de partidos en la base de datos
     * 
     * @return int
     */
    public function obtenerTotal(): int
    {
        $query = 'SELECT COUNT(*) as total FROM partidos';
        $resultado = $this->db->fetchOne($query);
        return $resultado['total'] ?? 0;
    }

    /**
     * Mapea una fila de la BD a un objeto Partido con equipos cargados
     * 
     * @param array $fila Fila de la base de datos
     * @return Partido
     */
    private function mapearFila(array $fila): Partido
    {
        $partido = new Partido(
            (int)$fila['equipo_local_id'],
            (int)$fila['equipo_visitante_id'],
            $fila['resultado'],
            (int)$fila['jornada'],
            (int)$fila['id'],
            $fila['fecha_creacion']
        );

        // Cargar información de equipos
        $equipoLocal = $this->equipoDAO->obtenerPorId((int)$fila['equipo_local_id']);
        $equipoVisitante = $this->equipoDAO->obtenerPorId((int)$fila['equipo_visitante_id']);

        if ($equipoLocal) {
            $partido->setEquipoLocal($equipoLocal);
        }

        if ($equipoVisitante) {
            $partido->setEquipoVisitante($equipoVisitante);
        }

        return $partido;
    }
}
