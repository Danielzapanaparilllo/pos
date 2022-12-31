<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosAlmacenModel extends Model
{
    protected $table      = 'movimientos_almacen';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = ['folio', 'tipo_movimiento', 'total', 'id_usuario', 'activo'];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_alta';
    protected $updatedField  = '';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function insertaMovimiento($folio, $tipo, $total, $id_usuario)
    {
        $this->insert([
            'folio' => $folio,
            'tipo_movimiento' => $tipo,
            'total' => $total,
            'id_usuario' => $id_usuario,
            'activo' => 1
        ]);

        return $this->insertID();
    }

    public function detalleMovimientos()
    {
        $detMov = new DetalleMovimientoModel();

        $detMov->select('movimientos_almacen.fecha_alta, movimientos_almacen.folio, movimientos_almacen.tipo_movimiento, detalle_mov_almacen.nombre, detalle_mov_almacen.cantidad_anterior, detalle_mov_almacen.cantidad, detalle_mov_almacen.cantidad_saldo, usuarios.usuario');
        $detMov->join('movimientos_almacen', 'detalle_mov_almacen.id_movimiento=movimientos_almacen.id');
        $detMov->join('usuarios', 'movimientos_almacen.id_usuario=usuarios.id');
        $detMov->where('movimientos_almacen.activo', 1);
        $detMov->orderBy('movimientos_almacen.fecha_alta', 'DESC');
        $datos = $detMov->findAll();
        return $datos;
    }

    public function detalleMovimientosProducto($id)
    {
        $detMov = new DetalleMovimientoModel();

        $detMov->select('movimientos_almacen.fecha_alta, movimientos_almacen.folio, movimientos_almacen.tipo_movimiento, detalle_mov_almacen.cantidad_anterior, detalle_mov_almacen.cantidad, detalle_mov_almacen.cantidad_saldo');
        $detMov->join('movimientos_almacen', 'detalle_mov_almacen.id_movimiento=movimientos_almacen.id');
        $detMov->where('movimientos_almacen.activo', 1);
        $detMov->where('detalle_mov_almacen.id_producto', $id);
        $detMov->orderBy('movimientos_almacen.fecha_alta', 'ASC');
        $datos = $detMov->findAll();
        return $datos;
    }
}
