<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar - DWES Store</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="cabecera">
    <div class="contenedor">
        <h1>Buscar productos</h1>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="compra.php">Comprar</a>
        </nav>
    </div>
</header>

<main class="contenedor">

    <section class="panel">

        <!--
            Este formulario es solamente HTML.
            Sin PHP no puede procesar realmente la búsqueda.
        -->
        <form class="formulario" action="buscar.php" method="GET">

            <div class="campo">
                <label for="q">Nombre del producto</label>

                <input
                    type="text"
                    id="q"
                    name="q"
                    placeholder="Ej.: teclado"
                >
            </div>

            <div>
                <button type="submit">
                    Buscar
                </button>
            </div>

        </form>

    </section>

    <section class="panel">
        <h2>Ejemplo de resultados</h2>
        <p>
            Como esta versión no tiene PHP, estos resultados son estáticos.
        </p>
    </section>

    <section class="grid-productos">

        <article class="producto">
            <h3>Teclado mecánico</h3>
            <p>Periféricos</p>
            <p class="precio">79,90 €</p>
            <a class="boton" href="producto.php">
                Ver producto
            </a>
        </article>

    </section>

</main>

</body>
</html>