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
        ?>
        <link rel="stylesheet" href="../../../css/bootstrap.css">
        <header class="bg-danger">
            <a class="btn btn-danger" href="../../">Regresar</a>
        </header>
        <div class="container">
        <form action="eliminar_producto.php" method="POST">
            <input type="number" name="producto" style="visibility:hidden;" value="<?php echo $_GET['producto']; ?>">
            <div class="card mt-4">
                <div class="card-header">
                    <h3>Aviso</h3>
                </div>
                <div class="card-body">
                    
                        <p>¿Estas Seguro que deseas eliminar este producto?</p>
                        <input class="btn btn-danger" type="submit" value="Eliminar">
                    </form>
                </div>
            </div>
        </div>
        <?php
    }elseif($metodo=="POST"){
        $borrar_usuario = $conexion->prepare("DELETE FROM producto WHERE id=:id");
        $borrar_usuario->bindParam(":id",$_POST['producto']);
        $borrar_usuario->execute();

        $descripcion_accion = "El administrador ".$fila['id']." ha eliminado el producto con id =".$_POST['producto'];
        $fecha_hora = date("Y-m-d H:i:s");

        $cargar_al_log = $conexion->prepare("INSERT INTO logs(id,fecha_hora,id_usuario,accion,descripcion_accion) VALUES(NULL,:fh,:idu,'ELIMINACIÓN DE PRODUCTO',:desc_accion)");
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
                    <h3>Producto Eliminado</h3>
                </div>
                <div class="card-body">
                    <p>El producto ha sido eliminado</p>
                </div>
            </div>
        </div>
        <?php
    }
}
?>