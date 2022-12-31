<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{ClientesModel, DetalleRolesPermisosModel};

class Clientes extends BaseController
{
    protected $clientes, $detalleRoles, $reglas, $session;

    public function __construct()
    {
        $this->clientes = new ClientesModel();
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

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '20');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        echo view('header');

        if ($activo == 1) {
            $where = "activo = 1 AND id > 1";
            $clientes = $this->clientes->where($where)->findAll();
            $data = ['datos' => $clientes];
            echo view('clientes/clientes', $data);
        } else {
            $where = "activo = 0 AND id > 1";
            $clientes = $this->clientes->where($where)->findAll();
            $data = ['datos' => $clientes];
            echo view('clientes/eliminados', $data);
        }

        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '20');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == "post" && $this->validate($this->reglas)) {

            $this->clientes->save([
                'nombre' => $this->request->getPost('nombre'),
                'direccion' => $this->request->getPost('direccion'),
                'telefono' => $this->request->getPost('telefono'),
                'correo' => $this->request->getPost('correo')
            ]);
            return redirect()->route('clientes');
        } else {

            $data = ['validation' => $this->validator];

            echo view('header');
            echo view('clientes/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '20');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null && $id > 1) {
            $cliente = $this->clientes->where('id', $id)->first();
            $data = ['cliente' => $cliente, 'validation' => $this->validator];

            echo view('header');
            echo view('clientes/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('clientes');
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
                $this->clientes->update($id, [
                    'nombre' => $this->request->getPost('nombre'),
                    'direccion' => $this->request->getPost('direccion'),
                    'telefono' => $this->request->getPost('telefono'),
                    'correo' => $this->request->getPost('correo')
                ]);
            } else {
                return $this->editar($id);
            }
        }
        return redirect()->route('clientes');
    }

    public function eliminar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '20');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null && $id != 1) {
            $this->clientes->update($id, ['activo' => 0]);
        }
        return redirect()->route('clientes');
    }

    public function reingresar($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '20');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->clientes->update($id, ['activo' => 1]);
        }
        return redirect()->route('clientes');
    }

    public function autocompleteData()
    {
        $returnData = array();

        $valor = $this->request->getGet('term');
        $where = "activo = 1 AND id > 1 AND codigo = '%$valor%'";
        $clientes = $this->clientes->where($where)->findAll();
        if (!empty($clientes)) {
            foreach ($clientes as $row) {
                $data['id'] = $row['id'];
                $data['value'] = $row['nombre'];
                array_push($returnData, $data);
            }
        }

        echo json_encode($returnData);
    }

    public function buscarClienteVenta()
    {
        $tbody = '';
        if ($this->request->getMethod() == "post") {
            $valor = $this->request->getPost('valor');
            $where = "activo = 1 AND id > 1 AND nombre LIKE '%$valor%'";
            $clientes = $this->clientes->where($where)->findAll();
            if (!empty($clientes)) {
                foreach ($clientes as $row) {
                    $tbody .= '<tr>';
                    $tbody .= '<td>' . $row['id'] . '</td>';
                    $tbody .= '<td>' . $row['nombre'] . '</td>';
                    $tbody .= "<td><button class='btn btn-success btn-sm' id='add_cliente' rel='tooltip' data-placement='top' title='Asignar cliente' onclick='addCliente(" . $row['id'] . ", \"" . $row['nombre'] . "\");'><span class='fas fa-plus'></span></button></td>";
                    $tbody .= '</tr>';
                }
            }
        }
        echo json_encode($tbody);
    }
}
