<?php

namespace App\Controllers;

use App\Models\{DetalleVentaModel, ProductosModel, VentasModel};
use DateTime;

class Inicio extends BaseController
{
	protected $productoModel, $ventasModel, $session, $detalleVentaModel;

	public function __construct()
	{
		$this->productoModel = new ProductosModel();
		$this->ventasModel = new VentasModel();
		$this->detalleVentaModel = new DetalleVentaModel();
		$this->session = session();
	}

	public function index()
	{
		if (!isset($this->session->id_usuario)) {
			return redirect()->to(base_url());
		}

		$idCaja = 0;
		if ($this->session->id_rol != 0) {
			$idCaja = $this->session->id_caja;
		}

		$total = $this->productoModel->totalProductos();
		$minimos = $this->productoModel->productosMinimo();

		$hoy = date('Y-m-d');
		$lunes = date('Y-m-d', strtotime('monday this week'));
		$domingo = date('Y-m-d', strtotime('sunday this week'));

		$totalVentas = $this->ventasModel->totalDia($hoy, $idCaja);

		$fechaInicial = new DateTime($lunes);
		$fechaFinal = new DateTime($domingo);

		$diasVentas = array();

		for ($i = $fechaInicial; $i <= $fechaFinal; $i->modify('+1 day')) {
			$monto =  $this->ventasModel->totalDia($i->format('Y-m-d'), $idCaja)['total'];
			$diasVentas[] = $monto;
		}

		$dias_separado = implode(",", $diasVentas);

		$fecha_ini = date('Y-m') . '-01';
		$ultimoDia = date("d", (mktime(0, 0, 0, date('m') + 1, 1, date('y')) - 1));
		$fecha_fin = date('Y-m') . '-' . $ultimoDia;

		$listaProductos = $this->detalleVentaModel->productosMasVendidos($fecha_ini, $fecha_fin);
		$nombreProductos = [];
		$cantidadProductos = [];

		foreach ($listaProductos as $producto) {
			$nombreProductos[] = $producto['nombre'];
			$cantidadProductos[] = $producto['cantidad'];
		}

		$productos_separado = implode("','", $nombreProductos);
		$cantidad_separado = implode(",", $cantidadProductos);

		$datos = ['total' => $total, 'totalVentas' => $totalVentas, 'minimos' => $minimos, 'diasVentas' => $dias_separado, 'nombreProductos' => $productos_separado, 'cantidadProductos' => $cantidad_separado, 'idRol' => $this->session->id_rol];

		echo view('header');
		echo view('inicio', $datos);
		echo view('footer');
	}
}
