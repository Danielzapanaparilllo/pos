<main>
    <div class="container-fluid">
        <h4 class="mt-4">Agregar categoría</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger alert-dismissible fade show col-12 col-sm-6" role="alert">
                <?php echo $validation->listErrors(); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('categorias/nuevo'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo esc(set_value('nombre')); ?>" autofocus required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Descripci&oacute;n</label>
                        <input class="form-control" id="descripcion" name="descripcion" type="text" value="<?php echo esc(set_value('descripcion')); ?>" />
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

            <a href="<?php echo site_url('categorias'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>
    </div>
</main>