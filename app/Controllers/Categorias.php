<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CategoriasModel, DetalleRolesPermisosModel};

class Categorias extends BaseController
{
    protected $categorias, $detalleRoles, $reglas, $session;

    public function __construct()
    {
        $this->categorias = new CategoriasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->session = Session();
        helper(['form']);

        $this->reglas = [
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $categorias = $this->categorias->where('activo', $activo)->findAll();
        $data = ['datos' => $categorias];
        echo view('header');

        if ($activo == 1) {
            echo view('categorias/categorias', $data);
        } else {
            echo view('categorias/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {
            $this->categorias->insert(['nombre' => $this->request->getPost('nombre'), 'descripcion' => $this->request->getPost('descripcion')]);
            return redirect()->route('categorias');
        } else {
            $data = ['validation' => $this->validator];

            echo view('header');
            echo view('categorias/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $categoria = $this->categorias->where('id', $id)->first();
            $data = ['datos' => $categoria, 'validation' => $this->validator];

            echo view('header');
            echo view('categorias/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('categorias');
        }
    }

    public function actualizar()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == "post") {
            $id = $this->request->getPost('id');

            if ($this->validate($this->reglas)) {
                $this->categorias->update($id, ['nombre' => $this->request->getPost('nombre'), 'descripcion' => $this->request->getPost('descripcion')]);
            } else {
                return $this->editar($id);
            }
        }
        return redirect()->route('categorias');
    }

    public function eliminar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->categorias->update($id, ['activo' => 0]);
        }
        return redirect()->route('categorias');
    }

    public function reingresar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '12');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->categorias->update($id, ['activo' => 1]);
        }
        return redirect()->route('categorias');
    }
}
