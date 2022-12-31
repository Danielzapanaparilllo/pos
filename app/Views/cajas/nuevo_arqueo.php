<main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>

            <?php if (isset($validation)) { ?>
                <div class="alert alert-danger">
                    <?php echo $validation->listErrors(); ?>
                </div>
            <?php } ?>

            <form method="POST" action="<?php echo base_url(); ?>/cajas/nuevo_arqueo" autocomplete="off">

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Número de caja</label>
                            <input class="form-control" id="numero_caja" name="numero_caja" type="text" value="<?php echo $caja['numero_caja']; ?>" autofocus required />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Nombre</label>
                            <input class="form-control" id="nombre" name="nombre" type="text" value="<?php echo $session->nombre; ?>" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Monto inicial</label>
                            <input class="form-control" id="monto_inicial" name="monto_inicial" type="text" value="" required />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Folio inicial</label>
                            <input class="form-control" id="folio" name="folio" type="text" value="<?php echo $caja['folio']; ?>" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Fecha</label>
                            <input class="form-control" id="fecha" name="fecha" type="date" value="<?php echo date('Y-m-d'); ?>" required />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Hora</label>
                            <input class="form-control" id="hora" name="hora" type="time" value="<?php echo date('H:i:s'); ?>" required />
                        </div>
                    </div>
                </div>

                <a href="<?php echo base_url(); ?>/cajas" class="btn btn-primary">Regresar</a>
                <button type="submit" class="btn btn-success">Guardar</button>


            </form>

        </div>
    </main>