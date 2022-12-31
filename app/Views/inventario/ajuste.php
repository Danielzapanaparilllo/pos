<?php $id_compra = uniqid(); ?>

<main>
    <div class="container-fluid">

        <h4 class="mt-3">Ajustar inventario</h4>

        <?php if (isset($validation)) { ?>
            <div class="alert alert-danger col-12 col-sm-6">
                <?php echo $validation->listErrors(); ?>
            </div>
        <?php } ?>

        <form method="POST" id="form_compra" name="form_compra" action="<?php echo site_url('inventario/ajuste'); ?>" autocomplete="off">
            <?php echo csrf_field(); ?>

            <input id="id_producto" name="id_producto" type="hidden" />

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <label for="codigo">Código</label>
                        <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Escribe el código y enter" onkeyup="buscarProducto(event, this, this.value)" required autofocus />

                        <div id="validacionCodigo" class="invalid-feedback">
                            No existe el código o producto.
                        </div>
                    </div>

                    <div class="col-12 col-sm-8">
                        <label>Nombre del producto</label>
                        <input class="form-control" id="nombre" name="nombre" type="text" disabled />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-12 col-sm-4">
                        <label>Existencia</label>
                        <input class="form-control" id="existencias" name="existencias" type="text" disabled />
                    </div>

                    <div class="col-12 col-sm-4">
                        <label>Ajuste + / -</label>
                        <input class="form-control" id="cantidad" name="cantidad" type="text" pattern="^([\-]?[0-9]*[\.]?[0-9]+)$" onkeyup="calcularCantidad(existencias.value, this.value)" required>
                    </div>

                    <div class="col-12 col-sm-4">
                        <label>Nueva existencia</label>
                        <input class="form-control" id="nueva_existencia" name="nueva_existencia" type="text" disabled />
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-12 col-sm-4">
                    <a href="<?php echo site_url('inventario'); ?>" class="btn btn-primary">Regresar</a>
                    <button id="ajuste" name="ajuste" type="submit" class="btn btn-success">Realizar ajuste</button>
                </div>
            </div>
        </form>
    </div>
</main>

<script src="<?php echo base_url(); ?>/js/sweetalert.min.js"></script>

<script>
    $(document).on("keypress", 'form', function(e) {
        var code = e.keyCode || e.which;
        if (code == 13) {
            e.preventDefault();
            return false;
        }
    });

    function calcularCantidad(existencias, cantidad) {

        if (isNaN(cantidad) || cantidad == '') {
            cantidad = 0;
        }

        if (isNaN(existencias) || existencias == '') {
            existencias = 0;
        }

        if (Number.isInteger(existencias)) {
            var resultado = parseInt(existencias) + parseInt(cantidad);
        } else {
            var resultado = parseFloat(existencias) + parseFloat(cantidad);
        }

        document.getElementById('nueva_existencia').value = resultado;
    }

    <?php if (isset($success)) { ?>
        $(function() {
            var success = <?php echo $success; ?>

            if (success) {
                var icono = 'success';
            } else {
                var icono = 'warning';
            }

            swal({
                title: '<?php echo $mensaje; ?>',
                icon: icono,
                timer: 2000
            }).then((value) => {
                $('#codigo').focus();
            });
        });
    <?php } ?>

    function buscarProducto(e, tagCodigo, codigo) {
        var enterKey = 13;
        if (codigo != '') {
            if (e.which == enterKey) {
                $.ajax({
                    method: "POST",
                    url: '<?php echo base_url(); ?>/productos/buscarPorCodigo',
                    dataType: 'json',
                    data: {
                        codigo: codigo,
                        <?php echo csrf_token(); ?>: '<?php echo csrf_hash(); ?>'
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(resultado) {
                        if (resultado == 0) {
                            $(tagCodigo).val('');
                        } else {

                            $("#validacionCodigo").html(resultado.error);

                            if (resultado.existe) {
                                $("#id_producto").val(resultado.datos['id']);
                                $("#nombre").val(resultado.datos['nombre']);
                                $("#existencias").val(resultado.datos['existencias']);
                                $("#nueva_existencia").val(resultado.datos['existencias']);
                                $("#cantidad").val('');
                                $(tagCodigo).removeProp("aria-describedby");
                                $(tagCodigo).prop("class", "form-control");
                                $("#cantidad").focus();
                            } else {
                                $("#id_producto").val('');
                                $("#nombre").val('');
                                $("#existencias").val('');
                                $("#nueva_existencia").val('');
                                $(tagCodigo).prop("aria-describedby", "validacionCodigo");
                                $(tagCodigo).prop("class", "form-control is-invalid");
                                $(tagCodigo).focus();
                            }
                        }
                    }
                });
            }
        }
    }
</script>