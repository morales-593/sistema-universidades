<?php
require_once 'Database.php';

class Carrera extends Model {
    protected $table = 'carreras';

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, id_universidad, titulo_otorgado, duracion, descripcion, perfil_egreso, campo_laboral) 
                  VALUES (:nombre, :id_universidad, :titulo_otorgado, :duracion, :descripcion, :perfil_egreso, :campo_laboral)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_universidad', $data['id_universidad']);
        $stmt->bindParam(':titulo_otorgado', $data['titulo_otorgado']);
        $stmt->bindParam(':duracion', $data['duracion']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':perfil_egreso', $data['perfil_egreso']);
        $stmt->bindParam(':campo_laboral', $data['campo_laboral']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      id_universidad = :id_universidad, 
                      titulo_otorgado = :titulo_otorgado, 
                      duracion = :duracion, 
                      descripcion = :descripcion, 
                      perfil_egreso = :perfil_egreso, 
                      campo_laboral = :campo_laboral
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':id_universidad', $data['id_universidad']);
        $stmt->bindParam(':titulo_otorgado', $data['titulo_otorgado']);
        $stmt->bindParam(':duracion', $data['duracion']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':perfil_egreso', $data['perfil_egreso']);
        $stmt->bindParam(':campo_laboral', $data['campo_laboral']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function getAllWithDetails() {
        $query = "SELECT c.*, u.nombre as universidad_nombre, u.id_region, r.nombre as region_nombre 
                  FROM " . $this->table . " c
                  JOIN universidades u ON c.id_universidad = u.id
                  JOIN regiones r ON u.id_region = r.id
                  ORDER BY c.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdWithDetails($id) {
        $query = "SELECT c.*, u.nombre as universidad_nombre, u.id_region, r.nombre as region_nombre 
                  FROM " . $this->table . " c
                  JOIN universidades u ON c.id_universidad = u.id
                  JOIN regiones r ON u.id_region = r.id
                  WHERE c.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getModalidades($id_carrera) {
        $query = "SELECT m.* 
                  FROM modalidades m
                  JOIN carrera_modalidad cm ON m.id = cm.id_modalidad
                  WHERE cm.id_carrera = :id_carrera
                  ORDER BY m.nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_carrera', $id_carrera);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function asignarModalidades($id_carrera, $modalidades) {
        // Eliminar asignaciones actuales
        $delete = "DELETE FROM carrera_modalidad WHERE id_carrera = :id_carrera";
        $stmt = $this->conn->prepare($delete);
        $stmt->bindParam(':id_carrera', $id_carrera);
        $stmt->execute();

        // Insertar nuevas asignaciones
        if (!empty($modalidades)) {
            $query = "INSERT INTO carrera_modalidad (id_carrera, id_modalidad) VALUES ";
            $values = [];
            foreach ($modalidades as $id_modalidad) {
                $values[] = "($id_carrera, $id_modalidad)";
            }
            $query .= implode(", ", $values);
            $stmt = $this->conn->prepare($query);
            return $stmt->execute();
        }
        return true;
    }

    public function getByUniversidad($id_universidad) {
        $query = "SELECT * FROM " . $this->table . " WHERE id_universidad = :id_universidad ORDER BY nombre";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_universidad', $id_universidad);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>