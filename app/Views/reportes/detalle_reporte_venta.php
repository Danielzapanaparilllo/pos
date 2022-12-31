<main>
    <div class="container-fluid">
        <h4 class="mt-4">Genera reporte de ventas</h4>

        <form method="POST" action="<?php echo site_url('reportes/reporte_ventas'); ?>" enctype="multipart/form-data" autocomplete="off">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Fecha de inicio:</label>
                        <input type='date' id="fecha_inicio" name="fecha_inicio" class="form-control form-control-sm" required />
                    </div>

                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Fecha de fin:</label>
                        <input type='date' id="fecha_fin" name="fecha_fin" class="form-control form-control-sm" required />
                    </div>

                    <div class="col-12 col-sm-4">
                        <label><i class="campo-obligatorio">*</i> Caja</label>
                        <select class="form-control form-control-sm" id="caja" name="caja" required>
                            <?php if ($idRol == 1) { ?>
                                <option value="0">Todas</option>
                            <?php
                            }
                            foreach ($cajas as $caja) {
                            ?>
                                <option value="<?php echo $caja['id']; ?>"><?php echo $caja['nombre']; ?></option>
                            <?php } ?>
                        </select>
                    </div>


                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <i class="campo-obligatorio">( * ) Campos obligatorios</i>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-success">Generar reporte</button>

        </form>
    </div>
</main>