<main>
    <div class="container-fluid">
        <h4 class="mt-3">Configuración</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <a class="nav-link active" id="nav-ticket-tab" data-toggle="tab" href="#nav-ticket" role="tab" aria-controls="nav-ticket" aria-selected="true">Ticket</a>
                <!--<a class="nav-link" id="nav-moneda-tab" data-toggle="tab" href="#nav-moneda" role="tab" aria-controls="nav-moneda" aria-selected="false">Moneda</a>-->
            </div>
        </nav>

        <form method="POST" action="<?php echo site_url('configuracion/actualizaConfiguracion'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>
            <div class="tab-content" id="nav-tabContent">

                <!--Configuración de ticket -->
                <div class="tab-pane fade show active" id="nav-ticket" role="tabpanel" aria-labelledby="nav-ticket-tab">

                    <h5 class="mt-3">Personalización de ticket de venta</h5>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="tkt_direccion" name="tkt_direccion" <?php if ($config['tkt_direccion'] == 1) {
                                                                                                                                            echo 'checked';
                                                                                                                                        } ?>>
                                    <label class="form-check-label" for="tkt_direccion">
                                        Agregar dirección
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="tkt_telefono" name="tkt_telefono" <?php if ($config['tkt_telefono'] == 1) {
                                                                                                                                        echo 'checked';
                                                                                                                                    } ?>>
                                    <label class="form-check-label" for="tkt_telefono">
                                        Agregar teléfono
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="tkt_folio" name="tkt_folio" <?php if ($config['tkt_folio'] == 1) {
                                                                                                                                    echo 'checked';
                                                                                                                                } ?>>
                                    <label class="form-check-label" for="tkt_folio">
                                        Agregar folio de venta
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <label>Leyenda del ticket</label>
                                <textarea class="form-control form-control-sm" id="tkt_leyenda" name="tkt_leyenda" required><?php echo $config['tkt_leyenda']; ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Moneda -->
                <!--
                <div class="tab-pane fade" id="nav-moneda" role="tabpanel" aria-labelledby="nav-moneda-tab">

                    <h5 class="mt-3">Personalización de moneda</h5>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label>Simbolo de moneda</label>
                                <input type="text" class="form-control form-control-sm" id="moneda" name="moneda" maxlength="1" value="<?php //echo $config['conf_moneda']; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label>Separador de miles</label>
                                <input type="text" class="form-control form-control-sm" id="separa_miles" name="separa_miles" maxlength="1" value="<?php //echo $config['conf_separa_miles']; ?>" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label>Separador de decimales</label>
                                <input type="text" class="form-control form-control-sm" id="separa_decimales" name="separa_decimales" maxlength="1" value="<?php //echo $config['conf_separa_decimales']; ?>" />
                            </div>
                        </div>
                    </div>

                </div>-->
            </div>

            <hr>

            <button type="button" id="previa" class="btn btn-primary">Vista previa</button>
            <button type="submit" id="guardar" name="guardar" class="btn btn-success">Guardar</button>

        </form>

    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Vista previa de ticket de venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $(".nav-tabs a").click(function() {
            $(this).tab('show');
        });
        $('.nav-tabs a').on('shown.bs.tab', function(event) {
            var x = $(event.target).text();
            var y = $(event.relatedTarget).text();
            $(".act span").text(x);
            $(".prev span").text(y);
        });

        $('#previa').on('click', function() {

            let direccion = 0;
            let telefono = 0;
            let folio = 0;

            if ($('#tkt_direccion').prop('checked')) {
                direccion = 1;
            }

            if ($('#tkt_telefono').prop('checked')) {
                telefono = 1;
            }

            if ($('#tkt_folio').prop('checked')) {
                folio = 1;
            }

            $.ajax({
                url: '<?php echo base_url(); ?>/configuracion/previaTicket',
                method: "POST",
                data: {
                    leyenda: $('#tkt_leyenda').val(),
                    direccion: direccion,
                    telefono: telefono,
                    folio: folio,
                    /*moneda: $('#moneda').val(),
                    miles: $('#separa_miles').val(),
                    decimales: $('#separa_decimales').val(),*/
                    <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(resultado) {

                    $('.modal-body').html('<object class="PDFdoc" width="100%" height="500px" type="application/pdf" data="' + resultado + '"></object>');
                    $('#modal-confirma').modal({
                        show: true
                    });
                }
            });
        });
    });
</script>