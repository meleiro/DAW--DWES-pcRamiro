<?php

// ======================================================
// 1. CARGAMOS LOS ARCHIVOS PHP NECESARIOS
// ======================================================

// require_once carga y ejecuta el archivo indicado.
//
// "once" significa que, aunque intentemos incluir el mismo
// archivo varias veces, PHP solamente lo cargará una vez.
//
// funciones.php contendrá las funciones de nuestra aplicación.
// Por ejemplo:
//
// function formatearPrecio($precio) {
//     ...
// }
require_once "funciones.php";


// datos.php contendrá los datos de nuestra aplicación.
//
// Por ejemplo, podría contener un array:
//
// $productos = [
//     ...
// ];
require_once "datos.php";

?>

<!DOCTYPE html>

<!--
    A partir de aquí tenemos principalmente HTML.

    PHP se ejecuta EN EL SERVIDOR.

    El navegador NO recibe este código PHP.
    El navegador recibirá únicamente el HTML generado.
-->
<html lang="es">

<head>

    <!-- Codificación de caracteres -->
    <meta charset="UTF-8">

    <!-- Adaptación a dispositivos móviles -->
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <!-- Título mostrado en la pestaña del navegador -->
    <title>DWES Store</title>

    <!-- Hoja de estilos CSS externa -->
    <link
        rel="stylesheet"
        href="estilos.css"
    >

</head>


<body>


<!-- ==================================================
     CABECERA DE LA PÁGINA
     ================================================== -->

<header class="cabecera">

    <div class="contenedor">

        <h1>DWES Store</h1>

        <p>Versión estática en HTML y CSS</p>


        <!--
            Menú de navegación.

            Cada enlace realizará una nueva petición
            HTTP al servidor.
        -->
        <nav class="navegacion">

            <a href="index.php">
                Inicio
            </a>

            <a href="buscar.php">
                Buscar
            </a>

            <a href="compra.php">
                Comprar
            </a>

        </nav>

    </div>

</header>



<!-- ==================================================
     CONTENIDO PRINCIPAL
     ================================================== -->

<main class="contenedor">


    <!-- Título de la sección -->
    <section class="panel">

        <h2>Catálogo</h2>

    </section>



    <!-- ==================================================
         CATÁLOGO DE PRODUCTOS
         ================================================== -->

    <section class="grid-productos">


        <?php

        // Recorremos el array $productos.
        //
        // En cada iteración, $producto contendrá
        // uno de los productos del array.
        //
        // Si tenemos:
        //
        // $productos = [
        //     ["nombre" => "Teclado", ...],
        //     ["nombre" => "Ratón", ...],
        //     ["nombre" => "Monitor", ...]
        // ];
        //
        // foreach realizará 3 iteraciones.
        //
        // Primera:
        // $producto → Teclado
        //
        // Segunda:
        // $producto → Ratón
        //
        // Tercera:
        // $producto → Monitor

        foreach ($productos as $producto) {

        ?>


            <!--
                Este <article> se generará una vez
                por cada producto existente en el array.
            -->
            <article class="producto">


                <!-- ==============================
                     NOMBRE DEL PRODUCTO
                     ============================== -->

                <h2>

                    <?=
                        // <?= es una forma abreviada de:
                        //
                        // <?php echo ...;
                        //
                        // Mostramos el nombre del producto.
                        $producto["nombre"]
                    ?>

                </h2>



                <!-- ==============================
                     CATEGORÍA
                     ============================== -->

                <p>

                    Categoria:

                    <?=
                        // Accedemos al valor asociado
                        // a la clave "categoria".
                        $producto["categoria"]
                    ?>

                </p>



                <!-- ==============================
                     PRECIO
                     ============================== -->

                <p class="precio">

                    <?=
                        // Llamamos a nuestra función
                        // formatearPrecio().
                        //
                        // Le pasamos como argumento
                        // el precio del producto.
                        //
                        // Por ejemplo:
                        //
                        // 29.9
                        //
                        // podría convertirse en:
                        //
                        // 29,90 €

                        formatearPrecio(
                            $producto["precio"]
                        )
                    ?>

                </p>



                <!-- ==============================
                     STOCK
                     ============================== -->

                <p>

                    Stock:

                    <?=
                        // Mostramos el stock disponible.
                        $producto["stock"]
                    ?>

                </p>


            </article>


        <?php

        // Cerramos el bloque correspondiente
        // al foreach.
        }

        ?>


    </section>

</main>



<!-- ==================================================
     PIE DE PÁGINA
     ================================================== -->

<footer class="pie">

    <div class="contenedor">

        Proyecto de Desarrollo Web en Entorno Servidor

    </div>

</footer>


</body>

</html>