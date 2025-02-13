<?php
session_start();
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['aux']) && $_SESSION['aux']==="administrador" && isset($_SESSION['limite']) && isset($_SESSION['nombre']) && $_SESSION['limite']>date_timestamp_get(date_create())){
    include_once "../../conexion/conexion.php";

    $consultar_usuarios = $conexion->prepare("SELECT usuarios.id,usuarios.nombre,usuarios.usuario,roles.nombre as rol,usuarios.creacion,usuarios.ultima_conexion as uc FROM usuarios,roles,usuario_roles WHERE usuario_roles.id_usuario=usuarios.id AND usuario_roles.id_roles=roles.id AND usuarios.id>1 AND usuarios.ststus=1;");
    $consultar_usuarios->execute();
?>
<body>
    <button class="btn btn-dark bg-dark" onClick="window.location.href='usuarios/tareas_admin/crear_usuario.php'">Nuevo Usuario</button>
<table class="table align-middle mb-0 bg-white">
  <thead class="bg-light">
    <tr>
      <th>Nombre</th>
      <th>Usuario</th>
      <th>Rol</th>
      <th>Fecha Creación</th>
      <th>Ultima conexión</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php while($fila = $consultar_usuarios->fetch(PDO::FETCH_ASSOC)){ ?>
    <tr>
      <td>
        <div class="d-flex align-items-center">
          <img
              src="images/user.png"
              class="rounded-circle"
              alt=""
              style="width: 45px; height: 45px"
              />
          <div class="ms-3">
            <p class="fw-bold mb-1"><?php echo $fila['nombre']; ?></p>
          </div>
        </div>
      </td>
      <td>
        <p class="fw-normal mb-1"><?php echo $fila['usuario']; ?></p>
      </td>
      <td>
      <p class="fw-normal mb-1"><?php echo $fila['rol']; ?></p>
      </td>
      <td>
        <p class="fw-normal mb-1"><?php echo $fila['creacion']; ?></p>
      </td>
      <td>
      <p class="fw-normal mb-1"><?php echo $fila['uc']; ?></p>
      </td>
      <td>
        <button
                type="button"
                class="btn btn-link btn-rounded btn-sm fw-bold"
                data-mdb-ripple-color="dark"
                title="Editar"
                id="editar"
                onClick="window.location.href='usuarios/tareas_admin/editar_usuario.php?usuario=<?php echo $fila['id']; ?>'"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill" viewBox="0 0 16 16">
  <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
</svg>
        </button>
        <button
                type="button"
                class="btn btn-link btn-rounded btn-sm fw-bold"
                data-mdb-ripple-color="dark"
                title="Eliminar"
                id="eliminar"
                onClick="window.location.href='usuarios/tareas_admin/eliminar_usuario.php?usuario=<?php echo $fila['id']; ?>'"
                >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
  <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5M8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5m3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0"/>
</svg>
        </button>
      </td>
    </tr>
    <?php } ?>
  </tbody>
</table>
</body>
<?php
}else{
    echo "<script>window.location.reload();</script>";
    session_unset();
    session_destroy();
    header("Refresh:0");
}
?>