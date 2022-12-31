<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{DetalleRolesPermisosModel, PermisosModel, RolesModel};

class Roles extends BaseController
{
    protected $roles, $permisos, $detalleRoles, $session, $reglas;

    public function __construct()
    {
        $this->session = session();
        $this->roles = new RolesModel();
        $this->permisos = new PermisosModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();

        helper(['form']);

        $this->reglas = [
            'nombre' => ['label' => 'nombre', 'rules' => 'required']
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $roles = $this->roles->where('activo', $activo)->findAll();
        $data = ['datos' => $roles];
        echo view('header');

        if ($activo == 1) {
            echo view('roles/roles', $data);
        } else {
            echo view('roles/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {
            $this->roles->insert(['nombre' => trim($this->request->getPost('nombre'))]);
            return redirect()->route('roles');
        } else {
            $data = ['validation' => $this->validator];

            echo view('header');
            echo view('roles/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $rol = $this->roles->where('id', $id)->first();
            $data = ['datos' => $rol, 'validation' => $this->validator];

            echo view('header');
            echo view('roles/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('roles');
        }
    }

    public function actualizar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post") {
            $id = $this->request->getPost('id');
            if ($this->validate($this->reglas)) {
                $this->roles->update($id, ['nombre' => trim($this->request->getPost('nombre'))]);
                return redirect()->route('roles');
            } else {
                return $this->editar($id);
            }
        }
    }

    public function eliminar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }
        if ($id != null) {
            $this->roles->update($id, ['activo' => 0]);
        }
        return redirect()->route('roles');
    }

    public function reingresar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->roles->update($id, ['activo' => 1]);
        }
        return redirect()->route('roles');
    }

    public function detalles($idRol)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '38');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $permisosMenu = $this->permisos->where('depende', 0)->findAll();
        $permisosAsignados = $this->detalleRoles->where('id_rol', $idRol)->findAll();
        $datos = array();

        foreach ($permisosAsignados as $permisoAsignado) {
            $datos[$permisoAsignado['id_permiso']] = true;
        }

        $data = ['titulo' => 'Asignar permisos', 'permisosMenu' => $permisosMenu, 'id_rol' => $idRol, 'asignado' =>  $datos];

        echo view('header');
        echo view('roles/detalles', $data);
        echo view('footer');
    }

    public function guardaPermisos()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post") {

            $idRol = $this->request->getPost('id_rol');
            $permisos = $this->request->getPost('permisos');

            $this->detalleRoles->where('id_rol', $idRol)->delete();

            if ($permisos != null) {
                foreach ($permisos as $permiso) {
                    $this->detalleRoles->save(['id_rol' => $idRol, 'id_permiso' => $permiso]);
                }
            }

            return redirect()->to(base_url() . "/roles");
        }
    }
}
