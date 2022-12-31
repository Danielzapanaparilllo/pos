<main>
    <div class="container-fluid">
        <h4 class="mt-4">Kardex</h4>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text"><span class="fas fa-barcode"></span></div>
                            </div>
                            <input class="form-control form-control-sm" id="codigo" name="codigo" type="text" placeholder="Escribe el código" autofocus />
                        </div>
                    </div>

                    <div class="col-12 col-sm-6">
                        <button type="button" class="btn btn-success" id="generar">Generar</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-12">
                <div class="panel">
                    <div class="embed-responsive embed-responsive-4by3" style="margin-top: 10px;">
                        <iframe class="embed-resposive-item" id="pdf"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="<?php echo base_url(); ?>/js/sweetalert.min.js"></script>

<script>
    $(function() {
        $("#codigo").autocomplete({
            source: "<?php echo base_url(); ?>/productos/autocompleteData",
            minLength: 3,
            select: function(event, ui) {
                event.preventDefault();
                $("#codigo").val(ui.item.value);
            }
        });

        $("#generar").click(function() {
            $.ajax({
                method: "POST",
                url: '<?php echo base_url(); ?>/inventario/genera_kardex',
                dataType: 'json',
                data: {
                    codigo: $("#codigo").val(),
                    <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(resultado) {
                    if (resultado != 0) {
                        if (resultado.existe) {
                            $('#pdf').attr('src', resultado.ruta);
                        } else {
                            swal({
                                title: resultado.error,
                                icon: 'warning',
                                timer: 2000
                            }).then((value) => {
                                $('#codigo').focus();
                            });
                        }
                    }
                }
            });
        });
    });
</script>