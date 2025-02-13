<?php
date_default_timezone_set('America/Mexico_City');
session_start();
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['limite']) && isset($_SESSION['nombre']) && $_SESSION['limite']>date_timestamp_get(date_create())){
    include_once "../../conexion/conexion.php";
    $consultar_rol = $conexion->prepare("SELECT roles.nombre,usuarios.id FROM roles,usuario_roles,usuarios WHERE usuarios.usuario=:usuario AND usuarios.id=usuario_roles.id_usuario and roles.id=usuario_roles.id_roles");
    $consultar_rol->bindParam(':usuario',$_SESSION['usuario']);
    $consultar_rol->execute();
    $fila = $consultar_rol->fetch(PDO::FETCH_ASSOC);
    if($fila['nombre']===NULL || $fila['nombre']==="operador"){
        session_unset();
    session_destroy();
    header("Location: ../../");
    }

    $metodo = $_SERVER['REQUEST_METHOD'];
    if($metodo === "GET" && isset($_GET['usuario']) && preg_match("/[0-9]/", $_GET['usuario'])==1){
        $consultar_usuario = $conexion->prepare("SELECT usuarios.id,usuarios.nombre,usuarios.usuario,roles.nombre as rol FROM usuarios,roles,usuario_roles WHERE usuario_roles.id_usuario=usuarios.id AND usuario_roles.id_roles=roles.id AND usuarios.id=:id");
        $consultar_usuario->bindParam(":id",$_GET['usuario']);
        $consultar_usuario->execute(); ?>
        <link rel="stylesheet" href="../../../css/bootstrap.css">
        <header class="bg-danger">
            <a class="btn btn-danger" href="../../">Regresar</a>
        </header>
        <div class="container">
        <form action="editar_usuario.php" method="POST">
        <?php while($usuario_devuelto=$consultar_usuario->fetch(PDO::FETCH_ASSOC)){ ?>
            <input type="number" name="id" value="<?php echo $usuario_devuelto['id']; ?>" style="visibility: hidden;">
        <div class="card mt-5">
            <div class="card-header">
                <center>
                    <h5>Nombre</h5>
                    <input class="form-control" type="text" name="nombre" value="<?php echo $usuario_devuelto['nombre']; ?>">
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Usuario</h5>
                    <input class="form-control" type="text" name="usuario" value="<?php echo $usuario_devuelto['usuario']; ?>">
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Contraseña</h5>
                    <input class="form-control" type="text" name="contrasenia" required>
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Rol</h5>
                    <select class="form-control" name="rol">
        <option value="1" <?php echo $usuario_devuelto['rol']=="administrador" ? "selected" : ""; ?>>Administrador</option>
        <option value="2" <?php echo $usuario_devuelto['rol']=="operador" ? "selected" : ""; ?>>Operador</option>
      </select>
                </center>
            </div>
        </div>
        <div class="card-body">
            <input type="submit" class="btn btn-success form-control">
        </div>
        </form>
        </div>
        <?php

        }
    }
    elseif($metodo === "POST"){
        $actualizar_usuario = $conexion->prepare("UPDATE usuarios SET nombre = :nombre, usuario = :usuario, password = AES_ENCRYPT(:pwd, :usuario2) WHERE id = :test");
        $actualizar_usuario->bindParam(':nombre', $_POST['nombre']);
        $actualizar_usuario->bindParam(':usuario', $_POST['usuario']);
        $actualizar_usuario->bindParam(':usuario2', $_POST['usuario']);
        $actualizar_usuario->bindParam(':pwd', $_POST['contrasenia']);
        $actualizar_usuario->bindParam(":test", $_POST['id']);
        $actualizar_usuario->execute();

        $actualizar_rol = $conexion->prepare("UPDATE usuario_roles SET id_roles=:rol WHERE id_usuario=:usr");
        $actualizar_rol->bindParam(':rol', $_POST['rol']);
        $actualizar_rol->bindParam(':usr',$_POST['id']);
        $actualizar_rol->execute();

        $descripcion_accion = "El administrador " . $fila['id'] . " ha actualizado la información del usuario con id = " . $_POST['id'];
        $fecha_hora = date("Y-m-d H:i:s");

        $cargar_al_log = $conexion->prepare("INSERT INTO logs(id, fecha_hora, id_usuario, accion, descripcion_accion) VALUES(NULL, :fh, :idu, 'ACTUALIZACIÓN DE USUARIO', :desc_accion)");
        $cargar_al_log->bindParam(':fh', $fecha_hora);
        $cargar_al_log->bindParam(':idu', $fila['id']);
        $cargar_al_log->bindParam(':desc_accion', $descripcion_accion);
        $cargar_al_log->execute();


        ?>
        <link rel="stylesheet" href="../../../css/bootstrap.css">
        <header class="bg-danger">
            <a class="btn btn-danger" href="../../">Regresar</a>
        </header>
        <div class="container">
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Datos Actualizados</h3>
                </div>
                <div class="card-body">
                    <p>Los datos se han actualizado</p>
                </div>
            </div>
        </div>
        <?php
        //header("Location: ../../");
    }
}else{
    echo "<script>window.location.reload();</script>";
    session_unset();
    session_destroy();
    header("Location: ../../logout.php");
}
?>