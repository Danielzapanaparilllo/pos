<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CategoriasModel, ConfiguracionModel, DetalleMovimientoModel, DetalleRolesPermisosModel, MovimientosAlmacenModel, ProductosModel, UnidadesModel};
use App\ThirdParty\Fpdf\Fpdf;

class Productos extends BaseController
{
    protected $config, $productos, $detalleRoles, $session, $movimientos, $detalle_movimientos, $reglas;

    public function __construct()
    {
        $this->productos = new ProductosModel();
        $this->unidades = new UnidadesModel();
        $this->categorias = new CategoriasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->session = Session();

        $this->reglas = [
            'codigo' => ['label' => 'código', 'rules' => 'required|is_unique[productos.codigo]'],
            'nombre' => ['label' => 'nombre', 'rules' => 'required'],
            'precio_venta' => ['label' => 'precio de venta', 'rules' => 'required|greater_than[0]'],
            'precio_compra' => ['label' => 'precio de costo', 'rules' => 'numeric'],
            'stock_minimo' => ['label' => 'stock mínimo', 'rules' => 'numeric'],
            'existencias' => ['label' => 'existencias', 'rules' => 'numeric']
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '2');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $productos = $this->productos->where('activo', $activo)->findAll();
        $data = ['datos' => $productos];
        echo view('header');

        if ($activo == 1) {
            echo view('productos/productos', $data);
        } else {
            echo view('productos/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '2');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {

            $codigo = $this->request->getPost('codigo');
            $nombre = $this->request->getPost('nombre');
            $tipo_venta = $this->request->getPost('tipo_venta');
            $inventariable = $this->request->getPost('inventariable') ?? 0;
            $existencias = preg_replace('([^0-9\.])', '', $this->request->getPost('existencias'));
            $precio_venta = preg_replace('([^0-9\.])', '', $this->request->getPost('precio_venta'));
            $precio_compra = preg_replace('([^0-9\.])', '', $this->request->getPost('precio_compra'));
            $stock_minimo = preg_replace('([^0-9\.])', '', $this->request->getPost('stock_minimo'));

            $this->productos->insert([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo_venta' => $tipo_venta,
                'precio_venta' => $precio_venta,
                'precio_compra' => $precio_compra,
                'inventariable' => $inventariable,
                'existencias' => $existencias,
                'stock_minimo' => $stock_minimo,
                'id_unidad' => $this->request->getPost('id_unidad'),
                'id_categoria' => $this->request->getPost('id_categoria')
            ]);
            $id = $this->productos->insertID();

            if ($id) {
                if ($inventariable && $existencias > 0) {
                    $this->movimientos = new MovimientosAlmacenModel();

                    $total = number_format($precio_venta * $existencias, 2, '.', '');

                    $this->config = new ConfiguracionModel();
                    $folio = $this->config->folioActual('');

                    $idMov = $this->movimientos->insertaMovimiento($folio, 'EI', $total, $this->session->id_usuario);

                    if ($idMov) {
                        $this->config->siguienteFolio('');
                        $this->detalle_movimientos = new DetalleMovimientoModel();
                        $this->detalle_movimientos->insertaDetalleMov($idMov, $id, $nombre, 0, $existencias, $existencias, $precio_venta);
                    }
                }
            }

            $validarImagen = $this->validate([
                'img_producto' => [
                    'uploaded[img_producto]',
                    'mime_in[img_producto,image/jpg,image/jpeg]',
                    'max_size[img_producto,512]',
                ],
            ]);

            $msg = 'Error al subir el archivo.';

            if ($validarImagen) {
                $ruta =  'images/productos/';
                $img = $this->request->getFile('img_producto');

                if (!file_exists($ruta)) {
                    mkdir($ruta, 0777, true);
                }

                if ($img->isValid() && !$img->hasMoved()) {
                    $img->move($ruta, $id . '.jpg');
                }
            }

            return redirect()->route('productos');
        } else {
            $unidades = $this->unidades->where('activo', 1)->findAll();
            $categorias = $this->categorias->where('activo', 1)->findAll();
            $data = ['unidades' => $unidades, 'categorias' => $categorias, 'validation' => $this->validator];

            echo view('header');
            echo view('productos/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '2');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null && $id != 1) {
            $unidades = $this->unidades->where('activo', 1)->findAll();
            $categorias = $this->categorias->where('activo', 1)->findAll();
            $producto = $this->productos->where('id', $id)->first();
            $data = ['unidades' => $unidades, 'categorias' => $categorias, 'producto' => $producto, 'validation' => $this->validator];

            echo view('header');
            echo view('productos/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('productos');
        }
    }

    public function actualizar()
    {
        if ($this->request->getMethod() == "post") {
            $id = $this->request->getPost('id');

            $this->reglasEditar = [
                'codigo' => ['label' => 'código', 'rules' => 'required|trim|is_unique[productos.codigo,id,{id}]'],
                'nombre' => ['label' => 'nombre', 'rules' => 'required|trim'],
                'precio_venta' => ['label' => 'precio venta', 'rules' => 'required|greater_than[0]'],
                'precio_compra' => ['label' => 'precio compra', 'rules' => 'numeric'],
                'stock_minimo' => ['label' => 'stock mínimo', 'rules' => 'numeric']
            ];

            if ($this->validate($this->reglasEditar)) {

                $this->productos->update($id, [
                    'codigo' => $this->request->getPost('codigo'),
                    'nombre' => $this->request->getPost('nombre'),
                    'precio_venta' => preg_replace('([^0-9\.])', '', $this->request->getPost('precio_venta')),
                    'precio_compra' => preg_replace('([^0-9\.])', '', $this->request->getPost('precio_compra')),
                    'inventariable' => $this->request->getPost('inventariable') ?? 0,
                    'stock_minimo' => preg_replace('([^0-9\.])', '', $this->request->getPost('stock_minimo')),
                    'id_unidad' => $this->request->getPost('id_unidad'),
                    'id_categoria' => $this->request->getPost('id_categoria')
                ]);

                $validarImagen = $this->validate([
                    'img_producto' => [
                        'uploaded[img_producto]',
                        'mime_in[img_producto,image/jpg,image/jpeg]',
                        'max_size[img_producto,512]',
                    ],
                ]);

                $msg = 'Error al subir el archivo.';

                if ($validarImagen) {
                    $ruta =  'images/productos/';
                    $imagen =  'images/productos/' . $id . 'jpg';
                    $img = $this->request->getFile('img_producto');

                    if (!file_exists($ruta)) {
                        mkdir($ruta, 0777, true);
                    }

                    if (file_exists($imagen)) {
                        unlink($imagen);
                    }

                    if ($img->isValid() && !$img->hasMoved()) {
                        $img->move($ruta, $id . '.jpg');
                    }
                }
            } else {
                $this->editar($id);
            }
        }
        return redirect()->to(site_url('productos'));
    }

    public function eliminar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '2');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null && $id != 1) {
            $this->productos->update($id, ['activo' => 0]);
        }
        return redirect()->to(site_url('productos'));
    }

    public function reingresar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '2');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->productos->update($id, ['activo' => 1]);
        }
        return redirect()->to(site_url('productos'));
    }

    function mostrarProductos()
    {
        $draw = intval($this->request->getPost("draw"));
        $start = intval($this->request->getPost("start"));
        $length = intval($this->request->getPost("length"));
        $order = $this->request->getPost("order");
        $search = $this->request->getPost("search");
        $activo = $this->request->getPost("activo");
        $search = $search['value'];
        $col = 0;
        $dir = "";

        $aColumns = array('codigo', 'nombre', 'precio_venta', 'tipo_venta', 'existencias', 'id', 'inventariable');
        $sTable = "productos";
        $sWhere = "activo = $activo AND id > 1";
        $sWhereoRG = "activo = $activo AND id > 1";

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
            $this->productos->orderBy($order, $dir);

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

        $this->productos->where($sWhere);
        $this->productos->limit($length, $start);
        $query = $this->productos->get();

        $data = array();

        if ($activo == 1) {
            foreach ($query->getResult('array') as $rows) {

                $ruta_imagen = 'images/productos/' . $rows['id'] . '.jpg';
                $ruta_sin_imagen = base_url() . '/images/productos/no-photo.jpg';
                if (file_exists($ruta_imagen)) {
                    $colImg = '<td><img src="' . base_url() . '/' . $ruta_imagen . '" width="48" /></td>';
                } else {
                    $colImg = '<td><img src="' . $ruta_sin_imagen . '" width="48" /></td>';
                }



                if ($rows['inventariable']) {
                    if ($rows['tipo_venta'] == 'P') {
                        $existencias =  number_format($rows['existencias'], 0);
                        $tipo = 'Pieza';
                    } else {
                        $existencias = $rows['existencias'];
                        $tipo = 'Granel';
                    }
                } else {
                    $existencias =  'N/A';
                    $tipo = 'Unidad';
                }

                $data[] = array(
                    esc($rows['codigo']),
                    esc($rows['nombre']),
                    esc($rows['precio_venta']),
                    esc($existencias),
                    esc($tipo),
                    $colImg,
                    "<a href='" . site_url('/productos/editar/' . $rows['id']) . "' class='btn btn-warning btn-sm' rel='tooltip' data-placement='top' title='Modificar registro'><i class='fas fa-pencil-alt'></a>",
                    "<a href='#' data-href='" . site_url('/productos/eliminar/' . $rows['id']) . "' data-toggle='modal' data-target='#modal-confirma' rel='tooltip' data-placement='top' title='Eliminar registro' class='btn btn-danger btn-sm'><span class='fas fa-trash'></span></a>"
                );
            }
        } else {
            foreach ($query->getResult('array') as $rows) {
                $data[] = array(
                    esc($rows['codigo']),
                    esc($rows['nombre']),
                    esc($rows['precio_venta']),
                    esc($rows['tipo_venta']),
                    esc($rows['existencias']),
                    "<a href='#' data-href='" . site_url('/productos/reingresar/' . $rows['id']) . "' data-toggle='modal' data-target='#modal-confirma' rel='tooltip' data-placement='top' title='Reingresar registro' class='btn btn-danger btn-sm'><i class='fas fa-trash'></i></a>"
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
        $query = $this->productos->query("SELECT COUNT(*) as num FROM $sTable WHERE $sWhereoRG")->getRow();
        if (isset($query)) return $query->num;
        return 0;
    }

    public function totalRegistroFiltrados($sTable, $where)
    {
        $query = $this->productos->query("SELECT COUNT(*) as num FROM $sTable WHERE $where")->getRow();

        if (isset($query)) return $query->num;
        return 0;
    }

    public function buscarPorCodigo()
    {
        if ($this->request->isAJAX()) {
            $codigo = $this->request->getPost("codigo");
            $where = "activo = 1 AND id > 1 AND codigo LIKE '$codigo'";
            $this->productos->select('*');
            $this->productos->where($where);
            $datos = $this->productos->get()->getRow();

            $res['existe'] = false;
            $res['datos'] = '';
            $res['error'] = '';

            if ($datos) {
                if ($datos->inventariable) {
                    $resultado = array();
                    $resultado['id'] = $datos->id;
                    $resultado['nombre'] = $datos->nombre;
                    if ($datos->tipo_venta == 'P') {
                        $resultado['existencias'] = number_format($datos->existencias, 0);
                    } else {
                        $resultado['existencias'] = number_format($datos->existencias, 2, '.', '');
                    }
                    $res['datos'] = $resultado;
                    $res['existe'] = true;
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

    public function buscarProductoVenta()
    {
        if ($this->request->getMethod() == "post") {
            $valor = $this->request->getPost('valor');

            $where = "activo = 1 AND id > 1 AND (codigo LIKE '%$valor%' OR nombre` LIKE '%$valor%')";
            $this->productos->select('*');
            $this->productos->where($where);
            $this->productos->limit(5);
            $datos = $this->productos->get()->getResultArray();

            $tbody = '';

            if (!empty($datos)) {
                foreach ($datos as $row) {
                    $tbody .= '<tr>';
                    $tbody .= '<td>' . $row['nombre'] . '</td>';
                    $tbody .= '<td>' . $row['precio_venta'] . '</td>';
                    $tbody .= '<td>' . $row['existencias'] . '</td>';
                    $tbody .= "<td><button class='btn btn-success btn-sm' id='add_producto' rel='tooltip' data-placement='top' title='Agregar producto' onclick='enviaProductoModal(\"" . $row['codigo'] . "\", 1);'><span class='fas fa-plus'></span></button></td>";
                    $tbody .= '</tr>';
                }
            }

            echo json_encode($tbody);
        }
    }

    public function autocompleteData()
    {
        $returnData = array();

        $valor = $this->request->getGet('term');

        $where = "activo = 1 AND id > 1 AND codigo LIKE '%$valor%'";
        $productos = $this->productos->where($where)->findAll();
        if (!empty($productos)) {
            foreach ($productos as $row) {
                $data['id'] = $row['id'];
                $data['value'] = $row['codigo'];
                $data['label'] = $row['codigo'] . ' - ' . $row['nombre'];
                array_push($returnData, $data);
            }
        }

        echo json_encode($returnData);
    }

    function muestraCodigos()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        echo view('header');
        echo view('productos/ver_codigos');
        echo view('footer');
    }

    public function generaBarras()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $pdf = new Fpdf('P', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Codigos de barras");

        $productos = $this->productos->where('activo', 1)->findAll();
        foreach ($productos as $producto) {
            $codigo = $producto['codigo'];

            $generaBarcode = new \barcode_genera();
            $generaBarcode->barcode("images/barcode/" . $codigo . ".png", $codigo, 20, "horizontal", "code39", true);

            $pdf->Image("images/barcode/" . $codigo . ".png");
        }

        $this->response->setContentType('application/pdf');
        $pdf->Output('Codigo.pdf', 'I');
    }

    function mostrarMinimos()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        echo view('header');
        echo view('productos/ver_minimos');
        echo view('footer');
    }

    public function generaMinimosPdf()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $pdf = new FPDF('P', 'mm', 'letter');
        $pdf->AddPage();
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetTitle("Producto con stock minimo");
        $pdf->SetFont("Arial", 'B', 10);

        $pdf->Image("images/logotipo.png", 10, 5, 15);

        $pdf->Cell(0, 5, utf8_decode("Reporte de producto con stock mínimo"), 0, 1, 'C');

        $pdf->Ln(10);

        $pdf->Cell(40, 5, utf8_decode("Código"), 1, 0, "C");
        $pdf->Cell(85, 5, utf8_decode("Nombre"), 1, 0, "C");
        $pdf->Cell(30, 5, utf8_decode("Existencias"), 1, 0, "C");
        $pdf->Cell(30, 5, utf8_decode("Stock mínimo"), 1, 1, "C");

        $datosProductos = $this->productos->getProductosMinimo();

        foreach ($datosProductos as $producto) {
            $pdf->Cell(40, 5, $producto['codigo'], 1, 0, "C");
            $pdf->Cell(85, 5, utf8_decode($producto['nombre']), 1, 0, "C");
            $pdf->Cell(30, 5, $producto['existencias'], 1, 0, "C");
            $pdf->Cell(30, 5, $producto['stock_minimo'], 1, 1, "C");
        }

        $this->response->setContentType('application/pdf');
        $pdf->Output('ProductoMinimo.pdf', 'I');
    }
}
