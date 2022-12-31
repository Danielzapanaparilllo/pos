<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CajasModel, ConfiguracionModel, DetalleMovimientoModel, DetalleRolesPermisosModel, DetalleVentaModel, MovimientosAlmacenModel, ProductosModel, TmpMovModel, VentasModel};
use App\ThirdParty\Fpdf\Fpdf;

class Ventas extends BaseController
{
    protected $detalleRoles, $ventas, $temporal_compra, $detalle_venta, $productos, $configuracion, $cajas, $session;

    public function __construct()
    {
        $this->ventas = new VentasModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->productos = new ProductosModel();
        $this->configuracion = new ConfiguracionModel();
        $this->cajas = new CajasModel();
        $this->session = session();

        helper(['form']);
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '26');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        echo view('header');
        if ($activo == 1) {
            echo view('ventas/ventas');
        } else {
            echo view('ventas/eliminados');
        }
        echo view('footer');
    }

    public function venta()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '25');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        echo view('header');
        echo view('ventas/caja');
        echo view('footer');
    }

    public function guarda()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $id_venta = $this->request->getPost('id_venta');
        $total = preg_replace('/[\$,]/', '', $this->request->getPost('total'));
        $forma_pago = $this->request->getPost('forma_pago');
        $id_cliente = $this->request->getPost('id_cliente');

        if ($id_cliente == null) {
            $id_cliente = 1;
        }

        if ($forma_pago == null) {
            $forma_pago = '001';
        }

        $caja = $this->cajas->where('id', $this->session->id_caja)->first();
        $folio = $caja['folio'];

        //Inserta venta
        $resultadoId = $this->ventas->insertaVenta($folio, $total, $this->session->id_usuario, $this->session->id_caja, $id_cliente, $forma_pago);

        //Inserta movimiento
        $this->movimientos = new MovimientosAlmacenModel();
        $idMov = $this->movimientos->insertaMovimiento($folio, 'V', $total, $this->session->id_usuario);

        $this->tmp_mov = new TmpMovModel();

        if ($resultadoId) {
            $folio++;
            $this->cajas->update($this->session->id_caja, ['folio' => $folio]);

            $resultadoCompra = $this->tmp_mov->porMovimiento($id_venta);

            foreach ($resultadoCompra as $row) {

                $producto = $this->productos->where('id', $row['id_producto'])->first();

                //Inserta detalle venta
                $this->detalle_venta->save([
                    'id_venta' => $resultadoId,
                    'id_producto' => $row['id_producto'],
                    'nombre' => $row['nombre'],
                    'cantidad' => $row['cantidad'],
                    'precio' => $row['precio']
                ]);

                if ($producto['inventariable'] == 1 && $row['id_producto'] > 1) {
                    //Actualiza stock producto
                    $this->productos->actualizaStock($row['id_producto'], $row['cantidad'], '-');

                    $cantidad_saldo = $producto['existencias'] - $row['cantidad'];

                    $this->detalle_movimientos = new DetalleMovimientoModel();
                    //Insertar detalle de movimiento
                    $this->detalle_movimientos->insertaDetalleMov($idMov, $row['id_producto'], $row['nombre'], $producto['existencias'], $row['cantidad'], $cantidad_saldo, $row['precio']);
                }
            }

            $this->tmp_mov->eliminarMovimientoTmp($id_venta);
        }
        return redirect()->to(base_url() . "/ventas/muestraTicket/" . $resultadoId);
    }

    function muestraTicket($id_venta)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $data['id_venta'] = $id_venta;
        echo view('header');
        echo view('ventas/ver_ticket', $data);
        echo view('footer');
    }

    function generaTicket($id_venta, $previa = false)
    {
        $datosVenta = $this->ventas->query("SELECT fecha_alta, folio, cliente, total, activo  FROM v_ventas WHERE id = $id_venta")->getRowArray();
        $detalleVenta = $this->detalle_venta->select('*')->where('id_venta', $id_venta)->findAll();
        $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
        $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;
        $telefonoTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_telefono')->get()->getRow()->valor;
        $leyendaTicket = $this->configuracion->select('valor')->where('nombre', 'tkt_leyenda')->get()->getRow()->valor;
        $tkt_direccion = $this->configuracion->select('valor')->where('nombre', 'tkt_direccion')->get()->getRow()->valor;
        $tkt_telefono = $this->configuracion->select('valor')->where('nombre', 'tkt_telefono')->get()->getRow()->valor;

        $fecha = substr($datosVenta['fecha_alta'], 0, 10);
        $hora = substr($datosVenta['fecha_alta'], 11, 8);

        $pdf = new Fpdf('P', 'mm', array(80, 200));
        $pdf->AddPage();
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetTitle("Venta");
        $pdf->SetFont('Arial', 'B', 10);

        $pdf->SetXY(5, 5);

        $pdf->Multicell(70, 4, utf8_decode($nombreTienda), 0, 'C');

        $pdf->SetFont('Arial', '', 9);

        $dirTel = '';
        if ($tkt_direccion) {
            $dirTel = $direccionTienda . ' ';
        }

        if ($tkt_telefono) {
            $dirTel .= $telefonoTienda;
        }

        $pdf->Multicell(70, 4, utf8_decode($dirTel), 0, 'C');
        $pdf->Ln(2);
        $pdf->Cell(60, 5, utf8_decode('Nº ticket:  ') . $datosVenta['folio'], 0, 1, 'L');
        $pdf->MultiCell(70, 4, utf8_decode($datosVenta['cliente']), 0, 'L', 0);

        $pdf->Cell(70, 5, '======================================', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(7, 3, 'Cant.', 0, 0, 'L');
        $pdf->Cell(36, 3, utf8_decode('Descripción'), 0, 0, 'L');
        $pdf->Cell(14, 3, 'Precio', 0, 0, 'L');
        $pdf->Cell(14, 3, 'Importe', 0, 1, 'L');
        $pdf->Cell(70, 3, '---------------------------------------------------------------------------', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 6.5);
        $totalArticulos = 0;

        foreach ($detalleVenta as $row) {
            $importe = number_format($row['precio'] * $row['cantidad'], 2, '.', ',');
            $pdf->Cell(7, 4, $row['cantidad'], 0, 0, 'C');
            $y = $pdf->GetY();
            $pdf->MultiCell(36, 4, utf8_decode($row['nombre']), 0, 'L');
            $y2 = $pdf->GetY();
            $pdf->SetXY(48, $y);
            $pdf->Cell(14, 4, '$ ' . number_format($row['precio'], 2, '.', ','), 0, 0, 'C');
            $pdf->SetXY(62, $y);
            $pdf->Cell(14, 4, '$ ' . $importe, 0, 1, 'C');
            $pdf->SetY($y2);
            $totalArticulos += $row['cantidad'];
        }

        $total = $datosVenta['total'];

        $pdf->Ln(2);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(70, 5, utf8_decode('Número de articulos:  ' . $totalArticulos), 0, 1, 'R');
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(70, 5, 'Total $ ' . number_format($total, 2, '.', ','), 0, 1, 'R');

        $pdf->SetFont('Arial', '', 8);
        $pdf->Ln(1);
        $decimales = explode(".", $total);
        $pdf->MultiCell(70, 4, utf8_decode(ucfirst(strtolower(\NumeroALetras::convertir($total, 'pesos')))) . ' ' . $decimales[1] . '/100 M.N', 0, 'L', 0);

        $pdf->Ln();
        $pdf->Cell(5);
        $pdf->Cell(30, 4, 'Fecha: ' . date("d/m/Y", strtotime($fecha)), 0, 0, 'L');
        $pdf->Cell(30, 4, 'Hora: ' . $hora, 0, 1, 'L');

        $pdf->Ln();
        $pdf->Multicell(70, 4, $leyendaTicket, 0, 'C', 0);

        if ($datosVenta['activo'] == 0) {
            $pdf->SetTextColor(255, 0, 0,);
            $pdf->SetFontSize(20);
            $pdf->SetY(30);
            $pdf->Cell(0, 5, 'Venta cancelada', 0, 0, 'C');
        }

        if ($previa) {
            $dir = "assets/ticket_previa.pdf";
            $pdf->Output($dir, "F");

            return base_url() . '/' . $dir;
        } else {
            $this->response->setHeader('Content-Type', 'application/pdf');
            $pdf->Output("ticket.pdf", "I");
        }
    }

    function verTicket()
    {
        if ($this->request->isAJAX()) {
            $id_venta = $this->request->getPost('id_venta') ?? 1;
            $datosVenta = $this->ventas->query("SELECT fecha_alta, folio, cliente, total, activo  FROM v_ventas WHERE id = $id_venta")->getRowArray();
            $detalleVenta = $this->detalle_venta->select('*')->where('id_venta', $id_venta)->findAll();
            $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
            $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;
            $telefonoTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_telefono')->get()->getRow()->valor;
            $leyendaTicket = $this->configuracion->select('valor')->where('nombre', 'tkt_leyenda')->get()->getRow()->valor;
            $tkt_direccion = $this->configuracion->select('valor')->where('nombre', 'tkt_direccion')->get()->getRow()->valor;
            $tkt_telefono = $this->configuracion->select('valor')->where('nombre', 'tkt_telefono')->get()->getRow()->valor;

            $fecha = substr($datosVenta['fecha_alta'], 0, 10);
            $hora = substr($datosVenta['fecha_alta'], 11, 8);

            $pdf = new Fpdf('P', 'mm', array(80, 200));
            $pdf->AddPage();
            $pdf->SetMargins(5, 5, 5);
            $pdf->SetTitle("Venta");
            $pdf->SetFont('Arial', 'B', 10);

            $pdf->SetXY(5, 5);

            $pdf->Multicell(70, 4, utf8_decode($nombreTienda), 0, 'C');

            $pdf->SetFont('Arial', '', 9);

            $dirTel = '';
            if ($tkt_direccion) {
                $dirTel = $direccionTienda . ' ';
            }

            if ($tkt_telefono) {
                $dirTel .= $telefonoTienda;
            }

            $pdf->Multicell(70, 4, utf8_decode($dirTel), 0, 'C');
            $pdf->Ln(2);
            $pdf->Cell(60, 5, utf8_decode('Nº ticket:  ') . $datosVenta['folio'], 0, 1, 'L');
            $pdf->MultiCell(70, 4, utf8_decode($datosVenta['cliente']), 0, 'L', 0);

            $pdf->Cell(70, 5, '======================================', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(7, 3, 'Cant.', 0, 0, 'L');
            $pdf->Cell(36, 3, utf8_decode('Descripción'), 0, 0, 'L');
            $pdf->Cell(14, 3, 'Precio', 0, 0, 'L');
            $pdf->Cell(14, 3, 'Importe', 0, 1, 'L');
            $pdf->Cell(70, 3, '---------------------------------------------------------------------------', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 6.5);
            $totalArticulos = 0;

            foreach ($detalleVenta as $row) {
                $importe = number_format($row['precio'] * $row['cantidad'], 2, '.', ',');
                $pdf->Cell(7, 4, $row['cantidad'], 0, 0, 'C');
                $y = $pdf->GetY();
                $pdf->MultiCell(36, 4, utf8_decode($row['nombre']), 0, 'L');
                $y2 = $pdf->GetY();
                $pdf->SetXY(48, $y);
                $pdf->Cell(14, 4, '$ ' . number_format($row['precio'], 2, '.', ','), 0, 0, 'C');
                $pdf->SetXY(62, $y);
                $pdf->Cell(14, 4, '$ ' . $importe, 0, 1, 'C');
                $pdf->SetY($y2);
                $totalArticulos += $row['cantidad'];
            }

            $total = $datosVenta['total'];

            $pdf->Ln(2);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(70, 5, utf8_decode('Número de articulos:  ' . $totalArticulos), 0, 1, 'R');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 5, 'Total $ ' . number_format($total, 2, '.', ','), 0, 1, 'R');

            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln(1);
            $decimales = explode(".", $total);
            $pdf->MultiCell(70, 4, utf8_decode(ucfirst(strtolower(\NumeroALetras::convertir($total, 'pesos')))) . ' ' . $decimales[1] . '/100 M.N', 0, 'L', 0);

            $pdf->Ln();
            $pdf->Cell(5);
            $pdf->Cell(30, 4, 'Fecha: ' . date("d/m/Y", strtotime($fecha)), 0, 0, 'L');
            $pdf->Cell(30, 4, 'Hora: ' . $hora, 0, 1, 'L');

            $pdf->Ln();
            $pdf->Multicell(70, 4, $leyendaTicket, 0, 'C', 0);

            if ($datosVenta['activo'] == 0) {
                $pdf->SetTextColor(255, 0, 0,);
                $pdf->SetFontSize(20);
                $pdf->SetY(30);
                $pdf->Cell(0, 5, 'Venta cancelada', 0, 0, 'C');
            }

            $dir = "assets/ver_ticket.pdf";
            $pdf->Output($dir, "F");

            return base_url() . '/' . $dir;
        }
    }

    public function eliminar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '26');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $datosVenta = $this->ventas->query("SELECT fecha_alta, folio, cliente, total, activo  FROM v_ventas WHERE id = $id")->getRowArray();

        $productos = $this->detalle_venta->where('id_venta', $id)->findAll();

        //Inserta movimiento
        $this->movimientos = new MovimientosAlmacenModel();
        $idMov = $this->movimientos->insertaMovimiento($datosVenta['folio'], 'VC', $datosVenta['total'], $this->session->id_usuario);
        $this->detalle_movimientos = new DetalleMovimientoModel();

        foreach ($productos as $producto) {
            $datosProducto = $this->productos->where('id', $producto['id_producto'])->first();
            if ($datosProducto['inventariable'] == 1 && $producto['id_producto'] > 1) {
                //Actualiza stock producto
                $this->productos->actualizaStock($producto['id_producto'], $producto['cantidad'], '+');

                $cantidad_saldo = $datosProducto['existencias'] + $producto['cantidad'];

                //Insertar detalle de movimiento
                $this->detalle_movimientos->insertaDetalleMov($idMov, $producto['id_producto'], $producto['nombre'], $datosProducto['existencias'], $producto['cantidad'], $cantidad_saldo, $producto['precio']);
            }
        }

        $this->ventas->update($id, ['activo' => 0]);

        return redirect()->to(base_url() . '/ventas');
    }

    function mostrarVentas()
    {
        $db = \Config\Database::connect();

        $draw = intval($this->request->getPost("draw"));
        $start = intval($this->request->getPost("start"));
        $length = intval($this->request->getPost("length"));
        $order = $this->request->getPost("order");
        $search = $this->request->getPost("search");
        $activo = $this->request->getPost("activo");
        $search = $search['value'];
        $col = 0;
        $dir = "";

        $aColumns = array('fecha_alta', 'folio', 'cliente', 'total', 'cajero', 'id');
        $sTable = "v_ventas";

        if ($this->session->id_rol != 1) {
            $idCaja = $this->session->id_caja;
            $sWhere = "activo = $activo AND id_caja = $idCaja";
            $sWhereoRG = "activo = $activo AND id_caja = $idCaja";
        } else {
            $sWhere = "activo = $activo";
            $sWhereoRG = "activo = $activo";
        }

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
        $query = $builder->get();

        $data = array();

        if ($activo == 1) {
            foreach ($query->getResult('array') as $rows) {

                $data[] = array(
                    esc($rows['fecha_alta']),
                    esc($rows['folio']),
                    esc($rows['cliente']),
                    esc($rows['total']),
                    esc($rows['cajero']),
                    "<button onclick='ver_ticket(" . $rows['id'] . ")' class='btn btn-primary btn-sm' rel='tooltip' data-placement='top' title='Ver ticket'><i class='fas fa-list-alt'></button>",

                    "<a href='#' data-href='" . site_url('/ventas/eliminar/' . $rows['id']) . "' data-toggle='modal' data-target='#modal-confirma' rel='tooltip' data-placement='top' title='Eliminar registro' class='btn btn-danger btn-sm'><span class='fas fa-ban'></span></a>"
                );
            }
        } else {
            foreach ($query->getResult('array') as $rows) {
                $data[] = array(
                    esc($rows['fecha_alta']),
                    esc($rows['folio']),
                    esc($rows['cliente']),
                    esc($rows['total']),
                    esc($rows['cajero']),
                    "<button onclick='ver_ticket(" . $rows['id'] . ")' class='btn btn-primary btn-sm' rel='tooltip' data-placement='top' title='Ver ticket'><i class='fas fa-list-alt'></button>",
                );
            }
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
        $query = $db->query("SELECT COUNT(*) as num FROM $sTable WHERE $sWhereoRG")->getRow();
        if (isset($query)) return $query->num;
        return 0;
    }

    public function totalRegistroFiltrados($sTable, $where)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as num FROM $sTable WHERE $where")->getRow();

        if (isset($query)) return $query->num;
        return 0;
    }

    function ordenarFechaHora($fechaHora)
    {
        $fecha = substr($fechaHora, 0, 10);
        $hora = substr($fechaHora, 11);
        $arreglo = explode("-", $fecha);
        return $arreglo[2] . '-' . $arreglo[1] . '-' . $arreglo[0] . ' ' . $hora;
    }
}
