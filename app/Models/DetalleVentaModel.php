<?php

namespace App\Models;
use CodeIgniter\Model;

class DetalleVentaModel extends Model
{
    protected $table      = 'detalle_venta';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_venta', 'id_producto', 'nombre', 'cantidad', 'precio' ];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function productosMasVendidos($fecha_ini, $fecha_fin)
    {
        $this->select('sum(cantidad) as cantidad, nombre');
        $this->where("DATE(fecha_alta) BETWEEN '$fecha_ini' AND '$fecha_fin'");
        $this->groupBy('id_producto');
        $this->orderBy('sum(cantidad)', 'DESC');
        $this->limit(5);
        $datos = $this->get()->getResultArray();
        return $datos;
    }
}
