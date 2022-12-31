<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['usuario', 'password', 'nombre', 'id_caja', 'id_rol', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_modifica';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getUsuarios($activo = 1)
    {
        $this->select('usuarios.id, usuarios.usuario, usuarios.nombre, roles.nombre AS rol, cajas.nombre AS caja');
        $this->join('roles', 'usuarios.id_rol = roles.id');
        $this->join('cajas', 'usuarios.id_caja = cajas.id');
        $this->where('usuarios.activo', $activo);
        $datos = $this->findAll();
        return $datos;
    }

    public function getUsuario($id)
    {
        $this->select('usuarios.id, usuarios.usuario, usuarios.nombre, roles.nombre AS rol, cajas.nombre AS caja');
        $this->join('roles', 'usuarios.id_rol = roles.id');
        $this->join('cajas', 'usuarios.id_caja = cajas.id');
        $this->where('usuarios.id', $id);
        $datos = $this->first();
        return $datos;
    }
}
