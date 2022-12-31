<main>
    <div class="container-fluid">
        <h4 class="mt-3">Modificar producto</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger alert-dismissible fade show col-12 col-sm-6" role="alert">
                <?php echo $validation->listErrors(); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <form method="POST" action="<?php echo site_url('productos/actualizar'); ?>" enctype="multipart/form-data" autocomplete="off">
            <?php echo csrf_field(); ?>

            <input type="hidden" id="id" name="id" value="<?php echo $producto['id']; ?>" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Código de barras</label>
                        <input class="form-control form-control-sm" id="codigo" name="codigo" type="text" value="<?php echo $producto['codigo']; ?>" autofocus required />
                        <div id="validacionCodigo" class="invalid-feedback">
                            El c&oacute;digo ya existe.
                        </div>
                    </div>

                    <div class="col-12 col-sm-8">
                        <label><i class="campo-obligatorio">*</i> Descripción</label>
                        <input class="form-control form-control-sm" id="nombre" name="nombre" type="text" value="<?php echo $producto['nombre']; ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Se vende por</label>
                        <select class="form-control form-control-sm" id="tipo_venta" name="tipo_venta" required>
                            <option value="P" <?php if ($producto['tipo_venta'] == 'P') echo 'selected'; ?>>Unidad / Pieza</option>
                            <option value="G" <?php if ($producto['tipo_venta'] == 'G') echo 'selected'; ?>>Granel (Con decimales)</option>
                        </select>
                    </div>

                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Unidad de medida</label>
                        <select class="form-control form-control-sm" id="id_unidad" name="id_unidad" required>
                            <option value="">Seleccionar unidad</option>
                            <?php foreach ($unidades as $unidad) { ?>
                                <option value="<?php echo $unidad['id']; ?>" <?php if ($unidad['id'] == $producto['id_unidad']) echo 'selected'; ?>><?php echo $unidad['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Categor&iacute;a o departamento</label>
                        <select class="form-control form-control-sm" id="id_categoria" name="id_categoria" required>
                            <option value="0">- Sin categoría -</option>
                            <?php foreach ($categorias as $categoria) { ?>
                                <option value="<?php echo $categoria['id']; ?>" <?php if ($categoria['id'] == $producto['id_categoria']) echo 'selected'; ?>><?php echo $categoria['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label><i class="campo-obligatorio">*</i> Precio venta</label>
                        <input class="form-control form-control-sm" id="precio_venta" name="precio_venta" type="text" value="<?php echo $producto['precio_venta']; ?>" required />
                    </div>

                    <div class="col-12 col-sm-6">
                        <label>Precio costo</label>
                        <input class="form-control form-control-sm" id="precio_compra" name="precio_compra" type="text" value="<?php echo $producto['precio_compra']; ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label>Imagen</label><br />

                        <?php
                        $ruta_imagen = 'images/productos/' . $producto['id'] . '.jpg';
                        if (file_exists($ruta_imagen)) {
                            echo '<img src="' . base_url() . '/' . $ruta_imagen . '" class="img-responsive" width="200" />';
                        }
                        ?>

                        <input type="file" id="img_producto" class="form-control form-control-sm" name="img_producto" accept=".jpg, .jpeg" />
                        <p class="text-danger">Imagen en formato jpg de 150x150 pixeles</p>
                    </div>
                </div>
            </div>

            <h5 class="mt-3">Inventario</h5>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-12">
                        <div class="col-12 col-sm-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="inventariable" name="inventariable" value="1" <?php if ($producto['inventariable'] == 1) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?>>
                                <label class="form-check-label" for="inventariable">
                                    Marcar si utiliza inventario
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-12">
                        <div class="col-12 col-sm-4">
                            <label>Existencias actuales</label>
                            <input class="form-control form-control-sm" id="existencias" name="existencias" type="text" value="<?php echo $producto['existencias']; ?>" rel='tooltip' data-placement='top' title='Para modificar las existencias actuales ve a Menú Inventario -> Ajustes' readonly />
                        </div>
                    </div>

                    <div class="col-12 col-sm-12">
                        <div class="col-12 col-sm-4">
                            <label>Stock mínimo</label>
                            <input class="form-control form-control-sm" id="stock_minimo" name="stock_minimo" type="text" value="<?php echo $producto['stock_minimo']; ?>" <?php if ($producto['inventariable'] == 0) {
                                                                                                                                                                                echo 'readonly';
                                                                                                                                                                            } ?> />
                        </div>
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

            <a href="<?php echo site_url('productos'); ?>" class="btn btn-primary">Regresar</a>
            <button type="submit" class="btn btn-success">Guardar</button>

        </form>

    </div>
</main>

<script>
    $(document).on("keypress", 'form', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

    function validateDecimal(valor) {
        var RE = /^\d*\.?\d*$/;
        if (RE.test(valor)) {
            return true;
        } else {
            return false;
        }
    }

    $(document).ready(function() {
        $('#inventariable').change(function() {
            if (this.checked) {
                $("#stock_minimo").prop('readonly', false);
            } else {
                $("#stock_minimo").prop('readonly', true);
            }
            $("#stock_minimo").val(0);
        });
    });
</script>