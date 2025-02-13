<?php
// configurar el servidor para que trabaje con la zona horaria de méxico
date_default_timezone_set('America/Mexico_City');

session_start();
// validamos que no exista ninguna variable de sesión en el token si la hay redirigimos al home
if(isset($_SESSION['usuario'])){
    header("Location: home");
}
// nos aseguramos que el script de php solo funcione si recibe una petición post
  $metodo = $_SERVER['REQUEST_METHOD'];
  if($metodo=="POST"){
    require_once "home/conexion/conexion.php";
    $usuario = $_POST['user'];
    $contra = $_POST['pwd'];
    // Validar usuario y contraseña en la BD
    $consultar_usuario = $conexion->prepare('SELECT id,nombre FROM usuarios WHERE usuario=:usuario AND :password=AES_DECRYPT(password,:usuario) AND ststus=1');
    $consultar_usuario->bindParam(':usuario',$usuario);
    $consultar_usuario->bindParam(':password',$contra);
    $consultar_usuario->execute();
    $fila = $consultar_usuario->fetch(PDO::FETCH_ASSOC);

    if($fila){
      //agregar fecha de ultima conexión
      $agregar_ultima_conexion = $conexion->prepare("UPDATE usuarios SET ultima_conexion=:ultimaCon WHERE usuarios.id = :usuario");
      $agregar_ultima_conexion->bindParam(':ultimaCon',date("Y-m-d"));
      $agregar_ultima_conexion->bindParam(':usuario',$fila['id']);
      $agregar_ultima_conexion->execute();
      // Guardar el inicio de sesión en el log
      $log = $conexion->prepare("INSERT INTO logs(id,fecha_hora,id_usuario,accion,descripcion_accion) VALUES(NULL,:fh,:idu,'INICIO DE SESIÓN','Inicio de sesión')");
      $log->bindParam(':fh',date("Y-m-d H:i:s"));
      $log->bindParam(':idu',$fila['id']);
      if($log->execute()){
        // Cargamos las variables al token de sesión
        $_SESSION['usuario']=$usuario;
        $limite=date_create();
        date_add($limite,date_interval_create_from_date_string("30 minutes"));
        $_SESSION['limite']=$limite->getTimestamp();
        $_SESSION['nombre']=$fila['nombre'];
        // redirigimos al home
        header("Location: home");
      }
    }
  }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Adrián Hernández Tejeda">
    <link rel="stylesheet" href="css/bootstrap.css">
    <title>YNE Almacenes</title>
    <style>
        .gradient-custom {
            background: #6a11cb;
            background: -webkit-linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1));
            background: linear-gradient(to right, rgba(106, 17, 203, 1), rgba(37, 117, 252, 1))
        }
    </style>
</head>
<body>
    <section class="vh-100 gradient-custom">
        <div class="container py-5 h-100">
          <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
              <div class="card bg-dark text-white" style="border-radius: 1rem;">
                <form method="POST" action="">
                <div class="card-body p-5 text-center">
      
                  <div class="mb-md-5 mt-md-4 pb-5">
      
                    <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                    <br>
      
                    <div class="form-outline form-white mb-4">
                    <label class="form-label">Usuario</label>
                      <input type="text" class="form-control form-control-lg" name="user" />
                    </div>
      
                  <div class="row">
                    <div class="col-10">
                    <div class="form-outline form-white mb-4">
                    <label class="form-label">Contraseña</label>
                      <input type="password" class="form-control form-control-lg" name="pwd" />
                    </div>
                   </div>
                    <div class="col-1">
                    <div class="form-outline form-white mb-4">
                      <br>
                    <div class="form-check form-switch">
                        <input class="form-check-input py-3 px-4" type="checkbox">
                    </div>

                    </div>
                      
                    </div>
                  </div>
      
                   <br>
      
                    <button class="btn btn-outline-light btn-lg px-5" type="submit">Ingresar</button>
      
                  </div>
      
                </div>
      </form>
              </div>
            </div>
          </div>
        </div>
      </section>
    
</body>
<script>
  let check = document.querySelector("body > section > div > div > div > div > form > div > div > div.row > div.col-1 > div > div > input");
  let contra = document.querySelector("body > section > div > div > div > div > form > div > div > div.row > div.col-10 > div > input");
  
  check.addEventListener('click',()=>{
    return check.checked ? contra.type="text" : contra.type="password";
  })
</script>
</html>