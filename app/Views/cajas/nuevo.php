<main>
    <div class="container-fluid">
        <h4 class="mt-4">Nueva caja</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('cajas/nuevo'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo set_value('nombre') ?>" required autofocus />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Folio</label>
                        <input class="form-control" id="folio" name="folio" type="text" value="<?php echo set_value('folio') ?>" required />
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

            <a href="<?php echo site_url('cajas'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>

    </div>
</main>