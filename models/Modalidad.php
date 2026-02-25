<?php
require_once 'Database.php';

class Modalidad extends Model {
    protected $table = 'modalidades';

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " (nombre, descripcion) VALUES (:nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getCarrerasCount($id) {
        $query = "SELECT COUNT(*) as total FROM carrera_modalidad WHERE id_modalidad = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getCarrerasWithModalidad($id_modalidad) {
        $query = "SELECT c.*, u.nombre as universidad_nombre, u.id_region, r.nombre as region_nombre 
                  FROM carreras c
                  JOIN carrera_modalidad cm ON c.id = cm.id_carrera
                  JOIN universidades u ON c.id_universidad = u.id
                  JOIN regiones r ON u.id_region = r.id
                  WHERE cm.id_modalidad = :id_modalidad
                  ORDER BY c.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_modalidad', $id_modalidad);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre ASC";
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