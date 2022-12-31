<main>
    <div class="container-fluid">
        <h4 class="mt-4">Categor&iacute;as eliminadas</h4>

        <div>
            <p>
                <a href="<?php echo site_url('categorias'); ?>" class="btn btn-primary btn-sm">Categor&iacute;as</a>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm display" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripci&oacute;n</th>
                        <th width="5%"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($datos as $dato) { ?>
                        <tr>
                            <td><?php echo $dato['id']; ?></td>
                            <td><?php echo esc($dato['nombre']); ?></td>
                            <td><?php echo esc($dato['descripcion']); ?></td>
                            <td><a href="#" data-href="<?php echo site_url('categorias/reingresar/' . $dato['id']); ?>" data-toggle="modal" data-target="#modal-confirma" rel='tooltip' data-placement="top" title="Reingresar registro" class="btn btn-success btn-sm"><i class="fas fa-arrow-alt-circle-up"></i></a></td>
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
                <p>¿Desea eliminar este registro?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Cancelar</button>
                <a class="btn btn-success btn-ok">Si</a>
            </div>
        </div>
    </div>
</div>