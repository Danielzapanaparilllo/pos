<footer class="py-3 bg-light mt-auto">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">Desarrollado por Códigos de Programación.</div>
        </div>
    </div>
</footer>
</div>
</div>

<script src="<?php echo base_url(); ?>/js/all.min.js"></script>
<script src="<?php echo base_url(); ?>/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo base_url(); ?>/js/scripts.js"></script>
<script src="<?php echo base_url(); ?>/js/jquery.dataTables.min.js"></script>
<script src="<?php echo base_url(); ?>/js/dataTables.bootstrap4.min.js"></script>

<script>
    $('#modal-confirma').on('show.bs.modal', function(e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));
    });

    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#dataTable')) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "<?php echo base_url(); ?>/assets/DatatablesSpanish.json"
                },
                "order": []
            });
        }

        $("body").tooltip({
            selector: '[rel=tooltip]'
        });
    });
</script>

</body>

</html>