<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductosModel extends Model
{
    protected $table      = 'productos';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['codigo', 'nombre', 'tipo_venta', 'precio_venta', 'precio_compra', 'existencias', 'stock_minimo', 'inventariable', 'id_unidad', 'id_categoria', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_modifica';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function actualizaStock($id_producto, $cantidad, $operador = '+')
    {
        $this->set('existencias', "existencias $operador $cantidad", FALSE);
        $this->where('id', $id_producto);
        $this->update();
    }

    public function totalProductos()
    {
        $where = "activo=1 AND id > 1";
        return $this->where($where)->countAllResults();
    }

    public function productosMinimo()
    {
        $where = "stock_minimo >= existencias AND inventariable=1 AND activo=1 AND id > 1";
        return $this->where($where)->countAllResults();
    }

    public function getProductosMinimo()
    {
        $where = "stock_minimo >= existencias AND inventariable=1 AND activo=1 AND id > 1";
        return $this->where($where)->findAll();
    }
}
