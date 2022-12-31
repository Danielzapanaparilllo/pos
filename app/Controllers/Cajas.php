<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ArqueoCajaModel, CajasModel, DetalleRolesPermisosModel, VentasModel};

class Cajas extends BaseController
{
    protected $cajas, $arqueoModel, $detalleRoles, $ventasModel, $reglas;

    public function __construct()
    {
        $this->cajas = new CajasModel();
        $this->arqueoModel = new ArqueoCajaModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->ventasModel = new VentasModel();
        $this->session = session();
        helper(['form']);

        $this->reglas = [
            'nombre' => ['label' => 'nombre', 'rules' => 'required'],
            'folio' => ['label' => 'folio', 'rules' => 'required'],
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '44');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $cajas = $this->cajas->where('activo', $activo)->findAll();
        $data = ['datos' => $cajas];
        echo view('header');

        if ($activo == 1) {
            echo view('cajas/cajas', $data);
        } else {
            echo view('cajas/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '44');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {
            $this->cajas->insert([
                'nombre' => $this->request->getPost('nombre'),
                'folio' => trim($this->request->getPost('folio'))
            ]);
            return redirect()->route('cajas');
        } else {
            $data = ['validation' => $this->validator];
            echo view('header');
            echo view('cajas/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '44');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $cajas = $this->cajas->where('id', $id)->first();
            $data = ['datos' => $cajas, 'validation' => $this->validator];

            echo view('header');
            echo view('cajas/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('cajas');
        }
    }

    public function actualizar()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post") {
            $id = $this->request->getPost('id');
            if ($this->validate($this->reglas)) {
                $this->cajas->update($id,
                ['nombre' => $this->request->getPost('nombre'),
                'folio' => trim($this->request->getPost('folio'))]);
                return redirect()->route('cajas');
            } else {
                return $this->editar($id);
            }
        }
        return redirect()->route('cajas');
    }

    public function eliminar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '44');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->cajas->update($id, ['activo' => 0]);
        }

        return redirect()->route('cajas');
    }

    public function reingresar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '44');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->cajas->update($id, ['activo' => 1]);
        }

        return redirect()->route('cajas');
    }

    public function arqueo($idCaja)
    {
        $arqueos = $this->arqueoModel->getDatos($idCaja);
        $data = ['titulo' => 'Cierres de caja', 'datos' => $arqueos];

        echo view('header');
        echo view('cajas/arqueos', $data);
        echo view('footer');
    }

    public function nuevo_arqueo()
    {
        $session = session();
        $existe = $this->arqueoModel->where(['id_caja' => $session->id_caja, 'estatus' => 1])->countAllResults();

        if ($existe > 0) {
            echo 'La caja ya está abierta';
            exit;
        }

        if ($this->request->getMethod() == "post") {
            $fecha = date('Y-m-d h:i:s');
            $existe = 0;
            $this->arqueoModel->save(['id_caja' => $session->id_caja, 'id_usuario' => $session->id_usuario, 'fecha_inicio' => $fecha, 'monto_inicial' => $this->request->getPost('monto_inicial'), 'estatus' => 1]);
            return redirect()->to(base_url() . '/cajas');
        } else {

            $caja = $this->cajas->where('id', $session->id_caja)->first();
            $data = ['titulo' => 'Apertura de caja', 'caja' => $caja, 'session' => $session];
            echo view('header');
            echo view('cajas/nuevo_arqueo', $data);
            echo view('footer');
        }
    }

    public function cerrar()
    {
        $session = session();

        if ($this->request->getMethod() == "post") {
            $fecha = date('Y-m-d h:i:s');

            $this->arqueoModel->update($this->request->getPost('id_arqueo'), ['fecha_fin' => $fecha, 'monto_final' => $this->request->getPost('monto_final'), 'total_ventas' => $this->request->getPost('total_ventas'), 'estatus' => 0]);

            return redirect()->to(base_url() . '/cajas');
        } else {
            $montoTotal = $this->ventasModel->totalDia(date('Y-m-d'));
            $arqueo = $this->arqueoModel->where(['id_caja' => $session->id_caja, 'estatus' => 1])->first();
            $caja = $this->cajas->where('id', $session->id_caja)->first();

            $data = ['titulo' => 'Cerrar caja', 'caja' => $caja, 'session' => $session, 'arqueo' => $arqueo, 'monto' => $montoTotal];

            echo view('header');
            echo view('cajas/cerrar', $data);
            echo view('footer');
        }
    }
}
