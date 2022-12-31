<main>
    <div class="container-fluid">
        <h4 class="mt-3">Datos generales</h4>

        <?php if (isset($validation) && count($validation->getErrors()) > 0) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>

            </div>
        <?php } ?>

        <form method="POST" enctype="multipart/form-data" action="<?php echo site_url('configuracion/actualizar'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-7">
                        <label><i class="campo-obligatorio">*</i> Nombre del negocio o tienda</label>
                        <input class="form-control form-control-sm" id="tienda_nombre" name="tienda_nombre" type="text" value="<?php echo $nombre['valor']; ?>" autofocus required />
                    </div>

                    <div class="col-12 col-sm-5">
                        <label> Número teléfonico</label>
                        <input class="form-control form-control-sm" id="tienda_telefono" name="tienda_telefono" type="text" value="<?php echo $telefono['valor']; ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-7">
                        <label>Dirección del negocio o tienda</label>
                        <input class="form-control form-control-sm" id="tienda_direccion" name="tienda_direccion" value="<?php echo $direccion['valor']; ?>" />
                    </div>

                    <div class="col-12 col-sm-5">
                        <label>Correo electrónico</label>
                        <input class="form-control form-control-sm" id="tienda_email" name="tienda_email" type="text" value="<?php echo $email['valor']; ?>" />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <label for="tienda_logo"><i class="campo-obligatorio">*</i> Logotipo</label><br />
                        <img id="load_img" class="img-responsive" src="<?php echo base_url() . '/images/logotipo.png?n=' . time(); ?>" width="150" /> <br />
                        <input class="form-control form-control-sm" type="file" id="tienda_logo" name="tienda_logo" accept="image/png" onChange='upload_image();'>
                        <p class="text-secondary">Cargar imagen en formato png de 150x150 píxeles</p>
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

            <button type="submit" class="btn btn-success">Guardar</button>
        </form>
    </div>
</main>

<script type="text/javascript">
    $(document).on("keypress", 'form', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

    function upload_image() {

        var inputFileImage = document.getElementById("tienda_logo");
        var file = inputFileImage.files[0];
        if ((typeof file === "object") && (file !== null)) {

            var data = new FormData();
            data.append('tienda_logo', file);
            data.append('<?php echo csrf_token(); ?>', '<?php echo csrf_hash(); ?>');

            $.ajax({
                url: '<?php echo site_url('configuracion/subirLogo'); ?>',
                type: "POST",
                data: data,
                contentType: false,
                cache: false,
                processData: false,
                success: function(response) {
                    $("input[name*='<?php echo csrf_token(); ?>']").val(response.token);

                    if (response.success == 1) {
                        $("#load_img").attr("src", "");
                        $("#load_img").attr("src", "<?php echo base_url() . '/images/logo_tmp.png'; ?>");
                    } else {
                        $('#modalito').modal('show')
                    }
                }
            });
        }
    }
</script>