<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CajasModel, ConfiguracionModel, DetalleRolesPermisosModel, DetalleVentaModel, ProductosModel, VentasModel};
use App\ThirdParty\Fpdf\PlantillaVentas;

class Reportes extends BaseController
{
    protected $detalleRoles, $ventas, $temporal_compra, $detalle_venta, $productos, $configuracion, $cajas, $session;

    public function __construct()
    {
        $this->ventas = new VentasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->detalle_venta = new DetalleVentaModel();
        $this->productos = new ProductosModel();
        $this->configuracion = new ConfiguracionModel();
        $this->cajas = new CajasModel();
        $this->session = session();

        helper(['form']);
    }

    //Muestra vista para filtrar reporte
    function detalle_reporte_venta()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '28');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $idRol = $this->session->id_rol;

        if ($idRol == 1) {
            $cajas = $this->cajas->where('activo', 1)->findAll();
        } else {
            $cajas = $this->cajas->where(['activo' => 1, 'id' => $idRol])->findAll();
        }

        $data = ['cajas' => $cajas, 'idRol' => $idRol];

        echo view('header');
        echo view('reportes/detalle_reporte_venta', $data);
        echo view('footer');
    }


    function reporte_ventas()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $fecha_inicio = $this->request->getPost("fecha_inicio");
        $fecha_fin = $this->request->getPost("fecha_fin");
        $caja = $this->request->getPost("caja");

        $data = ['fecha_inicio' => $fecha_inicio, 'fecha_fin' => $fecha_fin, 'caja' => $caja];

        echo view('header');
        echo view('reportes/ver_reporte_ventas', $data);
        echo view('footer');
    }

    function pdf_reporte_ventas($fecha_inicio, $fecha_fin, $caja)
    {
        $where = '';

        if ($caja != 0) {
            $where = ' AND ventas.id_caja=' . $caja;
        }

        $db = \Config\Database::connect();

        $builder = $db->table('ventas');
        $builder->select('ventas.fecha_alta, ventas.folio, ventas.total, clientes.nombre');
        $builder->join('clientes', 'ventas.id_cliente = clientes.id'); //INNER JOIN
        $builder->where("ventas.activo = 1 AND DATE(ventas.fecha_alta) BETWEEN '$fecha_inicio' AND '$fecha_fin' $where");
        $builder->orderBy('DATE(ventas.fecha_alta) DESC');
        $query = $builder->get();

        $logo = base_url() . '/images/logotipo.png';

        $datos = array('titulo' => 'Reporte de ventas', 'logo' => $logo, 'inicio' => $this->ordenarFecha($fecha_inicio), 'fin' => $this->ordenarFecha($fecha_fin));

        $pdf = new PlantillaVentas('P', 'mm', 'letter', $datos);
        $pdf->SetTitle('Reporte de ventas');
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->SetWidths(array(35, 35, 70, 35));
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Row(array('Fecha', 'Folio', 'Cliente', 'Total'));
        $pdf->SetFont('Arial', '', 8);

        $total = 0;
        $numVentas = 0;

        foreach ($query->getResult('array') as $row) {
            $pdf->Row(array($this->ordenarFechaHora($row['fecha_alta']), $row['folio'], utf8_decode($row['nombre']), number_format($row['total'], 2, '.', ',')));
            $total = $total + $row['total'];
            ++$numVentas;
        }

        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(140, 5, 'Total', 0, 0, 'R');
        $pdf->Cell(35, 5, number_format($total, 2, '.', ','), 1, 1, 'C');
        $pdf->Ln(2);
        $pdf->Cell(70, 5, utf8_decode('Número de ventas: ') . $numVentas, 0, 0, 'L');

        $pdf->SetFont('Arial', '', 8);

        //$pdf->Output("I", 'Reporte de ventas');

        $this->response->setHeader('Content-Type', 'application/pdf');
        $pdf->Output("ReporteVentas.pdf", "I");
    }

    function ordenarFecha($fecha)
    {
        $arreglo = explode("-", $fecha);
        return $arreglo[2] . '-' . $arreglo[1] . '-' . $arreglo[0];
    }

    function ordenarFechaHora($fechaHora)
    {
        $fecha = substr($fechaHora, 0, 10);
        $hora = substr($fechaHora, 11);
        $arreglo = explode("-", $fecha);
        return $arreglo[2] . '-' . $arreglo[1] . '-' . $arreglo[0] . ' ' . $hora;
    }
}
