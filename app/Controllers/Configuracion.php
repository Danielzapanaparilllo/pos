<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ConfiguracionModel, DetalleRolesPermisosModel, LogsModel};
use App\ThirdParty\Fpdf\Fpdf;

class Configuracion extends BaseController
{
    protected $configuracion, $detalleRoles, $logs, $reglas, $session;

    public function __construct()
    {
        $this->configuracion = new ConfiguracionModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->logs = new LogsModel();
        $this->session = session();
        helper(['form', 'upload']);

        $this->reglasDatos = [
            'tienda_nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];
    }

    public function index()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '31');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $datos = array();
        $config = $this->configuracion->findAll();
        foreach ($config as $row) {
            $datos[$row['nombre']] = $row['valor'];
        }

        $data = ['config' => $datos];

        echo view('header');
        echo view('configuracion/configuracion', $data);
        echo view('footer');
    }

    public function datos()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '30');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $nombre = $this->configuracion->where('nombre', 'tienda_nombre')->first();
        $rfc = $this->configuracion->where('nombre', 'tienda_rfc')->first();
        $telefono = $this->configuracion->where('nombre', 'tienda_telefono')->first();
        $email = $this->configuracion->where('nombre', 'tienda_email')->first();
        $direccion = $this->configuracion->where('nombre', 'tienda_direccion')->first();

        $data = ['nombre' => $nombre, 'rfc' => $rfc, 'telefono' => $telefono, 'email' => $email, 'direccion' => $direccion, 'validation' => $this->validator];

        echo view('header');
        echo view('configuracion/datos', $data);
        echo view('footer');
    }

    public function actualizar()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post" && $this->validate($this->reglasDatos)) {

            $this->configuracion->whereIn('nombre', ['tienda_nombre'])->set(['valor' => $this->request->getPost('tienda_nombre')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_telefono'])->set(['valor' => $this->request->getPost('tienda_telefono')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_email'])->set(['valor' => $this->request->getPost('tienda_email')])->update();
            $this->configuracion->whereIn('nombre', ['tienda_direccion'])->set(['valor' => $this->request->getPost('tienda_direccion')])->update();

            if ($this->request->getFile('tienda_logo')->isValid()) {

                $validacion = $this->validate([
                    'tienda_logo' => [
                        'uploaded[tienda_logo]',
                        'mime_in[tienda_logo,image/png]',
                        'max_size[tienda_logo, 2048]'
                    ]
                ]);

                if ($validacion) {

                    $ruta_logo = "images/logotipo.png";

                    if (file_exists($ruta_logo)) {
                        unlink($ruta_logo);
                    }

                    $img = $this->request->getFile('tienda_logo');
                    if ($img->isValid() && !$img->hasMoved()) {
                        $img->move('./images', 'logotipo.png');
                    }
                }
            }
        }
        $this->datos();
    }

    public function actualizaConfiguracion()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $this->configuracion->whereIn('nombre', ['tkt_direccion'])->set(['valor' => $this->request->getPost('tkt_direccion') ?? 0])->update();
        $this->configuracion->whereIn('nombre', ['tkt_telefono'])->set(['valor' => $this->request->getPost('tkt_telefono') ?? 0])->update();
        $this->configuracion->whereIn('nombre', ['tkt_folio'])->set(['valor' => $this->request->getPost('tkt_folio') ?? 0])->update();
        $this->configuracion->whereIn('nombre', ['tkt_leyenda'])->set(['valor' => $this->request->getPost('tkt_leyenda')])->update();
        /*$this->configuracion->whereIn('nombre', ['conf_moneda'])->set(['valor' => $this->request->getPost('moneda')])->update();
            $this->configuracion->whereIn('nombre', ['conf_separa_miles'])->set(['valor' => $this->request->getPost('separa_miles')])->update();
            $this->configuracion->whereIn('nombre', ['conf_separa_decimales'])->set(['valor' => $this->request->getPost('separa_decimales')])->update();*/
        return redirect()->to(base_url() . '/configuracion');
    }

    public function subirLogo()
    {
        $data = array();
        $data['token'] = csrf_hash();

        $validacion = $this->validate([
            'tienda_logo' => [
                'uploaded[tienda_logo]',
                'mime_in[tienda_logo,image/png]',
                'max_size[tienda_logo, 1024]'
            ]
        ]);

        if ($validacion) {

            $ruta_logo = "images/logo_tmp.png";

            if (file_exists($ruta_logo)) {
                unlink($ruta_logo);
            }

            $img = $this->request->getFile('tienda_logo');
            $img->move('./images', 'logo_tmp.png');

            $data['success'] = 1;
            return $this->response->setJSON($data);
        } else {
            $data['success'] = 0;
            return $this->response->setJSON($data);
        }
    }

    public function previaTicket()
    {
        if ($this->request->isAJAX()) {
            $leyenda = $this->request->getPost('leyenda') ?? 'Gracias por su compra';
            $direccion = $this->request->getPost('direccion') ?? 1;
            $telefono = $this->request->getPost('telefono') ?? 1;
            $folio = $this->request->getPost('folio') ?? 1;

            /* Se implementara más adelante 
           $moneda = $this->request->getPost('moneda') ?? '$';

            $miles = $this->request->getPost('miles') ?? ',';
            if ($miles == '' || $miles == null) {
                $miles = ',';
            }
            $decimales = $this->request->getPost('decimales') ?? '.';
            if ($decimales == '' || $decimales == null) {
                $decimales = '.';
            }*/

            $moneda = '$';
            $miles = ',';
            $decimales = '.';

            $nombreTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_nombre')->get()->getRow()->valor;
            $direccionTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_direccion')->get()->getRow()->valor;
            $telefonoTienda = $this->configuracion->select('valor')->where('nombre', 'tienda_telefono')->get()->getRow()->valor;

            $pdf = new FPDF('P', 'mm', array(80, 120));
            $pdf->AddPage();
            $pdf->SetMargins(5, 5, 5);
            $pdf->SetTitle("Venta");
            $pdf->SetFont('Arial', 'B', 10);

            $pdf->SetXY(5, 5);

            $pdf->Multicell(70, 4, utf8_decode($nombreTienda), 0, 'C');

            $pdf->SetFont('Arial', '', 9);

            $dirTel = '';
            if ($direccion) {
                $dirTel = $direccionTienda . ' ';
            }

            if ($telefono) {
                $dirTel .= $telefonoTienda;
            }

            if (strlen($dirTel) > 0) {
                $pdf->Multicell(70, 4, utf8_decode($dirTel), 0, 'C');
            }

            $pdf->Ln(2);

            if ($folio) {
                $pdf->Cell(60, 5, utf8_decode('Nº ticket: 000001'), 0, 1, 'L');
            }
            $pdf->MultiCell(70, 4, utf8_decode('Público en General'), 0, 'L', 0);

            $pdf->Cell(70, 5, '======================================', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(7, 3, 'Cant.', 0, 0, 'L');
            $pdf->Cell(36, 3, utf8_decode('Descripción'), 0, 0, 'L');
            $pdf->Cell(14, 3, 'Precio', 0, 0, 'L');
            $pdf->Cell(14, 3, 'Importe', 0, 1, 'L');
            $pdf->Cell(70, 3, '---------------------------------------------------------------------------', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 6.5);

            $pdf->Cell(7, 4, 2, 0, 0, 'C');
            $y = $pdf->GetY();
            $pdf->MultiCell(36, 4, 'Bebida hidratante 1 lt', 0, 'L');
            $y2 = $pdf->GetY();
            $pdf->SetXY(48, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(26, 2, $decimales, $miles), 0, 0, 'C');
            $pdf->SetXY(62, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(52, 2, $decimales, $miles), 0, 1, 'C');
            $pdf->SetY($y2);

            $pdf->Cell(7, 4, 1, 0, 0, 'C');
            $y = $pdf->GetY();
            $pdf->MultiCell(34, 4, 'Papel higienico paquete 4 pzas', 0, 'L');
            $y2 = $pdf->GetY();
            $pdf->SetXY(48, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(41, 2, $decimales, $miles), 0, 0, 'C');
            $pdf->SetXY(62, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(41, 2, $decimales, $miles), 0, 1, 'C');
            $pdf->SetY($y2);

            $pdf->Cell(7, 4, 1, 0, 0, 'C');
            $y = $pdf->GetY();
            $pdf->MultiCell(34, 4, 'Producto costoso', 0, 'L');
            $y2 = $pdf->GetY();
            $pdf->SetXY(48, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(999.99, 2, $decimales, $miles), 0, 0, 'C');
            $pdf->SetXY(62, $y);
            $pdf->Cell(14, 4, $moneda . ' ' . number_format(999.99, 2, $decimales, $miles), 0, 1, 'C');
            $pdf->SetY($y2);


            $pdf->Ln(2);
            $pdf->SetFont('Arial', '', 8);
            $pdf->Cell(70, 5, utf8_decode('Número de articulos:  4'), 0, 1, 'R');
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(70, 5, 'Total:  ' . $moneda . ' ' . number_format(1092.99, 2, $decimales, $miles), 0, 1, 'R');

            $pdf->SetFont('Arial', '', 8);
            $pdf->Ln(1);
            $decimales = explode(".", 1092.99);
            $pdf->MultiCell(70, 4, utf8_decode(ucfirst(strtolower(\NumeroALetras::convertir(1092.99, 'pesos')))) . ' ' . $decimales[1] . '/100 M.N', 0, 'L', 0);

            $pdf->Ln();
            $pdf->Cell(5);
            $pdf->Cell(30, 4, 'Fecha: ' . date("d/m/Y"), 0, 0, 'L');
            $pdf->Cell(30, 4, 'Hora: ' . date('H:i:s'), 0, 1, 'L');

            $pdf->Ln();
            $pdf->Multicell(70, 4, $leyenda, 0, 'C', 0);

            $dir = "assets/ticket_previa.pdf";
            $pdf->Output($dir, "F");

            return base_url() . '/' . $dir;
        }
    }

    public function logs()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '49');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $datos = array();
        $datos = $this->logs->findAll();
        $data = ['datos' => $datos];

        echo view('header');
        echo view('configuracion/logs', $data);
        echo view('footer');
    }

    function mostrarLogs()
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

        $aColumns = array('usuarios.usuario', 'logs.ip', 'logs.evento', 'logs.detalles', 'logs.fecha');
        $sTable = "logs";
        $sWhere = "1 = 1";
        $sWhereoRG = "1 = 1";

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
        $builder->join('usuarios', 'logs.id_usuario = usuarios.id');
        $query = $builder->get();

        $data = array();

        foreach ($query->getResult('array') as $rows) {
            $data[] = array(
                esc($rows['usuario']),
                esc($rows['ip']),
                esc($rows['evento']),
                esc($rows['detalles']),
                esc($rows['fecha'])
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
        $query = $db->query("SELECT COUNT(*) as num FROM logs INNER JOIN usuarios ON logs.id_usuario = usuarios.id WHERE $sWhereoRG")->getRow();
        if (isset($query)) return $query->num;
        return 0;
    }

    public function totalRegistroFiltrados($sTable, $where)
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT COUNT(*) as num FROM logs INNER JOIN usuarios ON logs.id_usuario = usuarios.id WHERE $where")->getRow();

        if (isset($query)) return $query->num;
        return 0;
    }
}
