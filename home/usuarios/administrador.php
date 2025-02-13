<?php 
if(session_status()===PHP_SESSION_ACTIVE && isset($_SESSION['usuario']) && isset($_SESSION['aux']) && $_SESSION['aux']==="administrador" && isset($_SESSION['limite']) && isset($_SESSION['nombre']) && $_SESSION['limite']>date_timestamp_get(date_create())){
 ?> 

<!doctype html>
<html lang="es" data-bs-theme="auto">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Adrián Hernández Tejeda">
    <title>Administrador</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body> 
<main>

  <nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container-fluid">
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
        <ul class="navbar-nav">
          <li class="nav-item">
            <p class="nav-link active"><?php echo "Bienvenido ".$_SESSION['nombre']; ?></p>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">Acciones</a>
            <ul class="dropdown-menu">
              <li><button class="dropdown-item" href="#" id="a_usuarios">Administrar Usuarios</button></li>
              <li><button class="dropdown-item" href="#" id="a_inventarios">Inventarios</button></li>
              <li><button class="dropdown-item" href="#" id="cerrar_sesion">Cerrar Sesión</button></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container" id="container">
    <img src="images/yne-logo.png" width="100%" height="500vh" style="opacity:50%;">
  </div>
</main>
<script src="../js/bootstrap.bundle.min.js"></script>
<script>
  function cambiar_contenido(documento){
    const http = new XMLHttpRequest();
    const archivo = `usuarios/tareas_admin/${documento}.php`;

    http.onreadystatechange = function(){
        if(this.readyState==4 && this.status==200){
            //console.log(this.responseText);
            document.querySelector('#container').innerHTML=this.responseText;
        }
    }
    http.open("GET",archivo);
    http.send();
  }

  document.querySelector('#a_usuarios').addEventListener("click",()=>{
    cambiar_contenido("admin_usuarios");
  })
  document.querySelector('#a_inventarios').addEventListener("click",()=>{
    cambiar_contenido("admin_inventarios");
  })
  document.querySelector('#cerrar_sesion').addEventListener("click",()=>{
    document.cookie="";
    window.location.href="logout.php";
  })
</script>
    </body>
</html>

<?php }else{
  header("Location: ../");
} ?>