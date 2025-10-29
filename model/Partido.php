<?php
class Partido
{
    private int $id;
    private int $equipoLocalId;
    private int $equipoVisitanteId;
    private string $resultado;
    private int $jornada;
    private string $fechaCreacion;
    private ?Equipo $equipoLocal = null;
    private ?Equipo $equipoVisitante = null;
    public function __construct(
        int $equipoLocalId,
        int $equipoVisitanteId,
        string $resultado,
        int $jornada,
        int $id = 0,
        string $fechaCreacion = ''
    ) {
        $this->id = $id;
        $this->equipoLocalId = $equipoLocalId;
        $this->equipoVisitanteId = $equipoVisitanteId;
        $this->resultado = $resultado;
        $this->jornada = $jornada;
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEquipoLocalId(): int
    {
        return $this->equipoLocalId;
    }

    public function getEquipoVisitanteId(): int
    {
        return $this->equipoVisitanteId;
    }

    public function getResultado(): string
    {
        return $this->resultado;
    }

    public function getJornada(): int
    {
        return $this->jornada;
    }

    public function getFechaCreacion(): string
    {
        return $this->fechaCreacion;
    }

    public function getEquipoLocal(): ?Equipo
    {
        return $this->equipoLocal;
    }

    public function getEquipoVisitante(): ?Equipo
    {
        return $this->equipoVisitante;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setEquipoLocalId(int $equipoLocalId): void
    {
        $this->equipoLocalId = $equipoLocalId;
    }

    public function setEquipoVisitanteId(int $equipoVisitanteId): void
    {
        $this->equipoVisitanteId = $equipoVisitanteId;
    }

    public function setResultado(string $resultado): void
    {
        $this->resultado = $resultado;
    }

    public function setJornada(int $jornada): void
    {
        $this->jornada = $jornada;
    }

    public function setFechaCreacion(string $fechaCreacion): void
    {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function setEquipoLocal(?Equipo $equipo): void
    {
        $this->equipoLocal = $equipo;
    }

    public function setEquipoVisitante(?Equipo $equipo): void
    {
        $this->equipoVisitante = $equipo;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'equipoLocalId' => $this->equipoLocalId,
            'equipoVisitanteId' => $this->equipoVisitanteId,
            'resultado' => $this->resultado,
            'jornada' => $this->jornada,
            'fechaCreacion' => $this->fechaCreacion
        ];
    }
}
