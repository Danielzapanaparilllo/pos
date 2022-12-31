<?php

$user_session = session();

$configModel = new \App\Models\ConfiguracionModel();
$rolesModel = new \App\Models\DetalleRolesPermisosModel();
$nombre_sistema = $configModel->getConfig('tienda_nombre');

$permisosAsignados = $rolesModel->where('id_rol', $user_session->id_rol)->findAll();
$datosRol = array();
foreach ($permisosAsignados as $permisoAsignado) {
    $datosRol[$permisoAsignado['id_permiso']] = true;
}

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title><?php echo $nombre_sistema; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Sistema punto de venta CDP ver. 3" />
    <meta name="author" content="Marko Robles - CDP" />
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>/images/favicon.png" />
    <link href="<?php echo base_url(); ?>/css/styles.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/css/dataTables.bootstrap4.min.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>/js/jquery-ui/jquery-ui.min.css" rel="stylesheet" />

    <script src="<?php echo base_url(); ?>/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url(); ?>/js/jquery-ui/jquery-ui.min.js"></script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="<?php echo site_url('inicio'); ?>"><?php echo $nombre_sistema; ?></a>
        <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>

        <!-- Navbar-->
        <ul class="navbar-nav ml-auto mr-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="fas fa-user-circle fa-fw"></i> <?php echo $user_session->nombre; ?></a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">

                    <a class="dropdown-item" href="<?php echo site_url('usuarios/perfil'); ?>"><i class="fas fa-user"></i> Perfil</a>
                    <a class="dropdown-item" href="<?php echo site_url('usuarios/cambia_password'); ?>"><i class="fas fa-key"></i> Cambiar contraseña</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="<?php echo site_url('usuarios/logout'); ?>"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
                </div>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <?php if (isset($datosRol[1])) { ?>
                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-basket"></i></div>
                                Productos
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php if (isset($datosRol[2])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('productos'); ?>">Productos</a>
                                    <?php }
                                    if (isset($datosRol[7])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('unidades'); ?>">Unidades</a>
                                    <?php }
                                    if (isset($datosRol[12])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('categorias'); ?>">Categorías</a>
                                    <?php } ?>
                                </nav>
                            </div>
                        <?php } ?>

                        <?php if (isset($datosRol[17])) { ?>
                            <a class="nav-link" href="<?php echo site_url('inventario'); ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-truck"></i></div>Inventario
                            </a>

                        <?php }
                        if (isset($datosRol[20])) { ?>

                            <a class="nav-link" href="<?php echo site_url('clientes'); ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>Clientes
                            </a>
                        <?php }
                        if (isset($datosRol[25])) { ?>
                            <a class="nav-link" href="<?php echo site_url('ventas/venta'); ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-cash-register"></i></div>Caja
                            </a>

                        <?php }
                        if (isset($datosRol[26])) { ?>

                            <a class="nav-link" href="<?php echo site_url('ventas'); ?>">
                                <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>Ventas
                            </a>

                        <?php }
                        if (isset($datosRol[28])) { ?>

                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#menuReportes" aria-expanded="false" aria-controls="menuReportes">
                                <div class="sb-nav-link-icon"><i class="fas fa-list"></i></div>
                                Reportes
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="menuReportes" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="<?php echo site_url('reportes/detalle_reporte_venta'); ?>">Reporte de ventas</a>
                                    <a class="nav-link" href="<?php echo site_url('productos/mostrarMinimos'); ?>">Reporte mínimos</a>
                                </nav>
                            </div>

                        <?php }
                        if (isset($datosRol[29])) { ?>

                            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#subAdministracion" aria-expanded="false" aria-controls="subAdministracion">
                                <div class="sb-nav-link-icon"><i class="fas fa-tools"></i></div>
                                Administración
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="subAdministracion" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <?php if (isset($datosRol[30])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('configuracion/datos'); ?>">Datos generales</a>
                                    <?php }
                                    if (isset($datosRol[31])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('configuracion'); ?>">Configuración</a>
                                    <?php }
                                    if (isset($datosRol[32])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('usuarios'); ?>">Usuarios</a>
                                    <?php }
                                    if (isset($datosRol[38])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('roles'); ?>">Roles</a>
                                    <?php }
                                    if (isset($datosRol[44])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('cajas'); ?>">Cajas</a>
                                    <?php }
                                    if (isset($datosRol[49])) { ?>
                                        <a class="nav-link" href="<?php echo site_url('configuracion/logs'); ?>">Logs de acceso</a>
                                    <?php } ?>
                                </nav>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">