<main>
    <div class="container-fluid">
        <h4 class="mt-4">Ventas</h4>

        <div>
            <p>
                <a href="<?php echo site_url('ventas/baja'); ?>" class="btn btn-warning btn-sm">Eliminados</a>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Folio</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Cajero</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal -->
<div class="modal fade" id="modal-confirma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Eliminar registro</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>¿Desea eliminar este registro?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-danger btn-ok">Si</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-ticket" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ticket de venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div id="modal-body-ticket" class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var base_url = "<?php echo base_url(); ?>";
        var csrfName = '<?php echo csrf_token(); ?>';

        $('#dataTable').DataTable({
            "language": {
                "url": "<?php echo base_url(); ?>/assets/DatatablesSpanish.json"
            },
            "pageLength": 10,
            "serverSide": true,
            "order": [
                [0, "desc"]
            ],
            "ajax": {
                url: base_url + '/ventas/mostrarVentas',
                type: 'POST',
                data: {
                    activo: "1",
                    [csrfName]: '<?php echo csrf_hash(); ?>'
                }
            },
        });
    });

    function ver_ticket(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>/ventas/verTicket',
            method: "POST",
            data: {
                id_venta: id,
                <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(resultado) {
                $('#modal-body-ticket').html('<object class="PDFdoc" width="100%" height="500px" type="application/pdf" data="' + resultado + '"></object>');
                //$('#modal-body-ticket').html(resultado);
                $('#modal-ticket').modal({
                    show: true
                });
            }
        });
    }
</script>