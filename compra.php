<?php

    require_once "datos.php";
    require_once "funciones.php";

    $errores = [];
    $compraRealizada = false;
    $resultadoCompra = null;
    $productoSeleccionado = null;
    $nombre = "";
    $email = "";
    $unidades = 1;
    $productoId = null;

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

       $nombre = leerCadena($_POST, "nombre");
       if ($nombre === "") {
          $errores[] = "Debes de introducir un nombre";
       }

       $emailBruto = leerCadena($_POST, "email");
       $emailValidado = filter_var($emailBruto, FILTER_VALIDATE_EMAIL);
       if ( $emailValidado === false){
         $errores[] = "Correo electrónico con formato incorrecto";
       } else {
        $email = $emailValidado;
       }

    }


?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprar - DWES Store</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="cabecera">
    <div class="contenedor">
        <h1>Realizar compra</h1>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="buscar.php">Buscar</a>
        </nav>
    </div>
</header>

<main class="contenedor">

  <form class="formulario" action="compra.php" method="POST">

  <div class="campo">
        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" value="<?= escapar($nombre) ?>">
  </div>
  <div class="campo">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= escapar($email) ?>">
  </div>
  
        <label for="email">Producto</label>
        <select id="producto" name="producto">
            <option value="">Selecciona un producto</option>
            <?php foreach ($productos as $producto): ?>
                <?php $seleccionado = $productoId === $producto["id"]; ?>
                   <option value="<?= $producto["id"] ?>" <?= $seleccionado ? "selected" : "" ?>>

                            <?= escapar($producto["nombre"]) ?> - <?= formatearPrecio($producto["precio"]) ?>

                   </option> 
            <?php endforeach; ?>    
        </select>
       <div class="campo">
            <label for="unidades">Unidades</label>
            <input type="number" id="unidades" name="unidades" value="<?= $unidades ?>">
       </div>
       <div><button type="submit">Calcular compra</button></div>

  



  </form>

    

</main>

</body>
</html>