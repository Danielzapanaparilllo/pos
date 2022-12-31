<main>
        <div class="container-fluid">
            <h4 class="mt-4">Compras eliminadas</h4>

            <div>
                <p>
                    <a href="<?php echo site_url('compras'); ?>" class="btn btn-warning btn-sm">Compras</a>
                </p>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                        <th>Id</th>
                            <th>Folio</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach($compras as $compra) { ?>
                            <tr>
                            <td><?php echo $compra['id']; ?></td>
                                <td><?php echo $compra['folio']; ?></td>
                                <td><?php echo $compra['total']; ?></td>
                                <td><?php echo $compra['fecha_alta']; ?></td>

                                <td><a href="#" data-href="<?php echo base_url() . '/compras/reingresar/' . $compra['id']; ?>" data-toggle="modal" data-target="#modal-confirma" class="btn btn-success btn-sm" data-placement="top" title="Reingresar registro" ><i class="fas fa-arrow-alt-circle-up"></i></a></td>
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
                    <h5 class="modal-title" id="exampleModalLabel">Reingresar registro</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                   <p>¿Desea reingresar este registro?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">No</button>
                    <a class="btn btn-danger btn-ok">Si</a>
                </div>
            </div>
        </div>
    </div>