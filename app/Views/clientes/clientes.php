<main>
    <div class="container-fluid">
        <h4 class="mt-4">Clientes</h4>

        <div>
            <p>
                <a href="<?php echo site_url('clientes/nuevo'); ?>" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Agregar</a>
                <a href="<?php echo site_url('clientes/baja'); ?>" class="btn btn-warning btn-sm">Historial</a>
            </p>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Direccion</th>
                        <th>Teléfono</th>
                        <th>Correo</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($datos as $dato) { ?>
                        <tr>
                            <td><?php echo $dato['id']; ?></td>
                            <td><?php echo esc($dato['nombre']); ?></td>
                            <td><?php echo esc($dato['direccion']); ?></td>
                            <td><?php echo esc($dato['telefono']); ?></td>
                            <td><?php echo esc($dato['correo']); ?></td>

                            <td><a href="<?php echo base_url() . '/clientes/editar/' . $dato['id']; ?>" class="btn btn-warning btn-sm" rel='tooltip' data-placement='top' title='Modificar registro'><i class="fas fa-pencil-alt"></i></a></td>

                            <td><a href="#" data-href="<?php echo base_url() . '/clientes/eliminar/' . $dato['id']; ?>" data-toggle="modal" data-target="#modal-confirma" rel='tooltip' data-placement="top" title="Eliminar registro" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a></td>
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
                <a class="btn btn-danger btn-ok">Si</a>
            </div>
        </div>
    </div>
</div>