<main>
    <div class="container-fluid">
        <h4 class="mt-4">Agregar cliente</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('clientes/nuevo'); ?>" autocomplete="off">

        <?php echo csrf_field(); ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo set_value('nombre') ?>" autofocus required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Dirección</label>
                        <input class="form-control" id="direccion" name="direccion" type="text" value="<?php echo set_value('direccion') ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Teléfono</label>
                        <input class="form-control" id="telefono" name="telefono" type="text" value="<?php echo set_value('telefono') ?>" />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Correo</label>
                        <input class="form-control" id="correo" name="correo" type="text" value="<?php echo set_value('correo') ?>" />
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

            <a href="<?php echo site_url('clientes'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>

    </div>
</main>