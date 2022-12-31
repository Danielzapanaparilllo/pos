<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ConfiguracionModel, DetalleMovimientoModel, DetalleRolesPermisosModel, MovimientosAlmacenModel, ProductosModel};
use App\ThirdParty\Fpdf\PlantillaKardex;

class Inventario extends BaseController
{
    protected $detalleRoles, $movimientos, $productos, $configuracion, $detalle_movimiento;
    protected $reglas, $configMoneda, $db;

    public function __construct()
    {
        $this->movimientos = new MovimientosAlmacenModel();
        $this->configuracion = new ConfiguracionModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->productos = new ProductosModel();
        $this->session = Session();
        helper(['form']);
    }

    public function index()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '17');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $movimientos = $this->movimientos->detalleMovimientos();
        $data = ['movimientos' => $movimientos];

        echo view('header');
        echo view('inventario/index', $data);
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '17');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post') {
            $msg = "";
            $id_producto = $this->request->getPost('id_producto');
            $cantidad = $this->request->getPost('cantidad');
            $producto = $this->productos->where('id', $id_producto)->first();

            if ($producto) {

                if ($producto['tipo_venta'] == 'P') {
                    $reglas = [
                        'codigo' => ['label' => 'código', 'rules' => 'required'],
                        'cantidad' => ['label' => 'cantidad', 'rules' => 'is_natural_no_zero']
                    ];
                } else if ($producto['tipo_venta'] == 'G') {
                    $reglas = [
                        'codigo' => ['label' => 'código', 'rules' => 'required'],
                        'cantidad' => ['label' => 'cantidad', 'rules' => 'regex_match[^([0-9]*[\.]?[0-9]+)$]']
                    ];
                }

                if ($this->validate($reglas)) {

                    if ($producto['inventariable']) {

                        $this->movimientos = new MovimientosAlmacenModel();

                        $total = number_format($producto['precio_venta'] * $cantidad, 2, '.', '');
                        $cantidad_saldo = $producto['existencias'] + $cantidad;

                        $this->config = new ConfiguracionModel();
                        $folio = $this->config->folioActual('E');

                        $idMov = $this->movimientos->insertaMovimiento($folio, 'E', $total, $this->session->id_usuario);

                        if ($idMov) {
                            $this->config->siguienteFolio('E');
                            $this->detalle_movimientos = new DetalleMovimientoModel();
                            $this->detalle_movimientos->insertaDetalleMov($idMov, $id_producto, $producto['nombre'], $producto['existencias'], $cantidad, $cantidad_saldo, $producto['precio_venta']);
                            $this->productos->actualizaStock($id_producto, $cantidad, '+');
                            $success = 1;
                            $msg = "Cantidad agregada al producto.";
                        } else {
                            $success = 0;
                            $msg = "Error al agregar cantidad al producto.";
                        }

                        $data = ['mensaje' => $msg, 'success' => $success];
                    } else {
                        $data = ['mensaje' => 'El producto no usa inventario', 'success' => 0];
                    }
                } else {
                    $data = ['validation' => $this->validator];
                }
            } else {

                $data = ['mensaje' => 'El producto no existe', 'success' => 0];
            }
        } else {
            $data = ['validation' => $this->validator];
        }

        echo view('header');
        echo view('inventario/nuevo', $data);
        echo view('footer');
    }

    public function ajuste()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '17');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $reglas = [
            'codigo' => ['label' => 'código', 'rules' => 'required'],
            'cantidad' => ['label' => 'existencias', 'rules' => 'numeric']
        ];

        if ($this->request->getMethod() == 'post' && $this->validate($reglas)) {
            $msg = "";
            $id_producto = $this->request->getPost('id_producto');
            $cantidad = $this->request->getPost('cantidad');
            $producto = $this->productos->where('id', $id_producto)->first();

            if ($producto) {
                if ($producto['inventariable']) {

                    $total = str_replace('-', '', number_format($producto['precio_venta'] * $cantidad, 2, '.', ''));
                    $cantidad_saldo = $producto['existencias'] + $cantidad;

                    if ($cantidad_saldo >= 0) {

                        $this->config = new ConfiguracionModel();
                        $folio = $this->config->folioActual('A');

                        $this->movimientos = new MovimientosAlmacenModel();
                        $idMov = $this->movimientos->insertaMovimiento($folio, 'A', $total, $this->session->id_usuario);

                        if ($idMov) {
                            $this->config->siguienteFolio('A');
                            $this->detalle_movimientos = new DetalleMovimientoModel();
                            $this->detalle_movimientos->insertaDetalleMov($idMov, $id_producto, $producto['nombre'], $producto['existencias'], $cantidad, $cantidad_saldo, $producto['precio_venta']);
                            $this->productos->actualizaStock($id_producto, $cantidad, '+');
                            $success = 1;
                            $msg = "Ajuste realizado al producto.";
                        } else {
                            $success = 0;
                            $msg = "Error al realizar ajuste del producto.";
                        }
                        $data = ['mensaje' => $msg, 'success' => $success];
                    } else {
                        $data = ['mensaje' => 'No hay suficientes existencias para realizar el ajuste', 'success' => 0];
                    }
                } else {
                    $data = ['mensaje' => 'El producto no usa inventario', 'success' => 0];
                }
            } else {
                $data = ['mensaje' => 'El producto no existe', 'success' => 0];
            }
        } else {
            $data = ['validation' => $this->validator];
        }

        echo view('header');
        echo view('inventario/ajuste', $data);
        echo view('footer');
    }

    function mostrarMovimientos()
    {
        $db = \Config\Database::connect();

        $draw = intval($this->request->getPost("draw"));
        $start = intval($this->request->getPost("start"));
        $length = intval($this->request->getPost("length"));
        $order = $this->request->getPost("order");
        $search = $this->request->getPost("search");
        $search = $search['value'];
        $col = 0;
        $dir = "";

        $aColumns = array('movimientos_almacen.fecha_alta', 'detalle_mov_almacen.nombre', 'movimientos_almacen.folio', 'movimientos_almacen.tipo_movimiento', 'detalle_mov_almacen.cantidad', 'detalle_mov_almacen.id');
        $sTable = "detalle_mov_almacen";
        $sWhere = "movimientos_almacen.activo = 1";
        $sWhereoRG = "movimientos_almacen.activo = 1";

        $builder = $db->table($sTable);

        if (!empty($order)) {
            foreach ($order as $o) {
                $col = $o['column'];
                $dir = $o['dir'];
            }
        }

        if ($dir != "asc" && $dir != "desc")
            $dir = "desc";

        if (!isset($aColumns[$col]))
            $order = null;
        else
            $order = $aColumns[$col];

        if ($order != null)
            $builder->orderBy($order, $dir);

        if (!empty($search)) {
            $x = 0;
            foreach ($aColumns as $sterm) {
                if ($x == 0) {
                    $sWhere .= " AND (" . $sterm . " LIKE '%" . $search . "%' ";
                } else {
                    $sWhere .= " OR " . $sterm . " LIKE '%" . $search . "%' ";
                }
                $x++;
            }
            $sWhere .= ")";
        }


        $builder->where($sWhere);
        $builder->limit($length, $start);
        $builder->join('movimientos_almacen', 'detalle_mov_almacen.id_movimiento = movimientos_almacen.id');
        $query = $builder->get();

        $data = array();

        foreach ($query->getResult('array') as $rows) {

            $mov = $rows['tipo_movimiento'];
            $folio = $rows['folio'];
            if ($mov == 'V') {
                $tipoMovimiento = 'Venta # ' . $folio;
            } else if ($mov == 'A') {
                $tipoMovimiento = 'Ajuste de inventario # ' . $folio;
            } else if ($mov == 'E') {
                $tipoMovimiento = 'Entrada de inventario # ' . $folio;
            } else if ($mov == 'EI') {
                $tipoMovimiento = 'Entrada inicial';
            } else if ($mov == 'VC') {
                $tipoMovimiento = 'Devolución de venta # ' . $folio;
            } else {
                $tipoMovimiento = 'Movimiento # ' . $folio;
            }

            $data[] = array(
                $rows['fecha_alta'], $rows['nombre'], $tipoMovimiento, $rows['cantidad']
            );
        }

        $total_registros = $this->totalRegistro($sTable, $sWhereoRG);
        $total_registros_filtrado = $this->totalRegistroFiltrados($sTable, $sWhere);
        $output = array(
            "draw" => $draw,
            "recordsTotal" => $total_registros,
            "recordsFiltered" => $total_registros_filtrado,
            "data" => $data
        );
        echo json_encode($output);
        exit();
    }

    public function totalRegistro($sTable, $sWhereoRG)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as num FROM $sTable INNER JOIN movimientos_almacen ON detalle_mov_almacen.id_movimiento = movimientos_almacen.id WHERE $sWhereoRG")->getRow();
        if (isset($query)) return $query->num;
        return 0;
    }

    public function totalRegistroFiltrados($sTable, $where)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as num FROM $sTable INNER JOIN movimientos_almacen ON detalle_mov_almacen.id_movimiento = movimientos_almacen.id WHERE $where")->getRow();

        if (isset($query)) return $query->num;
        return 0;
    }

    function detalle_kardex()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        echo view('header');
        echo view('inventario/detalle_kardex');
        echo view('footer');
    }

    function genera_kardex()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->isAJAX()) {

            $codigo = $this->request->getPost("codigo");

            $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;

            $this->productos->select('*');
            $this->productos->where('codigo', $codigo);
            $this->productos->where('activo', 1);
            $datos_producto = $this->productos->get()->getRow();

            if ($datos_producto) {

                if ($datos_producto->inventariable) {

                    if ($datos_producto->tipo_venta == 'P') {
                        $existencias = number_format($datos_producto->existencias, 0);
                    } else {
                        $existencias = number_format($datos_producto->existencias, 2, '.', '');
                    }

                    ini_set('display_errors', 1);

                    $movimientos = $this->movimientos->detalleMovimientosProducto($datos_producto->id);

                    $logo = base_url() . '/images/logotipo.png';

                    $datos = array('titulo' => $nombreTienda, 'logo' => $logo, 'producto' => $datos_producto);

                    $pdf = new PlantillaKardex('P', 'mm', 'letter', $datos);
                    $pdf->SetTitle('Kardex');
                    $pdf->AliasNbPages();
                    $pdf->AddPage();
                    $pdf->SetMargins(10, 10, 10);
                    $pdf->SetFont('Arial', 'B', 10);

                    $pdf->SetFont('Arial', 'B', 8);
                    $pdf->SetWidths(array(35, 55, 35, 35, 35));
                    $pdf->Row(array('Fecha', 'Movimiento', 'Entrada', 'Salida', 'Existencia'));
                    $pdf->SetFont('Arial', '', 8);

                    foreach ($movimientos as $row) {
                        $mov = $row['tipo_movimiento'];
                        $folio = $row['folio'];
                        $cantidad = $row['cantidad'];
                        if ($mov == 'V') {
                            $tipoMovimiento = 'Venta #' . $folio;
                            $entrada = 0;
                            $salida = $cantidad;
                        } else if ($mov == 'A') {
                            $tipoMovimiento = 'Ajuste de inventario #' . $folio;
                            if ($cantidad < 0) {
                                $entrada = 0;
                                $salida = $cantidad = str_replace('-', '', $cantidad);
                            } elseif ($cantidad > 0) {
                                $entrada = $cantidad;
                                $salida = 0;
                            } else {
                                $entrada = 0;
                                $salida = 0;
                            }
                        } else if ($mov == 'E') {
                            $tipoMovimiento = 'Entrada de inventario  #' . $folio;
                            $entrada = $cantidad;
                            $salida = 0;
                        } else if ($mov == 'EI') {
                            $tipoMovimiento = 'Entrada inicial';
                            $entrada = $cantidad;
                            $salida = 0;
                        } else if ($mov == 'VC') {
                            $tipoMovimiento = 'Devolución de venta #' . $folio;
                            $entrada = $cantidad;
                            $salida = 0;
                        } else {
                            $tipoMovimiento = 'Movimiento';
                        }

                        $pdf->Row(array($this->ordenarFechaHora($row['fecha_alta']), utf8_decode($tipoMovimiento), $entrada, $salida, $row['cantidad_saldo']));
                    }

                    $dir = "assets/kardex.pdf";
                    $pdf->Output($dir, "F");

                    $res['existe'] = true;
                    $res['ruta'] = base_url() . '/' . $dir;
                } else {
                    $res['error'] = 'El producto no usa inventario';
                    $res['existe'] = false;
                }
            } else {
                $res['error'] = 'No existe el producto';
                $res['existe'] = false;
            }
            echo json_encode($res);
        }
    }

    function ordenarFechaHora($fechaHora)
    {
        $fecha = substr($fechaHora, 0, 10);
        $hora = substr($fechaHora, 11);
        $arreglo = explode("-", $fecha);
        $arregloHora = explode(":", $hora);
        return $arreglo[2] . '/' . $arreglo[1] . '/' . $arreglo[0] . ' ' . $arregloHora[0] . ':' . $arregloHora[1];
    }
}
