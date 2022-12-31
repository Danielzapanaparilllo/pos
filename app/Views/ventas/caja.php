<?php $idVentaTmp = uniqid(); ?>

<main>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <form method="POST" id="form_venta" name="form_venta" action="<?php echo site_url('ventas/guarda'); ?>" autocomplete="off">
                    <?php echo csrf_field(); ?>

                    <input type="hidden" id="id_cliente" name="id_cliente" value="1" />
                    <input type="hidden" id="forma_pago" name="forma_pago" value="001" />
                    <input type="hidden" id="id_venta" name="id_venta" value="<?php echo $idVentaTmp; ?>" />

                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-7">
                                <div class="ui-widget">
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text"><span class="fas fa-barcode"></span></div>
                                        </div>
                                        <input class="form-control form-control-sm" id="codigo" name="codigo" type="text" placeholder="Escribe el código y presiona enter" onkeyup="enviaProducto(event, this.value, 1, '<?php echo $idVentaTmp; ?>');" autofocus />
                                        <div class="input-group-prepend">
                                            <button class="btn btn-light btn-sm" type="button" data-toggle="modal" data-target="#modalProductos"><span class="fas fa-search"></span></button>
                                            <button class="btn btn-light btn-sm" type="button" data-toggle="modal" data-target="#modalProductoComun"><span class="fas fa-plus-circle"></span></button>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-12 col-sm-5 my-bottom text-right">
                                <label style="font-weight: bold; font-size: 25px; text-align: center;">Total $ </label> <input type="text" id="total" name="total" size="7" readonly="true" value="0.00" style="font-weight: bold; font-size: 25px; text-align: center; border:#E2EBED; background:#ffffff'" />

                                <button type="button" id="completa_venta" class="btn btn-success"><span class="fas fa-check-circle"></span> Cobrar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="table-responsive">
                    <table id="tablaProductosVenta" class="table table-hover table-striped table-sm" width="100%">
                        <thead class="thead-dark">
                            <th>#</th>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th width="1%"></th>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-12 col-sm-6">
                <input class="form-control form-control-sm" id="nombre_cliente" name="nombre_cliente" type="text" style="border: 0px" />
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-sm-6">
                <button id="cambiar_cliente" name="cambiar_cliente" class="btn btn-light" data-toggle="modal" data-target="#modalClientes"><span class="fas fa-user-plus"></span> Asignar cliente</button>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="modalClientes" tabindex="-1" role="dialog" aria-labelledby="modalclientesLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalclientesLabel">Asignar cliente a venta</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><span class="fas fa-search"></span></div>
                    </div>
                    <input class="form-control form-control-sm" id="buscar_cliente" name="buscar_cliente" type="text" onkeyup="buscarCliente(this.value);" />
                </div>
                <table width="100%" class="table" id="tablaClientes">
                    <thead>
                        <tr>
                            <th width="20%">Id</th>
                            <th width="60%">Nombre</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" id="quita_cliente">Quitar cliente</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProductos" tabindex="-1" role="dialog" aria-labelledby="modalProductosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductosLabel">Buscar producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><span class="fas fa-search"></span></div>
                    </div>
                    <input class="form-control form-control-sm" id="buscar_producto" name="buscar_producto" type="text" onkeyup="buscarProducto(this.value);" />
                </div>
                <table width="100%" class="table table-sm" id="tablaProductos">
                    <thead>
                        <tr>
                            <th width="40%">Descripci&oacute;n</th>
                            <th width="20%">Precio</th>
                            <th width="20%">Existencia</th>
                            <th width="20%"></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalProductoComun" tabindex="-1" role="dialog" aria-labelledby="modalProductoComunLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductoComunLabel">Producto com&uacute;n</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <label>Descripci&oacute;n del producto</label>
                            <input class="form-control form-control-sm" id="descripcion_comun" name="descripcion_comun" type="text" />
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <label>Cantidad</label>
                            <input class="form-control form-control-sm" id="cantidad_comun" name="cantidad_comun" type="text" />
                        </div>

                        <div class="col-12 col-sm-6">
                            <label>Precio</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                </div>
                                <input class="form-control form-control-sm" id="precio_comun" name="precio_comun" onkeypress="return validateDecimal(this.value);" type="text" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-12">
                            <p class="text-danger" id="msg_modal_comun"></p>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="agrega_comun">Agregar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCobro" tabindex="-1" role="dialog" aria-labelledby="modalCobroLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalCobroLabel">Cobrar</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="form-group">
                    <div class="row">
                        <div class="col-12 text-center">
                            <input type="text" id="cobrar_total" name="cobrar_total" readonly="true" value="$ 0.00" style="font-weight: bold; font-size: 35px; text-align: center; border:#E2EBED; background:#ffffff'" />
                        </div>

                        <div class="col-12">
                            <label>Pago</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                </div>
                                <input class="form-control form-control-sm" id="cobrar_pago" name="cobrar_pago" type="text" style="font-size: 25px;" />
                            </div>
                        </div>

                        <div class="col-12">
                            <label>Cambio</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="fas fa-dollar-sign"></i></div>
                                </div>
                                <input class="form-control form-control-sm" id="cobrar_cambio" name="cobrar_cambio" type="text" style="font-size: 25px;" readonly />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-success" id="terminar_venta">Cobrar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var urlBase = '<?php echo base_url(); ?>'
    var csrfHash = '<?php echo csrf_hash(); ?>'
    var idVentaTmp = '<?php echo $idVentaTmp; ?>'
</script>
<script src="<?php echo base_url(); ?>/js/sweetalert.min.js"></script>
<script src="<?php echo base_url(); ?>/js/script_caja.js"></script>