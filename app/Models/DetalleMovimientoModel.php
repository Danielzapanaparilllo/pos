<?php

namespace App\Models;

use CodeIgniter\Model;

class DetalleMovimientoModel extends Model
{
    protected $table      = 'detalle_mov_almacen';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['id_movimiento', 'id_producto', 'nombre', 'cantidad_anterior', 'cantidad', 'cantidad_saldo', 'precio'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = 'fecha_modifica';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function insertaDetalleMov($id_movimiento, $id_producto, $nombre, $cantidad_anterior, $cantidad, $cantidad_saldo, $precio)
    {
        $this->insert([
            'id_movimiento' => $id_movimiento,
            'id_producto' => $id_producto,
            'nombre' => $nombre,
            'cantidad_anterior' => $cantidad_anterior,
            'cantidad' => $cantidad,
            'cantidad_saldo' => $cantidad_saldo,
            'precio' => $precio
        ]);
    }
}
