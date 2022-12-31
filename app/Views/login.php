<!--
	Copyright (c) 2021 Codigos de Programacion
	Punto de Venta CDP ver. 3.0
	Desarrollado por Codigos de Programacion
	www.codigosdeprogramacion.com
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="author" content="Marko Robles" />
    <title>Punto de Venta CDP</title>
    <link rel="icon" href="<?php echo base_url(); ?>/images/favicon.png" sizes="32x32" />
    <link href="<?php echo base_url(); ?>/css/styles.css" rel="stylesheet" />

    <style>
        body {
            background-image: url("<?php echo base_url(); ?>/images/background_pos.jpg");
            height: 100%;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
    </style>
</head>

<body>
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4"><img src="<?php echo base_url(); ?>/images/favicon.png" width="48">&nbsp; Punto de Venta CDP</h3>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="<?php echo base_url(); ?>/usuarios/login" autocomplete="off">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group">
                                            <label class="mb-1" for="usuario">Usuario</label>
                                            <input class="form-control py-4" id="usuario" name="usuario" type="text" placeholder="Ingresa tu usuario" required autofocus />
                                        </div>
                                        <div class="form-group">
                                            <label class="mb-1" for="password">Contraseña</label>
                                            <input class="form-control py-4" id="password" name="password" type="password" placeholder="Ingresa tu contraseña" required />
                                        </div>

                                        <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <button class="btn btn-primary" type="submit">Ingresar</button>
                                        </div>
                                    </form>

                                    <br />

                                    <?php if (isset($validation)) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo $validation->listErrors(); ?>
                                        </div>
                                    <?php } ?>

                                    <?php if (isset($error)) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo $error; ?>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <b class="text-right">Ver. 3.0 &nbsp; </b>
        <div id="layoutAuthentication_footer">
            <footer class="py-3 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Códigos de Programación <?php echo date('Y'); ?></div>
                    </div>
                    <div>
                        <a href="https://facebook.com/codigosprogramacion" target="_blank">Facebook</a>
                        &middot;
                        <a href="https://codigosdeprogramacion.com" target="_blank">Sitio Web</a>
                    </div>
                </div>
        </div>
        </footer>
    </div>
    <script src="<?php echo base_url(); ?>/js/jquery-3.6.0.min.js"></script>
    <script src="<?php echo base_url(); ?>/js/bootstrap.bundle.min.js"></script>
</body>

</html>