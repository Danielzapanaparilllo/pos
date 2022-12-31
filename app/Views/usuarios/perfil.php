<main>
    <div class="container-fluid">
        <h4 class="mt-4">Perfil de usuario</h4>

        <div class="card mb-3" style="max-width: 540px;">
            <div class="row no-gutters">
                <div class="col-md-4">
                    <img src="<?php echo base_url() ?>/images/blank-profile.png" class="img-thumbnail ">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $usuario['nombre']; ?></h5>
                        <table width="100%">
                            <tr>
                                <td width="30%"><b>Usuario:</b></td>
                                <td width="70%"><?php echo $usuario['usuario']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Rol:</b></td>
                                <td><?php echo $usuario['rol']; ?></td>
                            </tr>
                            <tr>
                                <td><b>Caja:</b></td>
                                <td><?php echo $usuario['caja']; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>