<?php
// obtenemos la el token de la sesión y verificamos que existan las variables y la expiración
session_start();
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['limite']) && isset($_SESSION['nombre']) && $_SESSION['limite']>date_timestamp_get(date_create())){
    
    include_once "conexion/conexion.php";
     // consultamos en la base de datos el tipo de rol que tiene el usuario y le mostramos su panel
    $consultar_rol = $conexion->prepare("SELECT roles.nombre FROM roles,usuario_roles,usuarios WHERE usuarios.usuario=:usuario AND usuarios.id=usuario_roles.id_usuario and roles.id=usuario_roles.id_roles");
    $consultar_rol->bindParam(':usuario',$_SESSION['usuario']);
    $consultar_rol->execute();
    $fila = $consultar_rol->fetch(PDO::FETCH_ASSOC);
    
    if($fila['nombre']===NULL){
        echo "<script>alert('La Sesión no existe o ha caducado');</script>";
    session_unset();
    session_destroy();
    header("Location: ../");
    }else{
        $_SESSION['aux']=$fila['nombre'];
    require "usuarios/".$fila['nombre'].".php";  
    }
}else{
    echo "<script>alert('La Sesión no existe o ha caducado');</script>";
    session_unset();
    session_destroy();
    header("Location: ../");
}
?>