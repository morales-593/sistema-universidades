<?php
require_once 'Database.php';

class Universidad extends Model {
    protected $table = 'universidades';

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, id_region, descripcion, link_plataforma, direccion, telefono, email, logo) 
                  VALUES (:nombre, :id_region, :descripcion, :link_plataforma, :direccion, :telefono, :email, :logo)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_region', $data['id_region']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':link_plataforma', $data['link_plataforma']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':logo', $data['logo']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      id_region = :id_region, 
                      descripcion = :descripcion, 
                      link_plataforma = :link_plataforma, 
                      direccion = :direccion, 
                      telefono = :telefono, 
                      email = :email,
                      logo = :logo
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_region', $data['id_region']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':link_plataforma', $data['link_plataforma']);
        $stmt->bindParam(':direccion', $data['direccion']);
        $stmt->bindParam(':telefono', $data['telefono']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':logo', $data['logo']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getAllWithRegion() {
        $query = "SELECT u.*, r.nombre as region_nombre 
                  FROM " . $this->table . " u
                  JOIN regiones r ON u.id_region = r.id
                  ORDER BY u.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithDetails($id) {
        $query = "SELECT u.*, r.nombre as region_nombre 
                  FROM " . $this->table . " u
                  JOIN regiones r ON u.id_region = r.id
                  WHERE u.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCarrerasCount($id) {
        $query = "SELECT COUNT(*) as total FROM carreras WHERE id_universidad = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getByRegion($id_region) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_region = :id_region ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_region', $id_region);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>