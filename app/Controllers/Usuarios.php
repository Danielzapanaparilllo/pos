<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\{CajasModel, DetalleRolesPermisosModel, LogsModel, RolesModel, UsuariosModel};

class Usuarios extends BaseController
{
    protected $detalleRoles, $usuarios, $cajas, $roles, $logs, $session, $reglas, $reglasMod, $reglasLogin, $reglasCambia;

    public function __construct()
    {
        $this->usuarios = new UsuariosModel();
        $this->cajas = new CajasModel();
        $this->detalleRoles = new DetalleRolesPermisosModel();
        $this->roles = new RolesModel();
        $this->logs = new LogsModel();
        $this->session = session();

        helper(['form']);

        $this->reglas = [
            'usuario' => [
                'rules' => 'required|is_unique[usuarios.usuario]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser unico.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'repassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ],
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'id_caja' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'id_rol' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];

        $this->reglasMod = [
            'usuario' => [
                'rules' => 'required|is_unique[usuarios.usuario,id,{id}]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'is_unique' => 'El campo {field} debe ser unico.'
                ]
            ],
            'nombre' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'id_caja' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'id_rol' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];

        $this->reglasLogin = [
            'usuario' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ]
        ];

        $this->reglasCambia = [
            'password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.'
                ]
            ],
            'repassword' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'El campo {field} es obligatorio.',
                    'matches' => 'Las contraseñas no coinciden.'
                ]
            ]
        ];
    }

    public function index($activo = 1)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '32');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        $usuarios = $this->usuarios->getUsuarios($activo);
        $data = ['datos' => $usuarios];
        echo view('header');

        if ($activo == 1) {
            echo view('usuarios/usuarios', $data);
        } else {
            echo view('usuarios/eliminados', $data);
        }
        echo view('footer');
    }

    public function nuevo()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '32');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($this->request->getMethod() == 'post' && $this->validate($this->reglas)) {

            $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            $this->usuarios->save([
                'usuario' => $this->request->getPost('usuario'),
                'password' => $hash,
                'nombre' => $this->request->getPost('nombre'),
                'id_caja' => $this->request->getPost('id_caja'),
                'id_rol' => $this->request->getPost('id_rol'),
                'activo' => 1
            ]);
            return redirect()->route('usuarios');
        } else {
            $cajas = $this->cajas->where('activo', 1)->findAll();
            $roles = $this->roles->where('activo', 1)->findAll();
            $data = ['cajas' => $cajas, 'roles' => $roles, 'validation' => $this->validator];

            echo view('header');
            echo view('usuarios/nuevo', $data);
            echo view('footer');
        }
    }

    public function editar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '32');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $cajas = $this->cajas->where('activo', 1)->findAll();
            $roles = $this->roles->where('activo', 1)->findAll();
            $usuario = $this->usuarios->where('id', $id)->first();
            $data = ['cajas' => $cajas, 'roles' => $roles, 'datos' => $usuario, 'validation' => $this->validator];

            echo view('header');
            echo view('usuarios/editar', $data);
            echo view('footer');
        } else {
            return redirect()->route('usuarios');
        }
    }

    public function actualizar()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post") {
            $id = $this->request->getPost('id');
            if ($this->validate($this->reglasMod)) {
                $this->usuarios->update($this->request->getPost('id'), ['nombre' => $this->request->getPost('nombre'), 'usuario' => $this->request->getPost('usuario'), 'id_caja' => $this->request->getPost('id_caja'), 'id_rol' => $this->request->getPost('id_rol')]);
                return redirect()->to(base_url() . '/usuarios');
            } else {
                return $this->editar($id);
            }
        }

        return redirect()->route('usuarios');
    }

    public function eliminar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '32');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->usuarios->update($id, ['activo' => 0]);
        }

        return redirect()->route('usuarios');
    }

    public function reingresar($id)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $permiso = $this->detalleRoles->verificaPermisos($this->session->id_rol, '32');
        if (!$permiso) {
            echo view('header');
            echo view('403');
            echo view('footer');
            exit;
        }

        if ($id != null) {
            $this->usuarios->update($id, ['activo' => 1]);
        }

        return redirect()->route('usuarios');
    }

    public function login()
    {
        if ($this->request->getMethod() == "post" && $this->validate($this->reglasLogin)) {
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');
            $query = $this->usuarios->getWhere(['usuario' => $usuario, 'activo' => 1]);
            $num_rows = $query->getNumRows();

            if ($num_rows > 0) {
                $row = $query->getFirstRow();
                if (password_verify($password, $row->password)) {
                    $datosSesion = [
                        'login' => 1,
                        'id_usuario' => $row->id,
                        'nombre' => $row->nombre,
                        'id_caja' => $row->id_caja,
                        'id_rol' => $row->id_rol
                    ];

                    $ip = $_SERVER['REMOTE_ADDR'];
                    $detalles = $_SERVER['HTTP_USER_AGENT'];

                    $this->logs->save([
                        'id_usuario' => $row->id,
                        'evento' => 'Inicio de sesión',
                        'ip' => $ip,
                        'detalles' => $detalles
                    ]);

                    $this->session->set($datosSesion);
                    return redirect()->to(base_url() . '/inicio');
                } else {
                    $this->session->destroy();
                    $data['error'] = "La contraseña no coinciden";
                    echo view('login', $data);
                }
            } else {
                $this->session->destroy();
                $data['error'] = "El usuario no existe";
                echo view('login', $data);
            }
        } else {
            $data = ['validation' => $this->validator];
            echo view('login', $data);
        }
    }

    public function logout()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $detalles = $_SERVER['HTTP_USER_AGENT'];

        if (isset($this->session->id_usuario)) {

            $this->logs->save([
                'id_usuario' => $this->session->id_usuario,
                'evento' => 'Cierre de sesión',
                'ip' => $ip,
                'detalles' => $detalles
            ]);
        }

        $this->session->destroy();
        return redirect()->to(base_url());
    }

    public function cambia_password($id = null)
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($id != null) {
            $usuario = $this->usuarios->where('id', $id)->first();
        } else {
            $usuario = $this->usuarios->where('id', $this->session->id_usuario)->first();
        }

        $data = ['usuario' => $usuario];

        echo view('header');
        echo view('usuarios/cambia_password', $data);
        echo view('footer');
    }

    public function actualizar_password()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        if ($this->request->getMethod() == "post") {
            $idUsuario = $this->request->getPost('id_usuario');

            if ($this->validate($this->reglasCambia)) {

                if ($this->session->id_usuario != $idUsuario) {
                    if ($this->session->id_rol != 1) {
                        return redirect()->route('usuarios');
                    }
                }

                $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
                $this->usuarios->update($idUsuario, ['password' => $hash]);

                $usuario = $this->usuarios->where('id', $idUsuario)->first();
                $data = ['usuario' => $usuario, 'mensaje' => 'Contraseña actualizada'];

                echo view('header');
                echo view('usuarios/cambia_password', $data);
                echo view('footer');
            } else {

                $usuario = $this->usuarios->where('id', $idUsuario)->first();
                $data = ['usuario' => $usuario, 'validation' => $this->validator];

                echo view('header');
                echo view('usuarios/cambia_password', $data);
                echo view('footer');
            }
        } else {
            return redirect()->route('usuarios');
        }
    }

    //Cargar vista perfil
    public function perfil()
    {
        if (!isset($this->session->id_usuario)) {
            return redirect()->to(base_url());
        }

        $id = $this->session->id_usuario;

        $usuario = $this->usuarios->getUsuario($id);
        $data = ['usuario' => $usuario];

        echo view('header');
        echo view("usuarios/perfil", $data);
        echo view('footer');
    }
}
