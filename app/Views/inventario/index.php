<main>
    <div class="container-fluid">
        <h4 class="mt-4">Inventario</h4>

        <div>
            <p>
                <a href="<?php echo site_url('inventario/nuevo'); ?>" class="btn btn-success btn-sm"><span class="fas fa-plus"></span>&nbsp; Agregar</a>
                <a href="<?php echo site_url('inventario/ajuste'); ?>" class="btn btn-warning btn-sm"><span class="fas fa-edit"></span>&nbsp; Ajustes</a>
                <a href="<?php echo site_url('inventario/detalle_kardex'); ?>" class="btn btn-primary btn-sm"><span class="fas fa-file-alt"></span>&nbsp; Kardex</a>
                <a href="<?php echo site_url('productos/mostrarMinimos'); ?>" class="btn btn-info btn-sm"><span class="fas fa-file-alt"></span>&nbsp; Productos bajos de inventario</a>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripci&oacute;n</th>
                        <th>Movimiento</th>
                        <th>Cantidad</th>
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
                <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
                <a class="btn btn-danger btn-ok">Si</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(e) {
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
                url: base_url + '/inventario/mostrarMovimientos',
                type: 'POST',
                data: {
                    [csrfName]: '<?php echo csrf_hash(); ?>'
                }
            },
        });
    });

    $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip()
    });
</script>