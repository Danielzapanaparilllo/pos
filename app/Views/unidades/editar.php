<main>
    <div class="container-fluid">
        <h4 class="mt-4">Modificar unidad</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger alert-dismissible fade show col-12 col-sm-6" role="alert">
                <?php echo $validation->listErrors(); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('unidades/actualizar/'.$datos['id']); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <input type="hidden" value="<?php echo $datos['id']; ?>" id="id" name="id" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo esc($datos['nombre']); ?>" autofocus required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label> Nombre corto</label>
                        <input class="form-control" id="nombre_corto" name="nombre_corto" type="text" value="<?php echo esc($datos['nombre_corto']); ?>"  required/>
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

            <a href="<?php echo site_url('unidades'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>
    </div>
</main>