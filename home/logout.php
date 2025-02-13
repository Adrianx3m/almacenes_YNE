<?php 
date_default_timezone_set('America/Mexico_City');
session_start();
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['limite']) && isset($_SESSION['nombre'])){
    include_once "conexion/conexion.php";
    // Cargamos en el log el cierre de sesión
    $log = $conexion->prepare("INSERT INTO logs(id,fecha_hora,id_usuario,accion,descripcion_accion) VALUES(NULL,:fh,(SELECT id FROM usuarios WHERE nombre=:nombre_completo AND usuario=:usuario),'CIERRE DE SESIÓN','Cierre de sesión')");
      $log->bindParam(':fh',date("Y-m-d H:i:s"));
      $log->bindParam(':nombre_completo',$_SESSION['nombre']);
      $log->bindParam(':usuario',$_SESSION['usuario']);
      $log->execute();
      //cerramos sesión
    session_unset();
    session_destroy();
    header("Location: ../");
}else{
    header("Location: ../");
}

?>