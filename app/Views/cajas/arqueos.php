<main>
        <div class="container-fluid">
            <h4 class="mt-4"><?php echo $titulo; ?></h4>

            <div>
                <p>
                    <a href="<?php echo base_url(); ?>/cajas/nuevo_arqueo" class="btn btn-info btn-sm">Agregar</a>
                    <a href="<?php echo base_url(); ?>/cajas/eliminados" class="btn btn-warning btn-sm">Eliminados</a>
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Fecha apertura</th>
                            <th>Fecha cierre</th>
                            <th>Monto inicial</th>
                            <th>Monto final</th>
                            <th>Total ventas</th>
                            <th>Estatus</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($datos as $dato) { ?>
                            <tr>
                                <td><?php echo $dato['id']; ?></td>
                                <td><?php echo $dato['fecha_inicio']; ?></td>
                                <td><?php echo $dato['fecha_fin']; ?></td>
                                <td><?php echo $dato['monto_inicial']; ?></td>
                                <td><?php echo $dato['monto_final']; ?></td>
                                <td><?php echo $dato['total_ventas']; ?></td>
                                <?php if ($dato['estatus'] == 1) { ?>
                                    <td>Abierta</td>
                                    <td><a href="#" data-href="<?php echo base_url() . '/cajas/cerrar/' . $dato['id']; ?>" data-toggle="modal" data-target="#modal-confirma" data-placement="top" title="Eliminar registro" class="btn btn-danger"><i class="fas fa-lock"></i></a></td>
                                <?php } else { ?>
                                    <td>Cerrada</td>
                                    <td><a href="#" data-href="<?php echo base_url() . '/cajas/eliminar/' . $dato['id']; ?>" data-toggle="modal" data-target="#add-new" data-placement="top" title="Eliminar registro" class="btn btn-success"><i class="fas fa-print"></i></a></td>
                                <?php } ?>

                            </tr>

                        <?php } ?>
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
                    <p>¿Desea cerrar caja?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
                    <a class="btn btn-danger btn-ok">Si</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Agregar Nuevos Registros -->
    <div class="modal fade" id="addnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Cerrar caja</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <form method="POST" action="AgregarNuevo.php">
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="control-label" >Fecha:</label>
                                </div>
                                <div class="col-sm-5">
                                    <input type="text" class="form-control" name="nombres">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="control-label" style="position:relative; top:7px;">Apellidos:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="apellidos">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="control-label" style="position:relative; top:7px;">Telefono:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="telefono">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="control-label" style="position:relative; top:7px;">Carrera:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="carrera">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-2">
                                    <label class="control-label" style="position:relative; top:7px;">Pais:</label>
                                </div>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="pais">
                                </div>
                            </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Cancelar</button>
                    <button type="submit" name="agregar" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-disk"></span> Guardar Registro</button>
                    </form>
                </div>

            </div>
        </div>
    </div>