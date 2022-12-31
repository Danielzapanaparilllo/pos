<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleRolesPermisosModel extends Model
{
    protected $table      = 'detalle_roles_permisos';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_rol', 'id_permiso'];

    protected $useTimestamps = true;
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function verificaPermisos($idRol, $permiso)
    {
        $tieneAcceso = false;
        $existe = $this->where(['id_rol' => $idRol, 'id_permiso' => $permiso])->first();

        if($existe != null){
            $tieneAcceso = true;
        }
        return $tieneAcceso;
    }
}
