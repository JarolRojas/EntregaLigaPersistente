<?php
class Equipo {
    private int $id;
    private string $nombre;
    private string $estadio;
    private string $fechaCreacion;
    
    public function __construct(
        string $nombre,
        string $estadio,
        int $id = 0,
        string $fechaCreacion = ''
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->estadio = $estadio;
        $this->fechaCreacion = $fechaCreacion;
    }
    
    public function getId(): int {
        return $this->id;
    }
    
    public function getNombre(): string {
        return $this->nombre;
    }
    
    public function getEstadio(): string {
        return $this->estadio;
    }
    
    public function getFechaCreacion(): string {
        return $this->fechaCreacion;
    }
    
    public function setId(int $id): void {
        $this->id = $id;
    }
    
    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }
    
    public function setEstadio(string $estadio): void {
        $this->estadio = $estadio;
    }
    
    public function setFechaCreacion(string $fechaCreacion): void {
        $this->fechaCreacion = $fechaCreacion;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'estadio' => $this->estadio,
            'fechaCreacion' => $this->fechaCreacion
        ];
    }
}
?>
