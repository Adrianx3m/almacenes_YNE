<?php
date_default_timezone_set('America/Mexico_City');
session_start();
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['limite']) && isset($_SESSION['nombre']) && $_SESSION['limite']>date_timestamp_get(date_create())){
    include_once "../../conexion/conexion.php";
    $consultar_rol = $conexion->prepare("SELECT roles.nombre,usuarios.id FROM roles,usuario_roles,usuarios WHERE usuarios.usuario=:usuario AND usuarios.id=usuario_roles.id_usuario and roles.id=usuario_roles.id_roles");
    $consultar_rol->bindParam(':usuario',$_SESSION['usuario']);
    $consultar_rol->execute();
    $fila = $consultar_rol->fetch(PDO::FETCH_ASSOC);
    if($fila['nombre']===NULL){
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
        <form action="crear_producto.php" method="POST">
        <div class="card mt-5">
            <div class="card-header">
                <center>
                    <h5>Nombre</h5>
                    <input class="form-control" type="text" name="nombre" required>
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Descripción</h5>
                    <input class="form-control" type="text" name="descripcion" required>
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Costo</h5>
                    <input class="form-control" type="number" name="costo" required>
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Almacen</h5>
                    <select class="form-control" name="almacen">
        <option value="1">Almacen 1</option>
        <option value="2">Almacen 2</option>
      </select>
                </center>
            </div>
        </div>
        <div class="card-body mt-3">
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
        $crear_producto = $conexion->prepare("INSERT INTO producto(id, nombre, descripcion, costo, id_almacen) VALUES (NULL, :nombre, :descripcion, :costo, :almacen)");

        // Vincular los parámetros
        $crear_producto->bindParam(':nombre', $_POST['nombre']);
        $crear_producto->bindParam(':descripcion', $_POST['descripcion']);
        $crear_producto->bindParam(':costo', $_POST['costo']);
        $crear_producto->bindParam(':almacen', $_POST['almacen']);

        // Ejecutar la consulta
        $crear_producto->execute();


        $descripcion_accion = "El operador " . $fila['id'] . " ha creado un producto nuevo en el almacen ".$_POST['almacen'];
        

        $cargar_al_log = $conexion->prepare("INSERT INTO logs(id, fecha_hora, id_usuario, accion, descripcion_accion) VALUES(NULL, :fh, :idu, 'CREACIÓN DE PRODUCTO', :desc_accion)");
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
                    <h3>Producto Creado</h3>
                </div>
                <div class="card-body">
                    <p>El producto ha sido registrado exitosamente</p>
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