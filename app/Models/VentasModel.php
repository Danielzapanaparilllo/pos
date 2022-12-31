<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasModel extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio', 'total', 'id_usuario', 'id_caja', 'id_cliente', 'forma_pago', 'activo', 'uuid', 'timbrado', 'fecha_timbrado'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function insertaVenta($id_venta, $total, $id_usuario, $id_caja, $id_cliente, $forma_pago)
    {
        $this->insert([
            'folio' => $id_venta,
            'total' => $total,
            'id_usuario' => $id_usuario,
            'id_caja' => $id_caja,
            'id_cliente' => $id_cliente,
            'forma_pago' => $forma_pago
        ]);

        return $this->insertID();
    }

    public function totalDia($fecha, $idCaja = 0)
    {
        if ($idCaja != 0) {
            $where = "activo = 1 AND DATE(fecha_alta) = '$fecha' AND id_caja = '$idCaja'";
        } else {
            $where = "activo = 1 AND DATE(fecha_alta) = '$fecha'";
        }
        $this->select("IFNULL(sum(total), 0) AS total");
        return $this->where($where)->first();
    }
}
