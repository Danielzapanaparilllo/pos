$(function () {
    $("#codigo").autocomplete({
        source: urlBase + "/productos/autocompleteData",
        minLength: 3,
        select: function (event, ui) {
            event.preventDefault();
            $("#codigo").val(ui.item.value);
            setTimeout(
                function () {
                    e = jQuery.Event("keypress");
                    e.which = 13;
                    enviaProducto(e, ui.item.value, 1);
                }, 500);
        }
    });

    $("#completa_venta").click(function () {
        let nFilas = $("#tablaProductosVenta tr").length;
        if (nFilas < 2) {
            swal({
                title: "Debe agregar un producto",
                icon: "warning",
                timer: 2000,
                buttons: false
            }).then((value) => {
                $('#codigo').focus();
            });
        } else {
            $('#modalCobro').modal('show');
            var totalVenta = document.getElementById('total').value;
            document.getElementById('cobrar_pago').value = totalVenta;
            document.getElementById('cobrar_total').value = totalVenta;
            var cobrar_pago = document.getElementById('cobrar_pago').value;
            cobrar_pago = cobrar_pago.replace(/,/g, "");
            totalVenta = totalVenta.replace(/,/g, "");
            var cambio = (cobrar_pago - totalVenta).toFixed(2);

            document.getElementById('cobrar_pago').value = totalVenta;
            document.getElementById('cobrar_cambio').value = cambio;
        }
    });

    $('#modalClientes').on('shown.bs.modal', function () {
        $('#buscar_cliente').val('');
        $('#tablaClientes tbody').empty();
        $('#buscar_cliente').trigger('focus')
    });

    $('#modalProductos').on('shown.bs.modal', function () {
        $('#buscar_producto').val('');
        $('#tablaProductos tbody').empty();
        $('#buscar_producto').trigger('focus')
    });

    $('#modalProductoComun').on('shown.bs.modal', function () {
        $('#descripcion_comun').val('Producto común')
        $('#cantidad_comun').val('1.00')
        $('#precio_comun').val('1.00')
        $('#msg_modal_comun').html('');
        $('#descripcion_comun').trigger('focus')
    });

    $('#modalCobro').on('shown.bs.modal', function () {
        $('#cobrar_pago').trigger('focus')
    });

    $('#quita_cliente').click(function () {
        $('#modalClientes').modal('hide');
        $('#buscar_cliente').val('');
        $("#tablaClientes tbody").empty();
        document.getElementById('id_cliente').value = 1;
        document.getElementById('nombre_cliente').value = '';
    });

    $('#agrega_comun').click(function () {
        var descripcion_comun = document.getElementById('descripcion_comun').value;
        var cantidad_comun = document.getElementById('cantidad_comun').value;
        var precio_comun = document.getElementById('precio_comun').value;

        if (descripcion_comun != '' && cantidad_comun > 0 && precio_comun > 0) {
            $('#modalProductoComun').modal('hide');
            agregarProducto('COMUN-001', cantidad_comun, 1, descripcion_comun, precio_comun);
        } else {
            $('#msg_modal_comun').html('* Debe completar la información.');
        }
    });

    $('#cobrar_pago').keyup(function () {
        var totalVenta = document.getElementById('total').value;
        var pago = $('#cobrar_pago').val();
        pago = pago.replace(/,/g, "");
        totalVenta = totalVenta.replace(/,/g, "");
        var cambio = (pago - totalVenta).toFixed(2);
        document.getElementById('cobrar_cambio').value = cambio;;
    });

    $('#terminar_venta').click(function () {
        $("#form_venta").submit();
    });
});

function validateDecimal(valor) {
    var RE = /^\d*\.?\d*$/;
    if (RE.test(valor)) {
        return true;
    } else {
        return false;
    }
}

function enviaProducto(e, codigo, cantidad) {
    let enterKey = 13;
    if (e.which == enterKey) {
        if (codigo != '' && codigo != null && codigo != 0 && cantidad > 0) {
            agregarProducto(codigo, cantidad);
        }
    }
}

function enviaProductoModal(codigo, cantidad) {
    if (codigo != '' && codigo != null && codigo != 0 && cantidad > 0) {
        agregarProducto(codigo, cantidad);
        $('#modalProductos').modal('hide');
        $('#buscar_producto').val('');
        $("#tablaProductos tbody").empty();
    }
}

function agregarProducto(codigo, cantidad, comun = 0, descripcion = '', precio = 0) {
    $.ajax({
        method: "POST",
        url: urlBase + '/TmpMov/inserta',
        data: {
            codigo: codigo,
            cantidad: cantidad,
            id_mov: idVentaTmp,
            comun: comun,
            descripcion: descripcion,
            precio: precio,
            csrf_test_name: csrfHash
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (resultado) {
            if (resultado != 0) {
                $("#codigo").removeClass('has-error');
                $("#codigo").autocomplete("close");
                $("#codigo").val('');
                var resultado = JSON.parse(resultado);

                if (resultado.error != '') {
                    swal({
                        title: resultado.error,
                        icon: "warning",
                        timer: 2500,
                        buttons: false
                    }).then((value) => {
                        $('#codigo').focus();
                    });
                } else {
                    $("#tablaProductosVenta tbody").empty();
                    $("#tablaProductosVenta tbody").append(resultado.datos);
                    $("#total").val(resultado.total);
                }
            }
        }
    });
}

function eliminaProducto(id_producto) {
    $.ajax({
        method: "POST",
        url: urlBase + '/TmpMov/eliminar',
        data: {
            id_producto: id_producto,
            id_mov: idVentaTmp,
            csrf_test_name: csrfHash
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (resultado) {
            if (resultado == 0) {
                $(tagCodigo).val('');
            } else {
                var resultado = JSON.parse(resultado);
                $("#tablaProductosVenta tbody").empty();
                $("#tablaProductosVenta tbody").append(resultado.datos);
                $("#total").val(resultado.total);
            }
        }
    });
}

function buscarCliente(valor) {
    $.ajax({
        method: "POST",
        url: urlBase + '/clientes/buscarClienteVenta',
        data: {
            valor: valor,
            csrf_test_name: csrfHash
        },
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (resultado) {
            var resultado = JSON.parse(resultado);
            $("#tablaClientes tbody").empty();
            $("#tablaClientes tbody").append(resultado);
        }
    });
}

function buscarProductoDetalle(codigo) {
    if (codigo.length > 0) {
        $.ajax({
            method: "POST",
            url: urlBase + '/productos/buscarProductoDetalle',
            data: {
                codigo: codigo,
                csrf_test_name: csrfHash
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (resultado) {
                var resultado = JSON.parse(resultado);
                $("#tablaProductos tbody").empty();
                $("#tablaProductos tbody").append(resultado);
            }
        });
    } else {
        $("#tablaProductos tbody").empty();
    }
}

function buscarProducto(valor) {
    if (valor.length > 0) {
        $.ajax({
            method: "POST",
            url: urlBase + '/productos/buscarProductoVenta',
            data: {
                valor: valor,
                csrf_test_name: csrfHash
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (resultado) {
                var resultado = JSON.parse(resultado);
                $("#tablaProductos tbody").empty();
                $("#tablaProductos tbody").append(resultado);
            }
        });
    } else {
        $("#tablaProductos tbody").empty();
    }
}

function addCliente(id, nombre) {
    document.getElementById('id_cliente').value = id;
    document.getElementById('nombre_cliente').value = 'Cliente: ' + nombre;
    $('#modalClientes').modal('hide');
    $('#buscar_cliente').val('');
    $("#tablaClientes tbody").empty();
}