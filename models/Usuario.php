<?php
require_once 'Database.php';

class Usuario extends Model {
    protected $table = 'usuarios';

    public function __construct() {
        parent::__construct();
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (nombre, email, password, id_rol, activo) 
                  VALUES (:nombre, :email, :password, :id_rol, :activo)";
        
        $stmt = $this->conn->prepare($query);
        
        $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);
        
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id_rol', $data['id_rol']);
        $stmt->bindParam(':activo', $data['activo']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET nombre = :nombre, 
                      email = :email, 
                      id_rol = :id_rol, 
                      activo = :activo 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':id_rol', $data['id_rol']);
        $stmt->bindParam(':activo', $data['activo']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function updatePassword($id, $password) {
        $query = "UPDATE " . $this->table . " 
                  SET password = :password 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bindParam(':password', $password_hash);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function login($email, $password) {
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM " . $this->table . " u
                  JOIN roles r ON u.id_rol = r.id
                  WHERE u.email = :email AND u.activo = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Actualizar último acceso
            $update = "UPDATE " . $this->table . " SET ultimo_acceso = NOW() WHERE id = :id";
            $stmt2 = $this->conn->prepare($update);
            $stmt2->bindParam(':id', $user['id']);
            $stmt2->execute();
            
            return $user;
        }
        
        return false;
    }

    public function getWithRol() {
        $query = "SELECT u.*, r.nombre as rol_nombre 
                  FROM " . $this->table . " u
                  JOIN roles r ON u.id_rol = r.id
                  ORDER BY u.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoles() {
        $query = "SELECT * FROM roles ORDER BY id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>