<main>
    <div class="container-fluid">
        <h4 class="mt-4">Modificar categoría</h4>

        <form method="POST" action="<?php echo site_url('categorias/actualizar/' . $datos['id']); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>

            <input type="hidden" value="<?php echo $datos['id']; ?>" name="id" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Nombre</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo esc($datos['nombre']); ?>" autofocus required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Descripci&oacute;n</label>
                        <input class="form-control" id="descripcion" name="descripcion" type="text" value="<?php echo esc($datos['descripcion']); ?>" />
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