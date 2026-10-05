<?php

/*
|--------------------------------------------------------------------------
| DETALLE DE UN PRODUCTO
|--------------------------------------------------------------------------
|
| Este archivo se encarga de mostrar la información de UN producto concreto.
|
| El producto que queremos mostrar se indica mediante un parámetro GET
| llamado "id".
|
| Por ejemplo:
|
|     producto.php?id=2
|
| Cuando el navegador solicita esa URL, PHP recibe el parámetro mediante
| el array superglobal $_GET:
|
|     $_GET["id"]
|
| IMPORTANTE:
| Los datos que llegan desde una URL son datos externos introducidos por
| el usuario, por lo que NO debemos confiar directamente en ellos.
|
| El proceso que seguimos en este archivo es:
|
|     URL
|      |
|      v
|     $_GET["id"]
|      |
|      v
|     $idBruto
|      |
|      v
|     Validamos que sea un número entero
|      |
|      v
|     $id
|      |
|      +---- ID incorrecto ----------> Error 400
|      |
|      v
|     Buscamos el producto
|      |
|      +---- No existe --------------> Error 404
|      |
|      v
|     Producto encontrado
|
|
| Ejemplos:
|
|     producto.php?id=2
|         -> El ID es válido.
|         -> Buscamos el producto 2.
|
|     producto.php?id=hola
|         -> "hola" no es un entero.
|         -> Respondemos con HTTP 400.
|
|     producto.php?id=-5
|         -> Es un entero, pero no aceptamos IDs menores que 1.
|         -> Respondemos con HTTP 400.
|
|     producto.php?id=999
|         -> El ID tiene un formato válido.
|         -> Pero el producto 999 no existe.
|         -> Respondemos con HTTP 404.
|
|
| DIFERENCIA IMPORTANTE:
|
|     400 Bad Request
|     ----------------
|     La petición contiene un ID que consideramos inválido.
|
|     Ejemplo:
|
|         producto.php?id=hola
|
|
|     404 Not Found
|     -------------
|     El ID es válido, pero no existe ningún producto con ese ID.
|
|     Ejemplo:
|
|         producto.php?id=999
|
|--------------------------------------------------------------------------
*/


/*
|--------------------------------------------------------------------------
| 1. CARGAMOS LOS ARCHIVOS NECESARIOS
|--------------------------------------------------------------------------
|
| datos.php contiene el array $productos.
|
| funciones.php contiene funciones reutilizables, por ejemplo:
|
|     buscarProductoPorId()
|
| Utilizamos require_once porque estos archivos son necesarios para que
| esta página pueda funcionar y queremos cargarlos una sola vez.
|
*/

require_once "datos.php";
require_once "funciones.php";


/*
|--------------------------------------------------------------------------
| 2. RECOGEMOS EL ID QUE LLEGA POR GET
|--------------------------------------------------------------------------
|
| Si visitamos:
|
|     producto.php?id=3
|
| PHP tendrá aproximadamente:
|
|     $_GET["id"] = "3";
|
| Utilizamos el operador ?? (fusión de null) para evitar problemas si
| el parámetro "id" no existe.
|
| Es decir:
|
|     $_GET["id"] ?? ""
|
| significa:
|
|     "Si existe $_GET['id'], utiliza su valor.
|      Si no existe, utiliza una cadena vacía."
|
| Lo llamamos $idBruto porque todavía NO lo hemos validado.
|
*/

$idBruto = $_GET["id"] ?? "";


/*
|--------------------------------------------------------------------------
| 3. VALIDAMOS EL ID
|--------------------------------------------------------------------------
|
| filter_var() permite validar o filtrar valores.
|
| FILTER_VALIDATE_INT indica que queremos comprobar si el valor
| representa un número entero válido.
|
| Ejemplo:
|
|     $idBruto = "3";
|
|     filter_var("3", FILTER_VALIDATE_INT)
|
| devuelve:
|
|     3
|
| como número entero.
|
| Pero:
|
|     filter_var("hola", FILTER_VALIDATE_INT)
|
| devuelve:
|
|     false
|
| Por eso $id puede contener:
|
|     int     -> si el dato es válido
|     false   -> si el dato NO es un entero válido
|
*/

$id = filter_var($idBruto, FILTER_VALIDATE_INT);


/*
|--------------------------------------------------------------------------
| 4. PREPARAMOS LAS VARIABLES QUE UTILIZAREMOS DESPUÉS
|--------------------------------------------------------------------------
|
| Inicialmente todavía no tenemos ningún producto.
|
| Por eso:
|
|     $producto = null;
|
| null significa que actualmente no tenemos un producto asignado.
|
| También preparamos una variable para almacenar un posible mensaje
| de error.
|
| Al principio no existe ningún error, así que contiene una cadena vacía.
|
*/

$producto = null;
$error = "";


/*
|--------------------------------------------------------------------------
| 5. COMPROBAMOS SI EL ID ES VÁLIDO
|--------------------------------------------------------------------------
|
| Hay dos situaciones que consideramos incorrectas:
|
|     $id === false
|
| El dato recibido no representa un entero válido.
|
| Por ejemplo:
|
|     producto.php?id=hola
|
|
| O bien:
|
|     $id < 1
|
| Es un entero, pero no aceptamos identificadores menores que 1.
|
| Por ejemplo:
|
|     producto.php?id=-3
|
|
| Utilizamos ||, que significa OR ("o").
|
| Por tanto:
|
|     if ($id === false || $id < 1)
|
| significa:
|
|     "Si el ID no es un entero válido
|      O
|      el ID es menor que 1..."
|
*/

if ($id === false || $id < 1) {

    /*
     * Indicamos que la petición realizada por el cliente
     * contiene datos incorrectos.
     *
     * HTTP 400 = Bad Request
     */

    http_response_code(400);

    /*
     * Guardamos el mensaje que podremos mostrar posteriormente
     * en el HTML.
     */

    $error = "El id de producto no es válido";

} else {

    /*
    |--------------------------------------------------------------------------
    | 6. EL ID ES VÁLIDO: BUSCAMOS EL PRODUCTO
    |--------------------------------------------------------------------------
    |
    | Si hemos llegado hasta aquí sabemos que $id contiene un entero
    | válido y mayor o igual que 1.
    |
    | Ahora buscamos dentro del array $productos.
    |
    | La función buscarProductoPorId() recibe:
    |
    |     1. El array de productos.
    |     2. El ID que queremos encontrar.
    |
    | Por ejemplo:
    |
    |     buscarProductoPorId($productos, 3);
    |
    | La función puede devolver:
    |
    |     array  -> si encuentra el producto.
    |
    |     null   -> si no encuentra ningún producto con ese ID.
    |
    */

    $producto = buscarProductoPorId($productos, $id);


    /*
    |--------------------------------------------------------------------------
    | 7. COMPROBAMOS SI EL PRODUCTO EXISTE
    |--------------------------------------------------------------------------
    |
    | Si buscarProductoPorId() devuelve null significa:
    |
    |     "El ID era válido, pero no existe ese producto."
    |
    | Por ejemplo:
    |
    |     producto.php?id=999
    |
    | 999 puede ser perfectamente un entero válido.
    |
    | Pero si no existe ningún producto con id 999, devolvemos:
    |
    |     HTTP 404 = Not Found
    |
    */

    if ($producto === null) {

        http_response_code(404);

        $error = "El producto no existe";
    }
}

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Producto - DWES Store</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>

<header class="cabecera">
    <div class="contenedor">
        <h1>DWES Store</h1>

        <nav class="navegacion">
            <a href="index.php">Inicio</a>
            <a href="buscar.php">Buscar</a>
            <a href="compra.php">Comprar</a>
        </nav>
    </div>
</header>
<main class="contenedor">

    <?php
    /*
    |--------------------------------------------------------------------------
    | MOSTRAR ERROR O MOSTRAR PRODUCTO
    |--------------------------------------------------------------------------
    |
    | En la parte PHP anterior de la página hemos preparado dos variables
    | importantes:
    |
    |     $error
    |     $producto
    |
    | La variable $error comienza normalmente como una cadena vacía:
    |
    |     $error = "";
    |
    | Si durante el procesamiento ocurre algún problema, guardamos dentro
    | de ella un mensaje.
    |
    | Por ejemplo:
    |
    |     $error = "El producto no existe";
    |
    | Por tanto, en esta parte del HTML tenemos que decidir:
    |
    |     ¿Ha ocurrido algún error?
    |
    |             |
    |       +-----+-----+
    |       |           |
    |      SÍ          NO
    |       |           |
    |       v           v
    |   mostramos    mostramos
    |    el error    el producto
    |
    |
    | Para tomar esta decisión utilizamos un if de PHP.
    |
    | En lugar de utilizar la sintaxis tradicional:
    |
    |     if (condicion) {
    |
    |     } else {
    |
    |     }
    |
    | utilizamos la SINTAXIS ALTERNATIVA de PHP:
    |
    |     if (condicion):
    |
    |     else:
    |
    |     endif;
    |
    | Esta sintaxis resulta especialmente cómoda cuando mezclamos
    | PHP con HTML.
    |
    |--------------------------------------------------------------------------
    */
    ?>


    <?php if ($error !== ""): ?>

        <?php
        /*
        |--------------------------------------------------------------------------
        | CASO 1: EXISTE UN ERROR
        |--------------------------------------------------------------------------
        |
        | La condición:
        |
        |     $error !== ""
        |
        | significa:
        |
        |     "¿El contenido de $error es diferente de una cadena vacía?"
        |
        | !== es el operador de desigualdad estricta.
        |
        | Por ejemplo:
        |
        |     $error = "";
        |
        |     $error !== ""      -> false
        |
        | No entraríamos en este bloque.
        |
        |
        | Pero:
        |
        |     $error = "El producto no existe";
        |
        |     $error !== ""      -> true
        |
        | Entraríamos en este bloque y mostraríamos el error.
        |
        |--------------------------------------------------------------------------
        */
        ?>

        <section class="panel">

            <h2>Error</h2>

            <?php
            /*
            |--------------------------------------------------------------------------
            | MOSTRAMOS EL MENSAJE DE ERROR
            |--------------------------------------------------------------------------
            |
            | <?= ... ?> es la sintaxis corta de echo.
            |
            | Es decir:
            |
            |     <?= escapar($error) ?>
            |
            | equivale aproximadamente a:
            |
            |     <?php echo escapar($error); ?>
            |
            |
            | Antes de mostrar el contenido utilizamos:
            |
            |     escapar($error)
            |
            | Esta función prepara los caracteres especiales para mostrarlos
            | correctamente como texto dentro del HTML.
            |
            | Es una buena práctica escapar el texto dinámico justo en el
            | momento en el que lo vamos a introducir en HTML.
            |
            | Podemos recordar la regla:
            |
            |     VALIDAR AL ENTRAR
            |     ESCAPAR AL SALIR
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <p><?= escapar($error) ?></p>


            <?php
            /*
             * Este enlace permite al usuario regresar a la página principal.
             *
             * Como href="index.php" es un valor escrito directamente por
             * nosotros y no procede del usuario, no necesitamos escapar nada.
             */
            ?>

            <a class="boton" href="index.php">Volver</a>

        </section>


    <?php else: ?>

        <?php
        /*
        |--------------------------------------------------------------------------
        | CASO 2: NO EXISTE NINGÚN ERROR
        |--------------------------------------------------------------------------
        |
        | Si hemos llegado al else significa que:
        |
        |     $error === ""
        |
        | Es decir, no tenemos ningún mensaje de error.
        |
        | En nuestro flujo del programa esto significa que previamente:
        |
        |     1. El ID recibido era válido.
        |
        |     2. Hemos buscado el producto.
        |
        |     3. El producto existe.
        |
        | Por tanto podemos utilizar:
        |
        |     $producto
        |
        | para generar su ficha.
        |
        |--------------------------------------------------------------------------
        */
        ?>


        <article class="producto">

            <?php
            /*
            |--------------------------------------------------------------------------
            | NOMBRE DEL PRODUCTO
            |--------------------------------------------------------------------------
            |
            | $producto es un array asociativo.
            |
            | Por ejemplo:
            |
            |     $producto = [
            |         "nombre"    => "Teclado mecánico",
            |         "categoria" => "Periféricos",
            |         "precio"    => 7990,
            |         "stock"     => 7
            |     ];
            |
            | Para acceder al nombre utilizamos:
            |
            |     $producto["nombre"]
            |
            | Como el nombre es TEXTO dinámico que vamos a introducir dentro
            | del HTML, utilizamos escapar().
            |
            | Si el nombre fuese:
            |
            |     Teclado <Pro>
            |
            | escapar() evita que <Pro> sea interpretado como una etiqueta
            | HTML.
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <h2><?= escapar($producto["nombre"]) ?></h2>


            <?php
            /*
            |--------------------------------------------------------------------------
            | CATEGORÍA DEL PRODUCTO
            |--------------------------------------------------------------------------
            |
            | Hacemos exactamente lo mismo con la categoría.
            |
            | Obtenemos:
            |
            |     $producto["categoria"]
            |
            | y como es una cadena de texto que vamos a insertar en HTML,
            | la escapamos antes de mostrarla.
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <p>
                Categoría: <?= escapar($producto["categoria"]) ?>
            </p>


            <?php
            /*
            |--------------------------------------------------------------------------
            | PRECIO DEL PRODUCTO
            |--------------------------------------------------------------------------
            |
            | El precio está almacenado como un valor numérico.
            |
            | No queremos mostrar directamente algo como:
            |
            |     7990
            |
            | Queremos mostrarlo con un formato adecuado:
            |
            |     79,90 €
            |
            | Para eso utilizamos nuestra función:
            |
            |     formatearPrecio(...)
            |
            | Le pasamos el precio:
            |
            |     $producto["precio"]
            |
            | y la función devuelve el precio preparado para mostrarlo.
            |
            | Por ejemplo:
            |
            |     7990
            |       |
            |       v
            |     formatearPrecio(7990)
            |       |
            |       v
            |     "79,90 €"
            |
            | En el proyecto original este campo se llamaba:
            |
            |     precio_centimos
            |
            | Si sigues utilizando aquella estructura de datos deberías usar:
            |
            |     $producto["precio_centimos"]
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <p class="precio">
                <?= formatearPrecio($producto["precio"]) ?>
            </p>


            <?php
            /*
            |--------------------------------------------------------------------------
            | STOCK DEL PRODUCTO
            |--------------------------------------------------------------------------
            |
            | Obtenemos el número de unidades disponibles:
            |
            |     $producto["stock"]
            |
            | Por ejemplo:
            |
            |     7
            |
            | En este caso sabemos que stock contiene un valor numérico
            | controlado por nuestra aplicación.
            |
            | Por eso podemos mostrar directamente:
            |
            |     <?= $producto["stock"] ?>
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <p>
                Stock: <?= $producto["stock"] ?>
            </p>


            <?php
            /*
            |--------------------------------------------------------------------------
            | ESTADO VISUAL DEL STOCK
            |--------------------------------------------------------------------------
            |
            | Aquí hacemos dos cosas diferentes utilizando el mismo stock:
            |
            |     1. Decidimos qué CLASE CSS utilizar.
            |
            |     2. Decidimos qué TEXTO mostrar.
            |
            |
            | Por ejemplo, imaginemos:
            |
            |     stock = 7
            |
            | Nuestra función:
            |
            |     obtenerClaseEstado(7)
            |
            | puede devolver:
            |
            |     "disponible"
            |
            | Y:
            |
            |     obtenerEstadoStock(7)
            |
            | puede devolver:
            |
            |     "Disponible"
            |
            |
            | De esta forma PHP podría terminar generando:
            |
            |     <p class="estado disponible">
            |         Estado: Disponible
            |     </p>
            |
            |
            | Si el stock fuese bajo:
            |
            |     obtenerClaseEstado(3)
            |
            | podría devolver:
            |
            |     "aviso"
            |
            | y PHP generaría:
            |
            |     <p class="estado aviso">
            |         Estado: Últimas unidades
            |     </p>
            |
            |
            | IMPORTANTE:
            |
            | PHP NO está cambiando el CSS.
            |
            | PHP simplemente está GENERANDO el nombre de una clase CSS.
            |
            | Después será el navegador quien aplique las reglas CSS
            | correspondientes a esa clase.
            |
            |--------------------------------------------------------------------------
            */
            ?>

            <p class="estado <?= obtenerClaseEstado($producto["stock"]); ?>">

                Estado:

                <?=
                    /*
                     * Llamamos a obtenerEstadoStock() para obtener el texto
                     * correspondiente al número de unidades disponibles.
                     *
                     * Ejemplos:
                     *
                     *     stock = 0  -> "Agotado"
                     *
                     *     stock = 3  -> "Últimas unidades"
                     *
                     *     stock = 7  -> "Disponible"
                     */
                    obtenerEstadoStock($producto["stock"]);
                ?>

            </p>

        </article>


    <?php
    /*
    |--------------------------------------------------------------------------
    | FIN DEL IF
    |--------------------------------------------------------------------------
    |
    | endif; cierra el if que comenzamos anteriormente:
    |
    |     if ($error !== ""):
    |
    |         mostramos error
    |
    |     else:
    |
    |         mostramos producto
    |
    |     endif;
    |
    | La gran ventaja es que SOLO se muestra uno de los dos bloques.
    |
    | Nunca mostramos simultáneamente:
    |
    |     ERROR + PRODUCTO
    |
    | El flujo es:
    |
    |                         $error
    |                            |
    |                            v
    |                       ¿está vacío?
    |                            |
    |                  +---------+---------+
    |                  |                   |
    |                 NO                  SÍ
    |                  |                   |
    |                  v                   v
    |          <section> Error      <article> Producto
    |
    |--------------------------------------------------------------------------
    */
    ?>

    <?php endif; ?>

</main>

</body>
</html>