<main>
    <div class="container-fluid">
        <h4 class="mt-4">Productos</h4>

        <div>
            <p>
                <a href="<?php echo site_url('productos/nuevo'); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Agregar</a>
                <a href="<?php echo site_url('productos/baja'); ?>" class="btn btn-warning btn-sm">Historial</a>
                <a href="<?php echo site_url('productos/muestraCodigos'); ?>" class="btn btn-info btn-sm">Códigos de barras</a>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm display" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Existencias</th>
                        <th>Tipo venta</th>
                        <th width="5%"></th>
                        <th width="5%"></th>
                        <th width="5%"></th>
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
                [0, "asc"]
            ],
            "ajax": {
                url: base_url + '/productos/mostrarProductos',
                type: 'POST',
                data: {
                    activo: "1",
                    [csrfName]: '<?php echo csrf_hash(); ?>'
                }
            },
        });
    });
</script>