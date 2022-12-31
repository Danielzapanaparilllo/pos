<main>
    <div class="container-fluid">
        <h4 class="mt-4">Modificar usuario</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('usuarios/actualizar'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" value="<?php echo $datos['id']; ?>" name="id" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $datos['nombre']; ?>" required autofocus />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Usuario</label>
                        <input class="form-control" id="usuario" name="usuario" type="text" value="<?php echo $datos['usuario']; ?>" required />
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
                                <option value="<?php echo $caja['id']; ?>" <?php if($caja['id'] == $datos['id_caja']) { echo 'selected'; } ?>><?php echo $caja['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Rol</label>
                        <select class="form-control" id="id_rol" name="id_rol" required>
                            <option value="">Seleccionar rol</option>
                            <?php foreach ($roles as $rol) { ?>
                                <option value="<?php echo $rol['id']; ?>" <?php if($rol['id'] == $datos['id_rol']) { echo 'selected'; } ?>><?php echo $rol['nombre']; ?></option>
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