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
    if($metodo === "GET" && isset($_GET['producto']) && preg_match("/[0-9]/", $_GET['producto'])==1){
        $consultar_producto = $conexion->prepare("SELECT id, nombre, descripcion, costo, id_almacen FROM producto WHERE id=:id");
        $consultar_producto->bindParam(":id",$_GET['producto']);
        $consultar_producto->execute(); ?>
        <link rel="stylesheet" href="../../../css/bootstrap.css">
        <header class="bg-danger">
            <a class="btn btn-danger" href="../../">Regresar</a>
        </header>
        <div class="container">
        <form action="editar_producto.php" method="POST">
        <?php while($producto_devuelto=$consultar_producto->fetch(PDO::FETCH_ASSOC)){ ?>
            <input type="number" name="id" value="<?php echo $producto_devuelto['id']; ?>" style="visibility: hidden;">
        <div class="card mt-5">
            <div class="card-header">
                <center>
                    <h5>Nombre</h5>
                    <input class="form-control" type="text" name="nombre" value="<?php echo $producto_devuelto['nombre']; ?>">
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Descripción</h5>
                    <input class="form-control" type="text" name="descripcion" value="<?php echo $producto_devuelto['descripcion']; ?>">
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Precio</h5>
                    <input class="form-control" type="number" name="precio" value="<?php echo $producto_devuelto['costo']; ?>">
                </center>
            </div>
            <div class="card-header">
                <center>
                    <h5>Almacen</h5>
                    <select class="form-control" name="almacen">
        <option value="1" <?php echo $producto_devuelto['id_almacen']=='1' ? "selected" : ""; ?>>Almacen 1</option>
        <option value="2" <?php echo $producto_devuelto['id_almacen']=='2' ? "selected" : ""; ?>>Almacen 2</option>
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
        $actualizar_produto = $conexion->prepare("UPDATE producto SET nombre = :nombre, descripcion = :descripcion, costo=:precio, id_almacen=:almacen WHERE id = :test");
        $actualizar_produto->bindParam(':nombre', $_POST['nombre']);
        $actualizar_produto->bindParam(':descripcion', $_POST['descripcion']);
        $actualizar_produto->bindParam(':precio', $_POST['precio']);
        $actualizar_produto->bindParam(':almacen', $_POST['almacen']);
        $actualizar_produto->bindParam(":test", $_POST['id']);
        $actualizar_produto->execute();

        $descripcion_accion = "El administrador " . $fila['id'] . " ha actualizado la información del producto con id = " . $_POST['id'];
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