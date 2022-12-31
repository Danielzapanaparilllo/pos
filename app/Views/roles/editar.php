<main>
    <div class="container-fluid">
        <h4 class="mt-4">Modificar rol</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('roles/actualizar'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" value="<?php echo $datos['id']; ?>" name="id" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $datos['nombre']; ?>" autofocus required />
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

            <a href="<?php echo base_url(); ?>/roles" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>
    </div>
</main>