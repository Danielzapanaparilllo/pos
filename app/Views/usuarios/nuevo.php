<main>
    <div class="container-fluid">
        <h4 class="mt-4">Nuevo usuario</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('usuarios/nuevo'); ?>" autocomplete="off">
        <?php echo csrf_field(); ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo set_value('nombre') ?>" required autofocus />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Usuario</label>
                        <input class="form-control" id="usuario" name="usuario" type="text" value="<?php echo set_value('usuario') ?>" required />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Contraseña</label>
                        <input class="form-control" id="password" name="password" type="password" value="<?php echo set_value('password') ?>" required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Repite contraseña</label>
                        <input class="form-control" id="repassword" name="repassword" type="password" value="<?php echo set_value('repassword') ?>" required />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Caja</label>
                        <select class="form-control" id="id_caja" name="id_caja" required>
                            <option value="">Seleccionar caja</option>
                            <?php foreach ($cajas as $caja) { ?>
                                <option value="<?php echo $caja['id']; ?>"><?php echo $caja['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Rol</label>
                        <select class="form-control" id="id_rol" name="id_rol" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $rol) { ?>
                                <option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <i class="campo-obligatorio">( * ) Campo obligatorio</i>
                    </div>
                </div>
            </div>

            <a href="<?php echo site_url('usuarios'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>
        </form>

    </div>
</main>