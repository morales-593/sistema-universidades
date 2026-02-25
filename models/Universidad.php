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
        $fields = [];
        $params = [':id' => $id];
        
        $allowed = ['nombre', 'id_region', 'descripcion', 'link_plataforma', 'direccion', 'telefono', 'email', 'logo'];
        
        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $query = "UPDATE " . $this->table . " SET " . implode(', ', $fields) . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        return $stmt->execute();
    }

    public function getAllWithRegion() {
        $query = "SELECT u.*, r.nombre as region_nombre 
                  FROM " . $this->table . " u
                  JOIN regiones r ON u.id_region = r.id
                  ORDER BY u.nombre ASC";
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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    public function getByRegion($id_region) {
        $query = "SELECT u.*, 
                  (SELECT COUNT(*) FROM carreras WHERE id_universidad = u.id) as carreras_count 
                  FROM " . $this->table . " u 
                  WHERE u.id_region = :id_region 
                  ORDER BY u.nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_region', $id_region);
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

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>