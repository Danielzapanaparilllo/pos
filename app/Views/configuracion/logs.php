<main>
    <div class="container-fluid">
        <h4 class="mt-4">Logs de acceso</h4>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm display" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Usuarios</th>
                        <th>IP</th>
                        <th>Evento</th>
                        <th>Detalles</th>
                        <th>Fecha y hora</th>
                    </tr>
                </thead>

                <tbody>
 
                </tbody>
            </table>
        </div>
    </div>
</main>

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
                [4, "desc"]
            ],
            "ajax": {
                url: base_url + '/configuracion/mostrarLogs',
                type: 'POST',
                data: {
                    [csrfName]: '<?php echo csrf_hash(); ?>'
                }
            },
        });
    });
</script>