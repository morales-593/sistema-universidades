<?php
require_once 'Database.php';

class InformacionUniversidad extends Model {
    protected $table = 'informacion_universidades';

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (id_universidad, tipo_proceso, tipo_prueba, temas, incidencia, registro, 
                   inscripciones, examen, postulacion, asignacion_cupos, matricula, creado_por) 
                  VALUES 
                  (:id_universidad, :tipo_proceso, :tipo_prueba, :temas, :incidencia, :registro, 
                   :inscripciones, :examen, :postulacion, :asignacion_cupos, :matricula, :creado_por)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_universidad', $data['id_universidad']);
        $stmt->bindParam(':tipo_proceso', $data['tipo_proceso']);
        $stmt->bindParam(':tipo_prueba', $data['tipo_prueba']);
        $stmt->bindParam(':temas', $data['temas']);
        $stmt->bindParam(':incidencia', $data['incidencia']);
        $stmt->bindParam(':registro', $data['registro']);
        $stmt->bindParam(':inscripciones', $data['inscripciones']);
        $stmt->bindParam(':examen', $data['examen']);
        $stmt->bindParam(':postulacion', $data['postulacion']);
        $stmt->bindParam(':asignacion_cupos', $data['asignacion_cupos']);
        $stmt->bindParam(':matricula', $data['matricula']);
        $stmt->bindParam(':creado_por', $data['creado_por']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET tipo_proceso = :tipo_proceso,
                      tipo_prueba = :tipo_prueba,
                      temas = :temas,
                      incidencia = :incidencia,
                      registro = :registro,
                      inscripciones = :inscripciones,
                      examen = :examen,
                      postulacion = :postulacion,
                      asignacion_cupos = :asignacion_cupos,
                      matricula = :matricula,
                      actualizado_por = :actualizado_por
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tipo_proceso', $data['tipo_proceso']);
        $stmt->bindParam(':tipo_prueba', $data['tipo_prueba']);
        $stmt->bindParam(':temas', $data['temas']);
        $stmt->bindParam(':incidencia', $data['incidencia']);
        $stmt->bindParam(':registro', $data['registro']);
        $stmt->bindParam(':inscripciones', $data['inscripciones']);
        $stmt->bindParam(':examen', $data['examen']);
        $stmt->bindParam(':postulacion', $data['postulacion']);
        $stmt->bindParam(':asignacion_cupos', $data['asignacion_cupos']);
        $stmt->bindParam(':matricula', $data['matricula']);
        $stmt->bindParam(':actualizado_por', $data['actualizado_por']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getByUniversidad($id_universidad) {
        $query = "SELECT i.*, 
                  u.nombre as universidad_nombre,
                  u.id_region,
                  r.nombre as region_nombre,
                  c1.nombre as creador_nombre,
                  c2.nombre as actualizador_nombre
                  FROM " . $this->table . " i
                  JOIN universidades u ON i.id_universidad = u.id
                  JOIN regiones r ON u.id_region = r.id
                  LEFT JOIN usuarios c1 ON i.creado_por = c1.id
                  LEFT JOIN usuarios c2 ON i.actualizado_por = c2.id
                  WHERE i.id_universidad = :id_universidad";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_universidad', $id_universidad);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllWithDetails() {
        $query = "SELECT i.*, 
                  u.nombre as universidad_nombre,
                  u.id_region,
                  r.nombre as region_nombre,
                  c1.nombre as creador_nombre,
                  c2.nombre as actualizador_nombre
                  FROM " . $this->table . " i
                  JOIN universidades u ON i.id_universidad = u.id
                  JOIN regiones r ON u.id_region = r.id
                  LEFT JOIN usuarios c1 ON i.creado_por = c1.id
                  LEFT JOIN usuarios c2 ON i.actualizado_por = c2.id
                  ORDER BY u.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUniversidadesSinInfo() {
        $query = "SELECT u.*, r.nombre as region_nombre 
                  FROM universidades u
                  JOIN regiones r ON u.id_region = r.id
                  WHERE u.id NOT IN (SELECT id_universidad FROM " . $this->table . ")
                  ORDER BY u.nombre ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>