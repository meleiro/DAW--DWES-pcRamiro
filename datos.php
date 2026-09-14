<?php

/*
|--------------------------------------------------------------------------
| ARCHIVO DE DATOS DE LA APLICACIÓN
|--------------------------------------------------------------------------
|
| En este archivo vamos a almacenar información que utilizará nuestra
| aplicación:
|
|   1. Constantes de configuración.
|   2. Un array con los productos de la tienda.
|
| Este archivo contiene únicamente código PHP.
|
| La etiqueta:
|
|     <?php
|
| indica al intérprete de PHP que a partir de este punto comienza
| código PHP.
|
| Todo lo que escribamos después será procesado por PHP en el servidor.
|
*/


/*
|--------------------------------------------------------------------------
| CONSTANTES
|--------------------------------------------------------------------------
|
| Una CONSTANTE es un valor al que damos un nombre y que, una vez
| definido, no debería cambiar durante la ejecución del programa.
|
| Para declarar una constante podemos utilizar la palabra reservada:
|
|     const
|
| Sintaxis:
|
|     const NOMBRE_CONSTANTE = valor;
|
| Por convención, los nombres de las constantes suelen escribirse
| utilizando MAYÚSCULAS y guiones bajos.
|
| Ejemplos:
|
|     IVA_GENERAL
|     UNIDADES_DESCUENTO
|     DESCUENTO_CANTIDAD
|
| A diferencia de las variables, las constantes NO llevan el símbolo $.
|
| Variable:
|
|     $precio = 100;
|
| Constante:
|
|     const IVA_GENERAL = 0.21;
|
*/


/*
|--------------------------------------------------------------------------
| IVA GENERAL
|--------------------------------------------------------------------------
|
| Definimos una constante llamada IVA_GENERAL.
|
|     const
|
| indica que estamos declarando una constante.
|
|     IVA_GENERAL
|
| es el nombre de la constante.
|
|     =
|
| es el operador de asignación.
|
|     0.21
|
| es el valor almacenado.
|
| Como contiene decimales, PHP lo tratará como un número de tipo:
|
|     float
|
| 0.21 representa un 21 %.
|
| Matemáticamente:
|
|     21 / 100 = 0.21
|
| Ejemplo de utilización:
|
|     $precio = 100;
|
|     $iva = $precio * IVA_GENERAL;
|
| El resultado sería:
|
|     21
|
| IMPORTANTE:
|
| Para utilizar la constante escribimos:
|
|     IVA_GENERAL
|
| y NO:
|
|     $IVA_GENERAL
|
| porque el símbolo $ solamente se utiliza con variables.
|
*/

const IVA_GENERAL = 0.21;


/*
|--------------------------------------------------------------------------
| UNIDADES NECESARIAS PARA APLICAR DESCUENTO
|--------------------------------------------------------------------------
|
| Esta constante establece a partir de cuántas unidades queremos
| aplicar un descuento.
|
| Su valor es:
|
|     5
|
| Como es un número entero, su tipo es:
|
|     int
|
| Posteriormente podríamos utilizarla en una condición:
|
|     if ($unidades >= UNIDADES_DESCUENTO) {
|         // aplicar descuento
|     }
|
| El operador:
|
|     >=
|
| significa:
|
|     "mayor o igual que"
|
| Por tanto, si el cliente compra 5, 6, 7... unidades,
| la condición será verdadera.
|
*/

const UNIDADES_DESCUENTO = 5;


/*
|--------------------------------------------------------------------------
| PORCENTAJE DE DESCUENTO
|--------------------------------------------------------------------------
|
| Esta constante representa un descuento del 10 %.
|
|     10 / 100 = 0.10
|
| El valor 0.10 es de tipo:
|
|     float
|
| Podríamos utilizarlo así:
|
|     $subtotal = 10000;
|
|     $descuento = $subtotal * DESCUENTO_CANTIDAD;
|
| Si $subtotal fuese 10000:
|
|     10000 * 0.10 = 1000
|
| El descuento sería 1000.
|
| En nuestro proyecto, como trabajamos con precios en céntimos:
|
|     10000 céntimos = 100 €
|     1000 céntimos  = 10 €
|
*/

const DESCUENTO_CANTIDAD = 0.10;


/*
|--------------------------------------------------------------------------
| VARIABLE $productos
|--------------------------------------------------------------------------
|
| Ahora creamos una VARIABLE llamada:
|
|     $productos
|
| En PHP todas las variables comienzan con el símbolo:
|
|     $
|
| Ejemplos:
|
|     $nombre
|     $precio
|     $stock
|     $productos
|
| La instrucción:
|
|     $productos = ...
|
| significa:
|
|     "asigna a la variable $productos el valor que aparece
|      a la derecha del signo ="
|
| El operador:
|
|     =
|
| es el OPERADOR DE ASIGNACIÓN.
|
| No debemos confundirlo con:
|
|     ==
|
| comparación de valores
|
| ni con:
|
|     ===
|
| comparación estricta de valor y tipo.
|
*/


/*
|--------------------------------------------------------------------------
| ARRAYS EN PHP
|--------------------------------------------------------------------------
|
| Queremos almacenar VARIOS productos dentro de una única variable.
|
| Para ello utilizamos un:
|
|     array
|
| Un array permite almacenar varios valores relacionados.
|
| La sintaxis moderna para crear un array en PHP utiliza:
|
|     [ ]
|
| Por ejemplo:
|
|     $numeros = [10, 20, 30];
|
| En nuestro caso:
|
|     $productos = [
|         ...
|     ];
|
| significa que $productos contiene un array.
|
| Pero hay algo más interesante:
|
| cada elemento de $productos será, a su vez, OTRO ARRAY.
|
| Por tanto tenemos una estructura:
|
|     ARRAY
|       ├── ARRAY producto 1
|       └── ARRAY producto 2
|
| Es decir, tenemos un ARRAY DE ARRAYS.
|
*/

$productos = [

    /*
    |--------------------------------------------------------------------------
    | PRIMER PRODUCTO
    |--------------------------------------------------------------------------
    |
    | Abrimos:
    |
    |     [
    |
    | porque comenzamos otro array.
    |
    | Este array representa UN producto.
    |
    | Utilizamos un ARRAY ASOCIATIVO.
    |
    | En un array asociativo cada dato tiene:
    |
    |     clave => valor
    |
    | Por ejemplo:
    |
    |     "nombre" => "Teclado"
    |
    | La clave es:
    |
    |     "nombre"
    |
    | y el valor asociado es:
    |
    |     "Teclado"
    |
    | El operador:
    |
    |     =>
    |
    | relaciona una CLAVE con un VALOR dentro de un array.
    |
    | Podemos leerlo mentalmente como:
    |
    |     "nombre" apunta a "Teclado"
    |
    */

    [
        /*
         * CLAVE: "id"
         *
         * VALOR: 1
         *
         * El valor 1 es un entero:
         *
         *     int
         *
         * El identificador nos permite distinguir un producto
         * de los demás.
         *
         * Posteriormente podremos acceder a este dato mediante:
         *
         *     $producto["id"]
         */

        "id" => 1,


        /*
         * CLAVE:
         *
         *     "nombre"
         *
         * VALOR:
         *
         *     "Teclado"
         *
         * Como el valor está entre comillas, es una cadena:
         *
         *     string
         *
         * Para acceder posteriormente:
         *
         *     $producto["nombre"]
         *
         * obtendríamos:
         *
         *     "Teclado"
         */

        "nombre" => "Teclado",


        /*
         * La categoría también es un string.
         *
         * PHP trabaja perfectamente con caracteres UTF-8 como:
         *
         *     é
         *
         * siempre que nuestros archivos estén correctamente
         * guardados utilizando UTF-8.
         */

        "categoria" => "Periféricos",


        /*
         * PRECIO
         * ---------------------------------------------------------------
         *
         * El precio se almacena como:
         *
         *     7990
         *
         * y NO como:
         *
         *     79.90
         *
         * En este proyecto hemos decidido almacenar el dinero
         * utilizando CÉNTIMOS ENTEROS.
         *
         * Por tanto:
         *
         *     7990 céntimos
         *
         * equivalen a:
         *
         *     79,90 €
         *
         * Esto significa que el tipo de dato de "precio" es:
         *
         *     int
         *
         * Esta estrategia evita muchos de los problemas de precisión
         * que pueden aparecer al realizar cálculos monetarios utilizando
         * números de punto flotante.
         */

        "precio" => 7990,


        /*
         * STOCK
         * ---------------------------------------------------------------
         *
         * Representa el número de unidades disponibles.
         *
         * Como 7 es un número entero:
         *
         *     int
         *
         * podremos realizar operaciones y comparaciones:
         *
         *     $producto["stock"] > 0
         *
         *     $producto["stock"] <= 5
         *
         * etc.
         */

        "stock" => 7
    ],


    /*
    |--------------------------------------------------------------------------
    | LA COMA ENTRE ELEMENTOS
    |--------------------------------------------------------------------------
    |
    | Observa:
    |
    |     ],
    |
    | Cerramos el array del primer producto con:
    |
    |     ]
    |
    | y escribimos una coma:
    |
    |     ,
    |
    | porque después viene otro elemento del array $productos.
    |
    | Podemos imaginar:
    |
    |     $productos = [
    |
    |         producto1,
    |         producto2
    |
    |     ];
    |
    */


    /*
    |--------------------------------------------------------------------------
    | SEGUNDO PRODUCTO
    |--------------------------------------------------------------------------
    |
    | Creamos otro array asociativo con exactamente la misma estructura.
    |
    | Es decir, todos nuestros productos tienen:
    |
    |     id
    |     nombre
    |     categoria
    |     precio
    |     stock
    |
    | Esto es muy útil porque posteriormente podremos recorrer
    | todos los productos utilizando foreach.
    |
    | NOTA:
    |
    | En el código original este segundo producto tiene exactamente
    | los mismos datos y también:
    |
    |     "id" => 1
    |
    | PHP permite hacerlo; no es un error de sintaxis.
    |
    | Sin embargo, en una aplicación real normalmente queremos que
    | cada producto tenga un identificador único.
    |
    | Por ejemplo:
    |
    |     producto 1 → id 1
    |     producto 2 → id 2
    |     producto 3 → id 3
    |
    */

    [
        "id" => 1,
        "nombre" => "Teclado",
        "categoria" => "Periféricos",
        "precio" => 7990,
        "stock" => 7
    ]

];


/*
|--------------------------------------------------------------------------
| IMPORTANTE SOBRE ?>
|--------------------------------------------------------------------------
|
| En un archivo que contiene solamente PHP, como datos.php,
| normalmente NO necesitamos escribir la etiqueta de cierre:
|
|     ?>
|
| De hecho, en proyectos PHP es habitual omitirla.
|
| El archivo podría terminar directamente después del código PHP.
|
| Esto ayuda a evitar que espacios o saltos de línea accidentales
| situados después de ?> sean enviados al navegador.
|
| Por tanto, para datos.php sería perfectamente correcto terminar
| el archivo aquí, sin escribir ?>.
|
*/