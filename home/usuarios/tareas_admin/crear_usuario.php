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
    if($metodo === "GET"){
        ?>

        <link rel="stylesheet" href="../../../css/bootstrap.css">
        <header class="bg-danger">
            <a class="btn btn-danger" href="../../">Regresar</a>
        </header>
        <div class="container">
        <form action="crear_usuario.php" method="POST">
        <div class="card mt-5">
            <div class="card-header">
                <center>
                    <h5>Nombre</h5>
                    <input class="form-control" type="text" name="nombre" required>
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Usuario</h5>
                    <input class="form-control" type="text" name="usuario" required>
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
        <option value="1">Administrador</option>
        <option value="2">Operador</option>
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
    elseif($metodo === "POST"){
        // Obtener la fecha actual
        $fecha_hora = date("Y-m-d H:i:s");

        // Preparar la consulta
        $crear_usuario = $conexion->prepare("INSERT INTO usuarios(id, nombre, usuario, password, creacion, ultima_conexion, ststus) VALUES (NULL, :nombre, :usuario, AES_ENCRYPT(:pwd, :usuario2), :crt, NULL, 1)");

        // Vincular los parámetros
        $crear_usuario->bindParam(':nombre', $_POST['nombre']);
        $crear_usuario->bindParam(':usuario', $_POST['usuario']);
        $crear_usuario->bindParam(':usuario2', $_POST['usuario']); // Clave de cifrado
        $crear_usuario->bindParam(':pwd', $_POST['contrasenia']);
        $crear_usuario->bindParam(":crt", $fecha_hora);

        // Ejecutar la consulta
        $crear_usuario->execute();

        $id_usuario_creado = $conexion->lastInsertId();
        $asignar_rol = $conexion->prepare("INSERT INTO usuario_roles(id_usuario,id_roles) VALUES(:idusr,:idrl)");
        $asignar_rol->bindParam(':idusr',$id_usuario_creado);
        $asignar_rol->bindParam('idrl',$_POST['rol']);
        $asignar_rol->execute();


        $descripcion_accion = "El administrador " . $fila['id'] . " ha creado un usuario nuevo de con el rol ".$_POST['rol'];
        

        $cargar_al_log = $conexion->prepare("INSERT INTO logs(id, fecha_hora, id_usuario, accion, descripcion_accion) VALUES(NULL, :fh, :idu, 'CREACIÓN DE USUARIO', :desc_accion)");
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
                    <h3>Ususario Creado</h3>
                </div>
                <div class="card-body">
                    <p>El usuario ha sido registrado exitosamente</p>
                </div>
            </div>
        </div>
        <?php
        //header("Location: ../../");
}else{
    echo "<script>window.location.reload();</script>";
    session_unset();
    session_destroy();
    header("Location: ../../logout.php");
}
}
?>