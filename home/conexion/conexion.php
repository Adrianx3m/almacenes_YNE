<?php

    define("base_de_datos", "yne_almacenes");
    define('usuario_base_de_datos', "root");
    define('contraseña_base_de_datos', "");
    define('servidor_base_de_datos', "localhost");

    $conexion = new PDO('mysql:host='.constant("servidor_base_de_datos").';dbname='.constant('base_de_datos'), constant('usuario_base_de_datos'), constant('contraseña_base_de_datos'));

?>