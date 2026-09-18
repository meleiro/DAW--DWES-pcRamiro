<?php
 
  require_once "funciones.php";

  echo formatearPrecio(5000)

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DWES Store</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="cabecera">
    <div class="contenedor">
        <h1>DWES Store</h1>
        <p>Versión estática en HTML y CSS</p>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="buscar.php">Buscar</a>
            <a href="compra.php">Comprar</a>
        </nav>
    </div>
</header>

<main class="contenedor">

    <section class="panel">
        <h2>Catálogo</h2>
        <h3> Prueba de función <?php echo formatearPrecio(6000); ?>  </h3>
       
    </section>

    <section class="grid-productos">

        <article class="producto">
            <h2>Teclado mecánico</h2>
            <p>Categoría: Periféricos</p>
            <p class="precio">79,90 €</p>
            <p>Stock: 7</p>
            <p>
                Estado:
                <span class="estado disponible">Disponible</span>
            </p>
            <div class="acciones">
                <a class="boton" href="producto.php">
                    Ver producto
                </a>
            </div>
        </article>

        <article class="producto">
            <h2>Ratón gaming</h2>
            <p>Categoría: Periféricos</p>
            <p class="precio">39,90 €</p>
            <p>Stock: 3</p>
            <p>
                Estado:
                <span class="estado aviso">Últimas unidades</span>
            </p>
            <div class="acciones">
                <a class="boton" href="producto.php">
                    Ver producto
                </a>
            </div>
        </article>

        <article class="producto">
            <h2>Monitor 27 pulgadas</h2>
            <p>Categoría: Monitores</p>
            <p class="precio">219,90 €</p>
            <p>Stock: 0</p>
            <p>
                Estado:
                <span class="estado agotado">Agotado</span>
            </p>
            <div class="acciones">
                <a class="boton" href="producto.php">
                    Ver producto
                </a>
            </div>
        </article>

        <article class="producto">
            <h2>Auriculares USB</h2>
            <p>Categoría: Audio</p>
            <p class="precio">49,90 €</p>
            <p>Stock: 12</p>
            <p>
                Estado:
                <span class="estado disponible">Disponible</span>
            </p>
            <div class="acciones">
                <a class="boton" href="producto.php">
                    Ver producto
                </a>
            </div>
        </article>

    </section>

</main>

<footer class="pie">
    <div class="contenedor">
        Proyecto de Desarrollo Web en Entorno Servidor
    </div>
</footer>

</body>
</html>