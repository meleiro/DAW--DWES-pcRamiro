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

    <section class="panel">

        <!--
            Este formulario muestra la parte de cliente.
            Sin PHP no existe código de servidor que valide,
            calcule o procese realmente los datos.
        -->
        <form class="formulario" action="compra.php" method="POST">

            <div class="campo">
                <label for="nombre">Nombre</label>

                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    placeholder="Tu nombre"
                >
            </div>

            <div class="campo">
                <label for="email">Correo electrónico</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="nombre@ejemplo.com"
                >
            </div>

            <div class="campo">
                <label for="producto">Producto</label>

                <select id="producto" name="producto">

                    <option value="">
                        Selecciona un producto
                    </option>

                    <option value="1">
                        Teclado mecánico — 79,90 €
                    </option>

                    <option value="2">
                        Ratón gaming — 39,90 €
                    </option>

                    <option value="3">
                        Monitor 27 pulgadas — 219,90 €
                    </option>

                    <option value="4">
                        Auriculares USB — 49,90 €
                    </option>

                </select>
            </div>

            <div class="campo">
                <label for="unidades">Unidades</label>

                <input
                    type="number"
                    id="unidades"
                    name="unidades"
                    min="1"
                    value="1"
                >
            </div>

            <div>
                <button type="submit">
                    Enviar formulario
                </button>
            </div>

        </form>

    </section>

</main>

</body>
</html>