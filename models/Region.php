<?php
require_once 'Database.php';

class Region extends Model
{
    protected $table = 'regiones';

    public function create($data)
    {
        $query = "INSERT INTO " . $this->table . " (nombre) VALUES (:nombre)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        return $stmt->execute();
    }

    public function update($id, $data)
    {
        $query = "UPDATE " . $this->table . " SET nombre = :nombre WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getUniversidadesCount($id)
    {
        $query = "SELECT COUNT(*) as total FROM universidades WHERE id_region = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getById($id)
    {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $query = "SELECT * FROM " . $this->table . " ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>