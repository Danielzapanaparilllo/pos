<main>
    <div class="container-fluid">
        <h4 class="mt-4"><?php echo $titulo; ?></h4>

        <form id="form_permisos" name="form_permisos" method="POST" action="<?php echo site_url('/roles/guardaPermisos'); ?>">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="id_rol" value="<?php echo $id_rol; ?>" />

            <hr />

            <ul style="list-style: none; padding-left: 5px;">
                <li><input type="checkbox" id="menu_1" name="permisos[]" value="1" <?php echo isset($asignado[1]) ? 'checked' : '';  ?> /> <b>Menú productos</b></li>
                <ul style="list-style: none;">
                    <li><input type="checkbox" id="menu_2" name="permisos[]" value="2" <?php echo isset($asignado[2]) ? "checked" : '';  ?> /> Productos</li>
                    <li><input type="checkbox" id="menu_7" name="permisos[]" value="7" <?php echo isset($asignado[7]) ? 'checked' : '';  ?> /> Unidades</li>
                    <li><input type="checkbox" id="menu_12" name="permisos[]" value="12" <?php echo isset($asignado[12]) ? 'checked' : '';  ?> /> Categorías</li>
                </ul>
                <li><input type="checkbox" id="menu_17" name="permisos[]" value="17" <?php echo isset($asignado[17]) ? 'checked' : '';  ?> /> <b>Menú Inventario</b></li>
                <li><input type="checkbox" id="menu_20" name="permisos[]" value="20" <?php echo isset($asignado[20]) ? 'checked' : '';  ?> /> <b>Menú clientes</b></li>
                <li><input type="checkbox" id="menu_25" name="permisos[]" value="25" <?php echo isset($asignado[25]) ? 'checked' : '';  ?> /> <b>Menú caja</b></li>
                <li><input type="checkbox" id="menu_26" name="permisos[]" value="26" <?php echo isset($asignado[26]) ? 'checked' : '';  ?> /> <b>Menú ventas</b></li>
                <li><input type="checkbox" id="menu_28" name="permisos[]" value="28" <?php echo isset($asignado[28]) ? 'checked' : '';  ?> /> <b>Menú reportes</b></li>
                <li><input type="checkbox" id="menu_29" name="permisos[]" value="29" <?php echo isset($asignado[29]) ? 'checked' : '';  ?> /> <b>Menú administración</b>
                    <ul style="list-style: none;">
                        <li><input type="checkbox" id="menu_30" name="permisos[]" value="30" <?php echo isset($asignado[30]) ? 'checked' : '';  ?> /> Datos generales</li>
                        <li><input type="checkbox" id="menu_31" name="permisos[]" value="31" <?php echo isset($asignado[31]) ? 'checked' : '';  ?> /> Configuración</li>
                        <li><input type="checkbox" id="menu_32" name="permisos[]" value="32" <?php echo isset($asignado[32]) ? 'checked' : '';  ?> /> Usuarios</li>
                        <li><input type="checkbox" id="menu_38" name="permisos[]" value="38" <?php echo isset($asignado[38]) ? 'checked' : '';  ?> /> Roles</li>
                        <li><input type="checkbox" id="menu_44" name="permisos[]" value="44" <?php echo isset($asignado[44]) ? 'checked' : '';  ?> /> Cajas</li>
                        <li><input type="checkbox" id="menu_49" name="permisos[]" value="49" <?php echo isset($asignado[49]) ? 'checked' : '';  ?> /> Logs de acceso</li>
                    </ul>
                </li>
            </ul>

            <button type="submit" class="btn btn-primary">Guardar</button>

        </form>
    </div>
</main>