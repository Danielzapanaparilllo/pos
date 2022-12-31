<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{DetalleRolesPermisosModel, UnidadesModel};

class Unidades extends BaseController
{
    protected $detalleRoles, $unidades, $reglas, $session;

    public function __construct()
    {
        $this->unidades = new UnidadesModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->session = Session();
        helper(['form']);

        $this->reglas = [
            'nombre' => ['label' => 'nombre', 'rules' => 'required'],
            'nombre_corto' => ['label' => 'nombre corto', 'rules' => 'required']
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '7');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $unidades = $this->unidades->where('activo', $activo)->findAll();
        $data = ['datos' => $unidades];
        echo view('header');

        if ($activo == 1) {
            echo view('unidades/unidades', $data);
        } else {
            echo view('unidades/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '7');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {
            $this->unidades->insert(['nombre' => $this->request->getPost('nombre'), 'nombre_corto' => $this->request->getPost('nombre_corto')]);
            return redirect()->to(base_url() . '/unidades');
        } else {
            $data = ['validation' => $this->validator];

            echo view('header');
            echo view('unidades/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '7');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {

            $unidad = $this->unidades->where('id', $id)->first();
            $data = ['datos' => $unidad, 'validation' => $this->validator];

            echo view('header');
            echo view('unidades/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('unidades');
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
                $this->unidades->update($id, ['nombre' => $this->request->getPost('nombre'), 'nombre_corto' => $this->request->getPost('nombre_corto')]);
            } else {
                return $this->editar($id);
            }
        }
        return redirect()->route('unidades');
    }

    public function eliminar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '7');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->unidades->update($id, ['activo' => 0]);
        }
        return redirect()->route('unidades');
    }

    public function reingresar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '7');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->unidades->update($id, ['activo' => 1]);
        }
        return redirect()->route('unidades');
    }
}
