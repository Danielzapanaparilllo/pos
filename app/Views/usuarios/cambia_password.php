<main>
    <div class="container-fluid">
        <h4 class="mt-4">Modificar contraseña</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('usuarios/actualizar_password'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input id="id_usuario" name="id_usuario" type="hidden" value="<?php echo $usuario['id']; ?>" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Usuario</label>

                        <input class="form-control" id="usuario" name="usuario" type="text" value="<?php echo $usuario['usuario']; ?>" disabled />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $usuario['nombre']; ?>" disabled />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Contraseña</label>
                        <input class="form-control" id="password" name="password" type="password" required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Confirma contraseña</label>
                        <input class="form-control" id="repassword" name="repassword" type="password" required />
                    </div>
                </div>
            </div>

            <a href="<?php echo site_url('usuarios'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

            <br />
            <br />

            <?php if (isset($mensaje)) { ?>
                <div class="alert alert-success col-12 col-sm-6">
                    <?php echo $mensaje; ?>
                </div>
            <?php } ?>

        </form>

    </div>
</main>