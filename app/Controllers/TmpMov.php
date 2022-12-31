<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ProductosModel, TmpMovModel};

class TmpMov extends BaseController
{
    protected $tmp_mov, $productos;

    public function __construct()
    {
        $this->tmp_mov = new TmpMovModel();
        $this->productos = new ProductosModel();
    }

    public function inserta()
    {
        $error = '';

        if ($this->request->isAJAX()) {

            $codigo = $this->request->getPost('codigo');
            $cantidad = $this->request->getPost('cantidad');
            $id_mov = $this->request->getPost('id_mov');
            $comun = $this->request->getPost('comun');
            $descripcion = $this->request->getPost('descripcion');
            $precio = $this->request->getPost('precio');

            $producto = $this->productos->where('codigo', $codigo)->first();

            if ($producto) {

                if ($comun) {
                    $subtotal = $cantidad * $precio;

                    $this->tmp_mov->save([
                        'folio' => $id_mov,
                        'id_producto' => $producto['id'],
                        'codigo' => $producto['codigo'],
                        'nombre' => $descripcion,
                        'precio' => $precio,
                        'cantidad' => $cantidad,
                        'subtotal' => $subtotal,
                    ]);
                } else {

                    $datosExiste = $this->tmp_mov->porIdProductoMov($producto['id'], $id_mov);
                    if ($datosExiste) {
                        $cantidad = $datosExiste->cantidad + $cantidad;
                        if ($producto['inventariable'] == 1) {
                            if ($producto['existencias'] >= $cantidad) {

                                $subtotal = $cantidad * $datosExiste->precio;

                                $this->tmp_mov->actualizarProductoMov($producto['id'], $id_mov, $cantidad, $subtotal);
                            } else {
                                $error = 'NO hay suficientes existencia';
                            }
                        } else {
                            $subtotal = $cantidad * $datosExiste->precio;
                            $this->tmp_mov->actualizarProductoMov($producto['id'], $id_mov, $cantidad, $subtotal);
                        }
                    } else {

                        if ($producto['inventariable'] == 1) {
                            if ($producto['existencias'] >= $cantidad) {

                                $subtotal = $cantidad * $producto['precio_venta'];

                                $this->tmp_mov->save([
                                    'folio' => $id_mov,
                                    'id_producto' => $producto['id'],
                                    'codigo' => $producto['codigo'],
                                    'nombre' => $producto['nombre'],
                                    'precio' => $producto['precio_venta'],
                                    'cantidad' => $cantidad,
                                    'subtotal' => $subtotal,
                                ]);
                            } else {
                                $error = 'No hay existencias';
                            }
                        } else {
                            $subtotal = $cantidad * $producto['precio_venta'];

                            $this->tmp_mov->save([
                                'folio' => $id_mov,
                                'id_producto' => $producto['id'],
                                'codigo' => $producto['codigo'],
                                'nombre' => $producto['nombre'],
                                'precio' => $producto['precio_venta'],
                                'cantidad' => $cantidad,
                                'subtotal' => $subtotal,
                            ]);
                        }
                    }
                }
            } else {
                $error = 'No existe el producto';
            }

            $res['datos'] = $this->cargaProductos($id_mov);
            $res['total'] = number_format($this->totalProductos($id_mov), 2, '.', ',');
            $res['error'] = $error;
            echo json_encode($res);
        }
    }

    public function cargaProductos($id_mov)
    {
        $resultado = $this->tmp_mov->porMovimiento($id_mov);
        $fila = '';
        $numFila = 0;

        foreach ($resultado as $row) {
            $numFila++;
            $fila .= "<tr id='fila" . $numFila . "'>";
            $fila .= "<td>" . $numFila . "</td>";
            $fila .= "<td>" . $row['codigo'] . "</td>";
            $fila .= "<td>" . $row['nombre'] . "</td>";
            $fila .= "<td>" . $row['precio'] . "</td>";
            $fila .= "<td>" . $row['cantidad'] . "</td>";
            $fila .= "<td>" . $row['subtotal'] . "</td>";
            $fila .= "<td><a onclick=\"eliminaProducto(" . $row['id_producto'] . ")\" class='borrar'><span class='fas fa-fw fa-trash'></span></a></td>";
            $fila .= "</tr>";
        }
        return $fila;
    }

    public function totalProductos($id_mov)
    {
        $resultado = $this->tmp_mov->porMovimiento($id_mov);
        $total = 0;

        foreach ($resultado as $row) {
            $total += $row['subtotal'];
        }
        return $total;
    }

    public function eliminar()
    {

        if ($this->request->isAJAX()) {

            $id_producto = $this->request->getPost('id_producto');
            $id_mov = $this->request->getPost('id_mov');

            $datosExiste = $this->tmp_mov->porIdProductoMov($id_producto, $id_mov);

            if ($datosExiste) {
                if ($datosExiste->cantidad > 1) {
                    $cantidad = $datosExiste->cantidad - 1;
                    $subtotal = $cantidad * $datosExiste->precio;
                    $this->tmp_mov->actualizarProductoMov($id_producto, $id_mov, $cantidad, $subtotal);
                } else {
                    $this->tmp_mov->eliminarProductoMov($id_producto, $id_mov);
                }
            }

            $res['datos'] = $this->cargaProductos($id_mov);
            $res['total'] = number_format($this->totalProductos($id_mov), 2, '.', ',');
            $res['error'] = '';
            echo json_encode($res);
        }
    }
}
