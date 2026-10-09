
<?php

/*
===============================================================================
ARCHIVO: compra.php
ASIGNATURA: DESARROLLO WEB EN ENTORNO SERVIDOR (DWES)
===============================================================================

OBJETIVO DEL ARCHIVO
--------------------

Este archivo se encarga de recibir los datos de un formulario de compra,
comprobar que sean correctos y calcular el importe correspondiente.

En este ejemplo NO estamos guardando una compra en una base de datos ni
realizando un pago real.

Únicamente estamos:

    1. Recibiendo información del usuario.
    2. Validando los datos recibidos.
    3. Comprobando que el producto exista.
    4. Comprobando que haya suficiente stock.
    5. Calculando los importes de la compra.
    6. Preparando las variables que después utilizaremos en el HTML.


===============================================================================
¿CÓMO FUNCIONA EL PROGRAMA?
===============================================================================

El usuario accede a la página de compra.

Inicialmente, el navegador realiza normalmente una petición GET.

El servidor ejecuta compra.php y muestra el formulario.

El usuario introduce:

    - Su nombre.
    - Su correo electrónico.
    - El producto que quiere comprar.
    - El número de unidades.

Cuando pulsa el botón de envío, el navegador realiza una petición POST.

Entonces PHP recibe los datos mediante el array superglobal:

    $_POST

El programa comprueba que los datos sean correctos.

Si encuentra errores:

    Los guarda en el array $errores.

Si no encuentra errores:

    Llama a calcularCompra() y prepara el resultado.


===============================================================================
ESQUEMA GENERAL
===============================================================================

                  USUARIO
                     |
                     v
              FORMULARIO HTML
                     |
                     | Pulsa "Enviar"
                     v
               PETICIÓN POST
                     |
                     v
                compra.php
                     |
                     v
             LEER DATOS $_POST
                     |
                     v
                VALIDACIONES
                     |
                     v
              ¿EXISTEN ERRORES?
                     |
              +------+------+
              |             |
             SÍ             NO
              |             |
              v             v
        GUARDAR ERRORES   CALCULAR COMPRA
              |             |
              v             v
        MOSTRAR ERRORES   MOSTRAR RESULTADO


===============================================================================
CONCEPTOS DE PHP QUE UTILIZAREMOS
===============================================================================

    require_once
    Variables
    Arrays
    Arrays asociativos
    Arrays superglobales
    $_POST
    $_SERVER
    if / else
    Operadores ===, !==, || y &&
    Operador ??
    filter_var()
    FILTER_VALIDATE_EMAIL
    FILTER_VALIDATE_INT
    Funciones
    Valores null, true y false
    Comparaciones
    Acumulación de errores en un array

===============================================================================
*/


/*
===============================================================================
1. CARGAR LOS ARCHIVOS NECESARIOS
===============================================================================

require_once permite incorporar otro archivo PHP.

En este proyecto tenemos dos archivos fundamentales:

    datos.php

        Contiene los productos de nuestra tienda.

        Por ejemplo:

        $productos = [
            [
                "id" => 1,
                "nombre" => "Teclado mecánico",
                "precio" => 7990,
                "stock" => 7
            ]
        ];


    funciones.php

        Contiene funciones que vamos a reutilizar.

        Por ejemplo:

            leerCadena()

            buscarProductoPorId()

            calcularCompra()


¿Por qué utilizamos require_once y no copiamos todo aquí?

Porque queremos separar responsabilidades:

    datos.php       -> DATOS

    funciones.php   -> FUNCIONES REUTILIZABLES

    compra.php      -> LÓGICA DE LA COMPRA


La palabra "once" significa "una vez".

Si el archivo ya se ha incluido anteriormente en la misma ejecución,
PHP no vuelve a incluirlo.

Además, si el archivo requerido no existe o no puede cargarse,
PHP produce un error que impide continuar normalmente la ejecución.
===============================================================================
*/

require_once "datos.php";
require_once "funciones.php";


/*
===============================================================================
2. INICIALIZACIÓN DE VARIABLES
===============================================================================

Antes de procesar el formulario vamos a preparar todas las variables
que necesitaremos.

Esto es importante porque el mismo archivo puede ejecutarse en dos
situaciones:

    A) El usuario acaba de entrar y todavía no ha enviado el formulario.

    B) El usuario ya ha enviado el formulario mediante POST.


Queremos que las variables tengan un valor inicial conocido.

Así evitamos trabajar con variables que todavía no existen.
===============================================================================
*/


/*
$errores será un ARRAY que almacenará los mensajes de error.

Inicialmente:

    $errores = [];

Significa:

    "Todavía no hemos encontrado ningún error".


Si posteriormente encontramos un problema, añadiremos un mensaje:

    $errores[] = "El nombre es obligatorio";


Podemos acumular varios errores:

    $errores = [
        "El nombre es obligatorio",
        "El correo electrónico no es válido",
        "No hay stock suficiente"
    ];


¿Por qué un array y no una única variable de texto?

Porque queremos poder mostrar TODOS los errores encontrados
y no solamente el último.
*/

$errores = [];


/*
$compraRealizada es una variable de tipo BOOLEANO.

Los booleanos solamente pueden tener dos valores:

    true
    false


Inicialmente la compra NO se ha calculado correctamente:

    false


Cuando todas las validaciones sean correctas, cambiaremos su valor:

    $compraRealizada = true;


Después, en el HTML, podremos utilizar:

    if ($compraRealizada)

para decidir si mostramos el resumen de compra.

IMPORTANTE:

En este ejercicio "compra realizada" significa que el cálculo se ha
completado correctamente.

No significa que se haya efectuado un pago real.
*/

$compraRealizada = false;


/*
$resultadoCompra almacenará el resultado devuelto por calcularCompra().

Inicialmente:

    null

significa que todavía no tenemos ningún resultado.


Cuando realicemos el cálculo, esta variable podrá contener un array
asociativo con los importes:

    [
        "subtotal" => ...,
        "descuento" => ...,
        "base" => ...,
        "iva" => ...,
        "total" => ...
    ]


Los valores exactos dependerán de la función calcularCompra().
*/

$resultadoCompra = null;


/*
$productoSeleccionado almacenará el producto que quiere comprar
el usuario.

Inicialmente no tenemos ninguno:

    null


Después podremos guardar un producto encontrado en el array $productos.

Por ejemplo:

    [
        "id" => 1,
        "nombre" => "Teclado mecánico",
        "precio" => 7990,
        "stock" => 7
    ]


IMPORTANTE:

null NO es lo mismo que un array vacío.

Aquí utilizamos null para representar:

    "Todavía no hemos encontrado ningún producto".
*/

$productoSeleccionado = null;


/*
Variables donde almacenaremos los datos del formulario.

$nombre y $email comienzan como cadenas vacías.

Esto permite que, cuando mostremos el formulario por primera vez,
los campos aparezcan inicialmente vacíos.
*/

$nombre = "";
$email = "";


/*
$unidades comienza con el valor 1.

¿Por qué?

Porque queremos que, por defecto, el usuario solicite una unidad.

También podremos utilizar esta variable para rellenar el atributo
value de un input HTML:

    <input type="number" name="unidades" value="1">
*/

$unidades = 1;


/*
$productoId almacenará el identificador del producto seleccionado.

Inicialmente:

    null

porque todavía no sabemos qué producto quiere comprar el usuario.

Más adelante, si recibimos un ID válido, lo guardaremos aquí.
*/

$productoId = null;


/*
===============================================================================
3. COMPROBAR SI EL FORMULARIO SE HA ENVIADO
===============================================================================

Un archivo PHP puede recibir peticiones utilizando diferentes
métodos HTTP.

Los más habituales en este nivel son:

    GET
    POST


GET:

    Se utiliza habitualmente para solicitar información.

    Ejemplo:

        compra.php

    o:

        producto.php?id=2


POST:

    Se utiliza habitualmente para enviar datos que deben procesarse.

    Por ejemplo, al enviar un formulario de compra.


En HTML tendríamos:

    <form action="compra.php" method="POST">

        ...

        <button type="submit">
            Calcular compra
        </button>

    </form>


Cuando el usuario pulsa el botón, el navegador envía una petición POST.


===============================================================================
¿QUÉ ES $_SERVER?
===============================================================================

$_SERVER es un ARRAY SUPERGLOBAL de PHP.

Contiene información sobre la petición y el entorno del servidor.

Una de sus claves es:

    $_SERVER["REQUEST_METHOD"]

que indica el método HTTP utilizado.


Por ejemplo:

    "GET"

    "POST"


La condición:

    $_SERVER["REQUEST_METHOD"] === "POST"

pregunta:

    "¿El método de esta petición es exactamente POST?"


=== significa IGUALDAD ESTRICTA.

Compara tanto el valor como el tipo.


Si la condición es verdadera:

    Procesamos el formulario.


Si es falsa:

    No ejecutamos este bloque de validación.


IMPORTANTE:

No debemos intentar validar los campos de un formulario
antes de que el usuario lo haya enviado.
===============================================================================
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    /*
    ===========================================================================
    4. VALIDACIÓN DEL NOMBRE
    ===========================================================================

    Queremos obtener el nombre introducido por el usuario.

    En el formulario HTML tendremos algo parecido a:

        <input
            type="text"
            name="nombre"
        >


    El atributo name="nombre" es fundamental.

    Gracias a él, PHP puede acceder al valor mediante:

        $_POST["nombre"]


    En nuestro proyecto utilizamos una función auxiliar:

        leerCadena($_POST, "nombre")


    Esta función se encarga de:

        1. Buscar la clave "nombre" dentro de $_POST.

        2. Comprobar que el valor sea una cadena.

        3. Eliminar espacios al principio y al final.

        4. Devolver una cadena.


    Por ejemplo:

        "   Pepe   "

    se transforma en:

        "Pepe"


    Esto evita que un usuario pueda introducir solamente espacios
    para superar nuestra comprobación de campo obligatorio.
    ===========================================================================
    */

    $nombre = leerCadena($_POST, "nombre");


    /*
    Comprobamos si el nombre está vacío.

        $nombre === ""

    significa:

        "¿El nombre es exactamente una cadena vacía?"


    Ejemplos:

        $nombre = "Pepe"   -> false

        $nombre = ""       -> true


    Si está vacío, añadimos un mensaje al array $errores.
    */

    if ($nombre === "") {

        /*
        La expresión:

            $errores[]

        significa:

            "Añade un nuevo elemento al final del array".


        No necesitamos indicar una posición concreta.

        PHP añade el elemento automáticamente.


        Por ejemplo:

            $errores = [];

            $errores[] = "Falta el nombre";


        Resultado:

            [
                "Falta el nombre"
            ]
        */

        $errores[] = "Debes de introducir un nombre";

    }


    /*
    ===========================================================================
    5. VALIDACIÓN DEL CORREO ELECTRÓNICO
    ===========================================================================

    Ahora necesitamos obtener y validar el email del usuario.

    En HTML tendremos:

        <input
            type="email"
            name="email"
        >


    Primero leemos el dato:

        leerCadena($_POST, "email")


    Lo almacenamos en una variable llamada:

        $emailBruto


    Utilizamos el nombre "Bruto" para indicar que todavía NO hemos
    comprobado si tiene un formato correcto.


    Es importante distinguir:

        DATO RECIBIDO

              |

              v

        DATO VALIDADO


    Que un dato llegue desde el formulario NO significa que sea válido.
    ===========================================================================
    */

    $emailBruto = leerCadena($_POST, "email");


    /*
    Utilizamos filter_var().

    filter_var() es una función incorporada en PHP que permite validar
    o filtrar datos.


    Su estructura general es:

        filter_var(VALOR, FILTRO);


    En este caso:

        VALOR:

            $emailBruto


        FILTRO:

            FILTER_VALIDATE_EMAIL


    FILTER_VALIDATE_EMAIL es una CONSTANTE PREDEFINIDA DE PHP.

    Indica que queremos comprobar si el valor tiene un formato
    sintácticamente válido de dirección de correo electrónico.


    Ejemplos:

        "pepe@gmail.com"    -> válido

        "profesor@centro.es" -> válido

        "pepe"              -> inválido

        "pepe@"             -> inválido

        "@gmail.com"        -> inválido


    ¿QUÉ DEVUELVE filter_var()?

    Si el formato es válido:

        Devuelve el email validado.


    Si el formato es incorrecto:

        Devuelve false.


    IMPORTANTE:

    Esto NO comprueba que el buzón exista realmente.

    Solamente valida el formato de la dirección.
    */

    $emailValidado = filter_var(
        $emailBruto,
        FILTER_VALIDATE_EMAIL
    );


    /*
    Comprobamos el resultado de la validación.

        $emailValidado === false


    significa:

        "¿La función ha indicado que el email es inválido?"


    Utilizamos === porque queremos comprobar exactamente el valor false.
    */

    if ($emailValidado === false) {

        /*
        Si el email no es válido, añadimos un nuevo error.

        No detenemos el programa inmediatamente.

        Queremos seguir comprobando los demás campos para poder
        mostrar varios errores juntos al usuario.
        */

        $errores[] = "Correo electrónico con formato incorrecto";

    } else {

        /*
        Si llegamos aquí, el formato del email es válido.

        Guardamos el resultado en la variable $email.

        Esta variable se podrá utilizar posteriormente para mostrar
        el resumen de compra.
        */

        $email = $emailValidado;

    }


    /*
    ===========================================================================
    6. VALIDACIÓN DEL IDENTIFICADOR DEL PRODUCTO
    ===========================================================================

    El usuario seleccionará un producto mediante un <select>.

    Por ejemplo:

        <select name="producto">

            <option value="">Selecciona un producto</option>

            <option value="1">Teclado mecánico</option>

            <option value="2">Ratón gaming</option>

        </select>


    Cuando el usuario selecciona el producto 2, el formulario envía:

        producto=2


    En PHP podemos acceder mediante:

        $_POST["producto"]


    IMPORTANTE:

    Los datos que llegan desde formularios suelen recibirse como
    cadenas de texto.

    Por ejemplo:

        "2"


    Pero nosotros queremos trabajar con un ID entero:

        2


    Además, no debemos confiar en que el usuario haya seleccionado
    necesariamente una de las opciones que aparecen en el HTML.

    Una petición HTTP puede modificarse manualmente.

    Por eso debemos validar el ID en el servidor.
    ===========================================================================
    */


    /*
    Primero obtenemos el dato:

        $_POST["producto"] ?? ""


    El operador ?? se llama OPERADOR DE FUSIÓN NULA.


    Significa:

        "Utiliza el valor de la izquierda si existe y no es null;
         en caso contrario, utiliza el valor de la derecha".


    Por ejemplo:

        $_POST["producto"] = "2"

    Resultado:

        "2"


    Pero si no existe la clave "producto":

        $_POST["producto"] ?? ""

    Resultado:

        ""


    Así evitamos intentar acceder directamente a una clave inexistente.


    Después aplicamos:

        FILTER_VALIDATE_INT


    Este filtro comprueba si el valor representa un número entero válido.


    Ejemplos:

        "2"       -> 2

        "15"      -> 15

        "hola"    -> false

        "3.5"     -> false


    IMPORTANTE:

    No es lo mismo validar que convertir mediante (int).

    Por ejemplo:

        (int) "hola"

    produce:

        0


    En cambio:

        filter_var("hola", FILTER_VALIDATE_INT)

    devuelve:

        false


    La validación nos permite detectar que el dato original
    no representaba un entero válido.
    */

    $productoIdValidado = filter_var(
        $_POST["producto"] ?? "",
        FILTER_VALIDATE_INT
    );


    /*
    Ahora comprobamos dos condiciones:

        $productoIdValidado === false

    O BIEN:

        $productoIdValidado < 1


    El operador || significa OR lógico.

    Basta con que UNA de las condiciones sea verdadera
    para entrar en el if.


    Primera condición:

        El valor no representa un entero válido.


    Segunda condición:

        El número es menor que 1.


    Ejemplos:

        "hola" -> inválido

        0      -> inválido

        -3     -> inválido

        2      -> válido en esta primera comprobación


    IMPORTANTE:

    Que el ID sea un entero positivo NO significa todavía
    que el producto exista realmente.
    */

    if (
        $productoIdValidado === false
        ||
        $productoIdValidado < 1
    ) {

        $errores[] = "Debes de seleccionar un producto válido";

    } else {


        /*
        Si llegamos al else, el ID tiene un formato válido
        y es mayor que cero.

        Lo guardamos en la variable $productoId.
        */

        $productoId = $productoIdValidado;


        /*
        Ahora tenemos que comprobar si ese ID corresponde
        a un producto real de nuestra tienda.


        Para ello llamamos a:

            buscarProductoPorId($productos, $productoId)


        Le pasamos DOS argumentos:

            1. $productos

               El array que contiene todos los productos.


            2. $productoId

               El identificador que queremos buscar.


        La función recorrerá los productos y buscará uno cuyo
        identificador coincida con el solicitado.


        Por ejemplo:

            buscarProductoPorId($productos, 2)


        Si encuentra el producto:

            Devuelve su array asociativo.


        Si no lo encuentra:

            Devuelve null.


        Por tanto, podemos distinguir:

            ID con formato correcto

        de:

            ID que realmente existe en la tienda.
        */

        $productoSeleccionado = buscarProductoPorId(
            $productos,
            $productoId
        );


        /*
        Comprobamos si la búsqueda ha devuelto null.

            === null

        significa que no hemos encontrado ningún producto.


        Ejemplo:

            ID = 999

        Puede ser un entero perfectamente válido.

        Pero si no existe un producto con ese ID:

            buscarProductoPorId(...)

        devolverá:

            null
        */

        if ($productoSeleccionado === null) {

            $errores[] = "El producto seleccionado no existe.";

        }

    }


    /*
    ===========================================================================
    7. VALIDACIÓN DEL NÚMERO DE UNIDADES
    ===========================================================================

    En el formulario tendremos algo parecido a:

        <input
            type="number"
            name="unidades"
            min="1"
        >


    El usuario debe indicar cuántas unidades quiere comprar.


    Queremos exigir dos condiciones:

        1. Que sea un número entero.

        2. Que sea mayor que cero.


    Ejemplos:

        1       -> válido

        3       -> válido

        10      -> válido

        0       -> inválido

        -2      -> inválido

        "hola"  -> inválido

        2.5     -> inválido


    IMPORTANTE:

    Aunque HTML tenga:

        type="number"

    y:

        min="1"

    debemos volver a validar el dato en PHP.

    Las restricciones del navegador NO sustituyen las
    comprobaciones del servidor.
    ===========================================================================
    */

    $unidadesValidadas = filter_var(
        $_POST["unidades"] ?? "",
        FILTER_VALIDATE_INT
    );


    /*
    Comprobamos:

        ¿No es un entero válido?

                O

        ¿Es menor que 1?


    Si alguna condición se cumple, añadimos un error.
    */

    if (
        $unidadesValidadas === false
        ||
        $unidadesValidadas < 1
    ) {

        /*
        CORRECCIÓN DIDÁCTICA:

        La condición permite comprar 1 unidad.

        Por eso el mensaje correcto es:

            "entero mayor que cero"

        y no:

            "entero mayor que uno".
        */

        $errores[] =
            "El número de unidades debe de ser un entero mayor que cero";

    } else {

        /*
        Si el valor es correcto, lo guardamos en $unidades.

        A partir de aquí sabemos que esta variable contiene
        un número entero positivo.
        */

        $unidades = $unidadesValidadas;

    }


    /*
    ===========================================================================
    8. COMPROBAR QUE HAY STOCK SUFICIENTE
    ===========================================================================

    Ya hemos validado:

        - El nombre.
        - El email.
        - El ID del producto.
        - El número de unidades.


    Ahora necesitamos aplicar una REGLA DE NEGOCIO:

        "No podemos vender más unidades de las que tenemos".


    Ejemplo:

        Producto: Teclado mecánico

        Stock disponible: 7


    Si el usuario solicita:

        3 unidades

    La cantidad solicitada no supera el stock.


    Pero si solicita:

        10 unidades

    Debemos mostrar un error.


    Para hacer esta comprobación necesitamos conocer:

        $productoSeleccionado["stock"]


    Sin embargo, debemos tener cuidado:

    ¿Qué ocurre si el producto no existe?

    En ese caso:

        $productoSeleccionado === null


    Y no podemos intentar acceder a:

        null["stock"]


    Por eso primero comprobamos que tenemos un producto.
    ===========================================================================
    */


    /*
    El operador && significa AND lógico.

    Para que la condición completa sea verdadera,
    deben cumplirse AMBAS condiciones.


    CONDICIÓN 1:

        $productoSeleccionado !== null

    Significa:

        "Hemos encontrado un producto".


    CONDICIÓN 2:

        $unidades > $productoSeleccionado["stock"]

    Significa:

        "El usuario solicita más unidades de las disponibles".


    Ejemplo:

        $unidades = 10

        $productoSeleccionado["stock"] = 7


        10 > 7

    Resultado:

        true


    PHP utiliza evaluación de cortocircuito con &&.

    Si la primera condición es falsa, no necesita evaluar la segunda.

    Esto evita acceder al stock cuando no tenemos un producto válido.
    */

    if (
        $productoSeleccionado !== null
        &&
        $unidades > $productoSeleccionado["stock"]
    ) {

        $errores[] = "No hay stock suficiente";

    }


    /*
    ===========================================================================
    9. COMPROBAR SI TODAS LAS VALIDACIONES HAN SIDO CORRECTAS
    ===========================================================================

    Hemos terminado de validar los campos.

    Durante las comprobaciones hemos ido guardando mensajes
    en el array $errores.


    Ahora tenemos dos situaciones posibles.


    SITUACIÓN A: HAY ERRORES
    -----------------------

        $errores = [
            "Debes de introducir un nombre",
            "Correo electrónico con formato incorrecto"
        ];


    No debemos calcular la compra.


    SITUACIÓN B: NO HAY ERRORES
    --------------------------

        $errores = [];


    Significa que todas las validaciones han sido superadas.


    Podemos realizar el cálculo.


    La condición:

        $errores === []

    pregunta:

        "¿El array $errores está exactamente vacío?"


    IMPORTANTE:

    No estamos comprobando un error concreto.

    Estamos comprobando que NO se haya producido NINGUNO.
    ===========================================================================
    */

    if ($errores === []) {


        /*
        =======================================================================
        10. CALCULAR LOS IMPORTES DE LA COMPRA
        =======================================================================

        Si llegamos aquí significa que:

            - El nombre no está vacío.

            - El email tiene un formato válido.

            - El ID del producto es válido.

            - El producto existe.

            - Las unidades son un entero positivo.

            - No se ha detectado falta de stock.


        Por tanto, ya podemos calcular la compra.


        Para ello utilizamos nuestra función:

            calcularCompra()


        Esta función recibe DOS argumentos:

            1. El precio del producto.

            2. El número de unidades.


        Por ejemplo:

            calcularCompra(7990, 3)


        Si los precios se almacenan en céntimos:

            7990 = 79,90 €


        La función se encargará de calcular los importes
        según las reglas definidas en funciones.php.


        IMPORTANTE:

        En este código estamos utilizando la clave:

            ["precio"]

        Si en datos.php el campo se llama:

            ["precio_centimos"]

        debemos utilizar exactamente ese nombre.

        Los nombres de las claves de un array asociativo
        deben coincidir con los que hemos definido.
        =======================================================================
        */

        $resultadoCompra = calcularCompra(
            $productoSeleccionado["precio"],
            $unidades
        );


        /*
        =======================================================================
        11. MARCAR QUE EL CÁLCULO SE HA COMPLETADO
        =======================================================================

        Una vez calculada la compra, cambiamos:

            $compraRealizada = false;

        por:

            $compraRealizada = true;


        Esta variable actuará como una BANDERA o INDICADOR.

        En programación, una bandera es una variable que nos permite
        recordar si ha ocurrido una determinada situación.


        Después, en el HTML, podremos escribir:

            if ($compraRealizada)

        para mostrar el resumen.


        Si es false:

            Mostraremos el formulario.


        Si es true:

            Mostraremos el resultado de la compra.


        RECUERDA:

        Aquí solamente hemos calculado los importes.

        No estamos registrando todavía un pedido real.
        =======================================================================
        */

        $compraRealizada = true;

    }


    /*
    ===========================================================================
    FIN DEL PROCESAMIENTO POST
    ===========================================================================

    Aquí termina el bloque que se ejecuta cuando el usuario
    ha enviado el formulario mediante POST.

    A partir de este punto ya tenemos preparadas las variables
    que necesitaremos para generar la respuesta HTML.
    ===========================================================================
    */

}


/*
===============================================================================
12. ¿QUÉ OCURRE DESPUÉS DE ESTE CÓDIGO?
===============================================================================

Después del bloque PHP normalmente aparecerá el HTML de la página.

En ese HTML podemos utilizar las variables que acabamos de preparar.


CASO 1: LA COMPRA SE HA CALCULADO CORRECTAMENTE
----------------------------------------------

    $compraRealizada === true


Mostraremos el resumen utilizando:

    $resultadoCompra

    $nombre

    $email

    $productoSeleccionado

    $unidades


CASO 2: NO SE HA CALCULADO LA COMPRA
-----------------------------------

    $compraRealizada === false


Mostraremos el formulario.


Si además:

    $errores !== []

mostraremos los mensajes de error.


Podemos recorrerlos utilizando:

    foreach ($errores as $error)


Y mostrarlos en HTML utilizando:

    escapar($error)


===============================================================================
IDEA FUNDAMENTAL
===============================================================================

La lógica de un formulario PHP debe seguir este orden:

    1. PREPARAR VARIABLES.

    2. COMPROBAR SI EL FORMULARIO SE HA ENVIADO.

    3. LEER LOS DATOS RECIBIDOS.

    4. VALIDAR LOS DATOS.

    5. APLICAR LAS REGLAS DEL NEGOCIO.

    6. COMPROBAR SI EXISTEN ERRORES.

    7. REALIZAR EL CÁLCULO SI TODO ES CORRECTO.

    8. GENERAR LA RESPUESTA HTML.


NO DEBEMOS HACER ESTO:

    Recibir datos
         |
         v
    Calcular directamente


DEBEMOS HACER ESTO:

    Recibir datos
         |
         v
    Validar datos
         |
         v
    Comprobar reglas
         |
         v
    ¿Todo correcto?
         |
         v
    Calcular compra


===============================================================================
REGLA PARA RECORDAR
===============================================================================

        NUNCA CONFIAR EN LOS DATOS QUE LLEGAN DEL CLIENTE.

        VALIDAR AL ENTRAR.

        PROCESAR SOLO LOS DATOS CORRECTOS.

        ESCAPAR AL MOSTRAR DATOS EN HTML.

===============================================================================
*/

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


<!--
===============================================================================
PRÁCTICA: COMPLETAR LA PARTE HTML Y PHP DE compra.php
===============================================================================

SITUACIÓN DE PARTIDA
--------------------

En la parte superior de compra.php ya hemos programado la lógica que:

    1. Recibe los datos enviados mediante POST.
    2. Valida el nombre.
    3. Valida el email.
    4. Comprueba que el producto exista.
    5. Valida el número de unidades.
    6. Comprueba el stock disponible.
    7. Calcula la compra si no hay errores.


Ahora debemos utilizar los resultados de esa lógica para generar
el HTML que verá el usuario.


VARIABLES QUE YA TENEMOS DISPONIBLES
------------------------------------

$errores

    Array que contiene los mensajes de error.

    Si está vacío:

        []

    significa que no se han detectado errores.


$compraRealizada

    Booleano que indica si la compra se ha calculado correctamente.

    true  -> Mostrar el resumen de compra.

    false -> Mostrar el formulario.


$resultadoCompra

    Array asociativo que contiene los importes calculados.

    Sus claves son:

        "subtotal"
        "descuento"
        "base"
        "iva"
        "total"


$productoSeleccionado

    Contiene el producto seleccionado, cuando se ha encontrado.


$nombre

    Contiene el nombre introducido por el usuario.


$email

    Contiene el email validado.


$unidades

    Contiene el número de unidades.


$productoId

    Contiene el ID del producto seleccionado.


OBJETIVO FINAL
--------------

Queremos que nuestra página funcione de esta manera:

                  compra.php
                      |
                      v
             ¿Compra calculada?
                      |
               +------+------+
               |             |
              SÍ             NO
               |             |
               v             v
          MOSTRAR         ¿HAY ERRORES?
          RESUMEN              |
                         +----+----+
                         |         |
                        SÍ         NO
                         |         |
                         v         v
                     MOSTRAR    MOSTRAR
                     ERRORES   FORMULARIO
                         |
                         v
                     MOSTRAR
                    FORMULARIO


IMPORTANTE:

Si hay errores, el usuario debe poder corregir el formulario.

No queremos que tenga que volver a empezar desde cero.

===============================================================================
-->


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


    <!--
    ===========================================================================
    TODO 1: DECIDIR QUÉ DEBEMOS MOSTRAR
    ===========================================================================

    Antes de mostrar el formulario debemos comprobar si la compra
    se ha calculado correctamente.

    Disponemos de esta variable:

        $compraRealizada


    Debéis crear una estructura condicional PHP:

        if
        else
        endif


    PISTA:

        <?php if (CONDICION): ?>

            HTML que se muestra cuando la condición es verdadera.

        <?php else: ?>

            HTML que se muestra cuando es falsa.

        <?php endif; ?>


    ¿QUÉ CONDICIÓN DEBÉIS UTILIZAR?

        $compraRealizada


    Si vale true:

        Mostraremos el RESUMEN DE COMPRA.


    Si vale false:

        Mostraremos el FORMULARIO.


    TAREA:

    Escribid la apertura del if PHP justo debajo de este comentario.

    El formulario que aparece más abajo deberá quedar dentro
    del else.

    No olvidéis cerrar la estructura con endif.
    ===========================================================================
    -->


    <!-- TODO: ABRIR AQUÍ EL IF QUE COMPRUEBA $compraRealizada -->


    <!--
    ===========================================================================
    TODO 2: CREAR EL RESUMEN DE COMPRA
    ===========================================================================

    Esta sección solamente debe aparecer cuando:

        $compraRealizada === true


    Debéis crear:

        <section class="panel">

            <h2>Compra calculada correctamente</h2>

            ...

        </section>


    Dentro de la sección debéis construir una TABLA HTML.


    ESTRUCTURA RECORDATORIO:

        <table>

            <tr>
                <th>Nombre del dato</th>
                <td>Valor del dato</td>
            </tr>

        </table>


    La tabla debe mostrar:

        Cliente
        Email
        Producto
        Precio unitario
        Unidades
        Subtotal
        Descuento (solamente si existe)
        Base
        IVA
        Total


    ---------------------------------------------------------------------------
    TODO 2.1: MOSTRAR EL NOMBRE
    ---------------------------------------------------------------------------

    El nombre está almacenado en:

        $nombre


    Debéis mostrarlo utilizando la sintaxis corta de echo:

    


    IMPORTANTE:

    Como es texto introducido por el usuario, debemos utilizar:

        escapar(...)


    ---------------------------------------------------------------------------
    TODO 2.2: MOSTRAR EL EMAIL
    ---------------------------------------------------------------------------

    El email está almacenado en:

        $email


    También debéis mostrarlo mediante escapar().


    ---------------------------------------------------------------------------
    TODO 2.3: MOSTRAR EL PRODUCTO
    ---------------------------------------------------------------------------

    El producto está almacenado en:

        $productoSeleccionado


    Es un array asociativo.

    Para obtener su nombre utilizamos la clave:

        ["nombre"]


    Recordad escapar el nombre antes de mostrarlo.


    ---------------------------------------------------------------------------
    TODO 2.4: MOSTRAR EL PRECIO UNITARIO
    ---------------------------------------------------------------------------

    El precio está almacenado en el array del producto.

    En nuestra versión utilizamos:

        ["precio"]


    Para mostrar un precio correctamente utilizamos:

        formatearPrecio(...)


    Por ejemplo, si almacenamos precios en céntimos:

        7990

    queremos mostrar:

        79,90 €


    ---------------------------------------------------------------------------
    TODO 2.5: MOSTRAR LAS UNIDADES
    ---------------------------------------------------------------------------

    Utilizad la variable:

        $unidades


    ---------------------------------------------------------------------------
    TODO 2.6: MOSTRAR EL SUBTOTAL
    ---------------------------------------------------------------------------

    El resultado del cálculo está en:

        $resultadoCompra


    Para acceder al subtotal:

        $resultadoCompra["subtotal"]


    Recordad formatear el precio.


    ---------------------------------------------------------------------------
    TODO 2.7: MOSTRAR EL DESCUENTO SOLAMENTE SI EXISTE
    ---------------------------------------------------------------------------

    Esta fila NO debe aparecer siempre.

    Solo debe aparecer cuando el descuento sea mayor que cero.

    PISTA:

        if ($resultadoCompra["descuento"] > 0)


    Utilizad la sintaxis alternativa de PHP.

    Dentro del if debéis generar una fila <tr>.

    Cuando no exista descuento, esa fila no debe generarse.


    ---------------------------------------------------------------------------
    TODO 2.8: MOSTRAR BASE, IVA Y TOTAL
    ---------------------------------------------------------------------------

    Las claves que necesitamos son:

        ["base"]

        ["iva"]

        ["total"]


    Mostrad los tres importes utilizando formatearPrecio().

    El total debe aparecer destacado mediante:

        <strong> ... </strong>


    ---------------------------------------------------------------------------
    TODO 2.9: AÑADIR UN ENLACE PARA REALIZAR OTRA COMPRA
    ---------------------------------------------------------------------------

    Debéis crear un enlace:

        <a href="compra.php">Realizar otra compra</a>


    Aplicadle la clase CSS:

        boton


    ¿QUÉ OCURRIRÁ AL PULSARLO?

    El navegador realizará una nueva petición GET a compra.php.

    Como no se habrá enviado el formulario mediante POST,
    se volverán a inicializar las variables y aparecerá
    el formulario de compra.
    ===========================================================================
    -->


    <!-- TODO: CONSTRUIR AQUÍ LA SECCIÓN CON EL RESUMEN -->


    <!--
    ===========================================================================
    TODO 3: AÑADIR EL ELSE
    ===========================================================================

    Ya hemos preparado la zona correspondiente al resumen.

    Ahora necesitamos indicar qué ocurre cuando la compra
    NO se ha calculado correctamente.


    Debéis escribir la estructura PHP correspondiente a:

        else


    Todo el formulario debe quedar dentro de esta rama.


    IMPORTANTE:

    En esta rama pueden ocurrir DOS situaciones:

        1. El usuario acaba de entrar y todavía no ha enviado nada.

        2. El usuario ha enviado datos, pero existen errores.


    En ambas situaciones queremos mostrar el formulario.

    La diferencia es que, si existen errores, también
    mostraremos los mensajes correspondientes.
    ===========================================================================
    -->


    <!-- TODO: ESCRIBIR AQUÍ EL ELSE -->


    <!--
    ===========================================================================
    TODO 4: MOSTRAR LOS ERRORES DEL FORMULARIO
    ===========================================================================

    Antes de mostrar el formulario debemos comprobar si existen errores.

    Los mensajes están almacenados en:

        $errores


    Recordad:

        $errores = [];

    significa que no hay errores.


    En cambio:

        $errores = [
            "Debes introducir un nombre",
            "Correo electrónico incorrecto"
        ];

    significa que tenemos dos errores.


    ---------------------------------------------------------------------------
    PASO 1
    ---------------------------------------------------------------------------

    Crear un if que compruebe si el array NO está vacío.

    PISTA:

        $errores !== []


    ---------------------------------------------------------------------------
    PASO 2
    ---------------------------------------------------------------------------

    Dentro del if, crear:

        <section class="errores">

            <h2>Revisa los siguientes errores</h2>

            <ul>

                ...

            </ul>

        </section>


    ---------------------------------------------------------------------------
    PASO 3
    ---------------------------------------------------------------------------

    Recorrer todos los errores mediante foreach.

    PISTA:

        foreach ($errores as $error)


    Por cada error debéis generar:

        <li>MENSAJE DEL ERROR</li>


    IMPORTANTE:

    Utilizad escapar() antes de mostrar cada mensaje.


    ---------------------------------------------------------------------------
    PASO 4
    ---------------------------------------------------------------------------

    Cerrar el foreach y el if utilizando:

        endforeach;

        endif;


    RESULTADO ESPERADO:

    Si tenemos tres errores, aparecerán tres elementos <li>.

    Si no hay errores, NO debe aparecer la sección de errores.
    ===========================================================================
    -->


    <!-- TODO: MOSTRAR AQUÍ LOS ERRORES SI EXISTEN -->


    <!--
    ===========================================================================
    TODO 5: FORMULARIO DE COMPRA
    ===========================================================================

    Este formulario ya tiene gran parte del trabajo realizado.

    Vamos a estudiar cómo se conecta con el código PHP
    que hemos programado en la parte superior del archivo.


    ATRIBUTO action:

        action="compra.php"

    Indica el archivo que recibirá los datos.


    ATRIBUTO method:

        method="POST"

    Indica que el navegador enviará los datos mediante POST.


    En PHP podremos acceder a ellos utilizando:

        $_POST


    IMPORTANTE:

    El formulario solamente debe aparecer cuando
    $compraRealizada sea false.
    ===========================================================================
    -->


    <form class="formulario" action="compra.php" method="POST">


        <!--
        =======================================================================
        CAMPO 1: NOMBRE
        =======================================================================

        <label> muestra el texto descriptivo del campo.

        El atributo:

            for="nombre"

        se relaciona con el atributo:

            id="nombre"

        del input.


        El atributo:

            name="nombre"

        es el que permite enviar el dato al servidor.


        PHP lo recibe mediante:

            $_POST["nombre"]


        -----------------------------------------------------------------------
        ¿PARA QUÉ SIRVE value?
        -----------------------------------------------------------------------

        El atributo value permite indicar el valor que debe
        aparecer dentro del campo.

        Aquí estamos utilizando:

            value="<?= escapar($nombre) ?>"


        ¿POR QUÉ?

        Porque si el usuario envía el formulario y comete un error,
        queremos conservar el nombre que había escrito.


        Ejemplo:

            El usuario escribe "Pepe".

            Introduce un email incorrecto.

            PHP detecta el error.

            Volvemos a mostrar el formulario.

            El campo nombre debe seguir mostrando "Pepe".


        IMPORTANTE:

        escapar() es necesario porque el valor se introduce
        dentro de un atributo HTML.

        En este caso, nuestra función de escape HTML también
        convierte las comillas en entidades seguras.
        =======================================================================
        -->


        <div class="campo">

            <label for="nombre">Nombre</label>

            <input
                type="text"
                id="nombre"
                name="nombre"
                value="<?= escapar($nombre) ?>"
            >

        </div>


        <!--
        =======================================================================
        CAMPO 2: EMAIL
        =======================================================================

        type="email" indica que esperamos un correo electrónico.

        El navegador puede realizar una comprobación básica
        del formato del email.

        Sin embargo, NO debemos confiar solamente en esa validación.

        En PHP ya hemos utilizado:

            filter_var($emailBruto, FILTER_VALIDATE_EMAIL)


        El atributo:

            name="email"

        permite acceder al dato mediante:

            $_POST["email"]


        El atributo value intenta conservar el email
        almacenado en la variable $email.


        ATENCIÓN:

        En nuestra lógica actual, $email solamente se asigna
        cuando el correo tiene un formato válido.

        Por eso, si el email introducido es incorrecto,
        este campo volverá a aparecer vacío.

        TODO OPCIONAL:

        Modificar la lógica PHP para conservar también
        el email incorrecto y permitir que el usuario lo corrija.
        =======================================================================
        -->


        <div class="campo">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= escapar($email) ?>"
            >

        </div>


        <!--
        =======================================================================
        CAMPO 3: SELECCIÓN DE PRODUCTO
        =======================================================================

        Este campo es especialmente interesante porque
        las opciones NO están escritas manualmente.

        Las vamos a generar recorriendo el array $productos.


        En HTML, un desplegable utiliza:

            <select>


        Y cada opción utiliza:

            <option>


        Ejemplo:

            <select name="producto">

                <option value="1">Teclado</option>

                <option value="2">Ratón</option>

            </select>


        Pero nosotros NO queremos escribir los productos a mano.

        Queremos que el desplegable se genere automáticamente
        a partir de los productos disponibles en datos.php.


        Para eso utilizamos:

            foreach ($productos as $producto)


        Por cada producto generamos un <option>.


        -----------------------------------------------------------------------
        EL ATRIBUTO value
        -----------------------------------------------------------------------

        En cada opción:

            value="..."

        debe aparecer el ID del producto.


        Por ejemplo:

            <option value="2">Ratón gaming</option>


        Si el usuario selecciona esa opción, PHP recibirá:

            $_POST["producto"] = "2"


        -----------------------------------------------------------------------
        EL TEXTO VISIBLE
        -----------------------------------------------------------------------

        Queremos mostrar:

            Nombre del producto - Precio


        Por ejemplo:

            Ratón gaming - 39,90 €


        Utilizamos:

            escapar()

        para mostrar el nombre.


        Y:

            formatearPrecio()

        para mostrar el precio.


        -----------------------------------------------------------------------
        ¿QUÉ ES selected?
        -----------------------------------------------------------------------

        El atributo HTML:

            selected

        indica que una opción aparece seleccionada.


        Por ejemplo:

            <option value="2" selected>
                Ratón gaming
            </option>


        -----------------------------------------------------------------------
        ¿CÓMO SABEMOS QUÉ PRODUCTO ESTÁ SELECCIONADO?
        -----------------------------------------------------------------------

        Tenemos la variable:

            $productoId


        Y cada producto tiene:

            $producto["id"]


        Los comparamos mediante:

            $productoId === $producto["id"]


        Si coinciden:

            $seleccionado = true


        Si no coinciden:

            $seleccionado = false


        -----------------------------------------------------------------------
        OPERADOR TERNARIO
        -----------------------------------------------------------------------

        Utilizamos:

            $seleccionado ? "selected" : ""


        Su significado es:

            Si $seleccionado es true:

                Escribir "selected".


            Si $seleccionado es false:

                No escribir nada.


        Es una forma abreviada de utilizar un if/else.


        EJEMPLO:

            $seleccionado = true;


        Resultado:

            <option value="2" selected>


        Si:

            $seleccionado = false;


        Resultado:

            <option value="2">


        =======================================================================
        -->


        <div class="campo">

            <!--
            TODO 6:

            Comprobad que el atributo for del label coincide
            con el id del select.

            En este caso debe ser:

                for="producto"

            y:

                id="producto"

            No debe utilizarse for="email" para este campo.
            -->

            <label for="producto">Producto</label>

            <select id="producto" name="producto">

                <!--
                Esta opción permite representar que todavía
                no se ha seleccionado ningún producto.

                Su value está vacío.

                En PHP, un valor vacío no superará la validación
                FILTER_VALIDATE_INT.
                -->

                <option value="">Selecciona un producto</option>


                <!--
                TODO 7:

                Estudiad el siguiente foreach.

                Debéis identificar:

                    1. Qué array recorremos.

                    2. Qué representa $producto.

                    3. Cuántas opciones se generan.

                    4. De dónde sale el ID.

                    5. De dónde sale el nombre.

                    6. De dónde sale el precio.


                Recordad que si tenemos 5 productos,
                se generarán 5 opciones dinámicas,
                además de la opción inicial vacía.
                -->


                <?php foreach ($productos as $producto): ?>


                    <?php
                    /*
                    Comprobamos si el ID del producto actual
                    coincide con el producto seleccionado.

                    El resultado será un booleano:

                        true

                    o:

                        false
                    */

                    $seleccionado = $productoId === $producto["id"];
                    ?>


                    <option
                        value="<?= $producto["id"] ?>"
                        <?= $seleccionado ? "selected" : "" ?>
                    >

                        <?= escapar($producto["nombre"]) ?>
                        -
                        <?= formatearPrecio($producto["precio"]) ?>

                    </option>


                <?php endforeach; ?>


            </select>

        </div>


        <!--
        =======================================================================
        CAMPO 4: UNIDADES
        =======================================================================

        type="number" permite introducir valores numéricos.

        name="unidades" indica que PHP recibirá el valor mediante:

            $_POST["unidades"]


        El atributo:

            value="<?= $unidades ?>"

        permite conservar el número de unidades.


        -----------------------------------------------------------------------
        TODO 8: AÑADIR UNA RESTRICCIÓN HTML
        -----------------------------------------------------------------------

        No queremos permitir cantidades menores que 1.

        Debéis investigar y añadir el atributo:

            min


        para indicar que la cantidad mínima es 1.


        IMPORTANTE:

        Esta restricción mejora el comportamiento del formulario,
        pero en PHP debemos seguir validando las unidades.

        Recordad:

            FILTER_VALIDATE_INT

        y:

            $unidadesValidadas < 1
        =======================================================================
        -->


        <div class="campo">

            <label for="unidades">Unidades</label>

            <input
                type="number"
                id="unidades"
                name="unidades"
                value="<?= $unidades ?>"
            >

        </div>


        <!--
        =======================================================================
        BOTÓN DE ENVÍO
        =======================================================================

        type="submit" indica que este botón envía el formulario.

        Cuando el usuario pulse el botón:

            1. El navegador recoge los campos del formulario.

            2. Utiliza sus atributos name.

            3. Envía los datos a compra.php mediante POST.

            4. PHP vuelve a ejecutar compra.php desde el principio.

            5. Entra en el bloque:

                if ($_SERVER["REQUEST_METHOD"] === "POST")

            6. Valida todos los datos.

            7. Prepara los errores o el resultado.

            8. Genera el HTML correspondiente.


        IMPORTANTE:

        Pulsar el botón NO significa que JavaScript ejecute
        automáticamente una función.

        En este ejercicio el procesamiento se realiza en PHP,
        en el servidor.
        =======================================================================
        -->


        <div>

            <button type="submit">
                Calcular compra
            </button>

        </div>


    </form>


    <!--
    ===========================================================================
    TODO 9: CERRAR LA ESTRUCTURA CONDICIONAL PRINCIPAL
    ===========================================================================

    Al principio de <main> hemos abierto un if para comprobar:

        $compraRealizada


    Después hemos añadido un else para mostrar el formulario.


    Ahora debemos cerrar la estructura utilizando:

        endif;


    RECORDATORIO:

        <?php if (CONDICION): ?>

            ...

        <?php else: ?>

            ...

        <?php endif; ?>


    Si olvidamos cerrar el if, PHP producirá un error de sintaxis.
    ===========================================================================
    -->


    <!-- TODO: ESCRIBIR AQUÍ EL endif DE LA CONDICIÓN PRINCIPAL -->


</main>


<!--
===============================================================================
COMPROBACIONES FINALES
===============================================================================

Cuando hayáis completado todos los TODO, probad lo siguiente:


PRUEBA 1
--------

Entrar directamente en:

    compra.php

RESULTADO ESPERADO:

    Debe aparecer el formulario.


PRUEBA 2
--------

Enviar el formulario sin introducir un nombre.

RESULTADO ESPERADO:

    Debe aparecer un mensaje de error.


PRUEBA 3
--------

Introducir un email incorrecto.

RESULTADO ESPERADO:

    Debe aparecer el error correspondiente.


PRUEBA 4
--------

No seleccionar ningún producto.

RESULTADO ESPERADO:

    Debe aparecer un error indicando que falta un producto válido.


PRUEBA 5
--------

Seleccionar un producto y solicitar más unidades
de las que existen en stock.

RESULTADO ESPERADO:

    Debe aparecer un mensaje de stock insuficiente.


PRUEBA 6
--------

Introducir todos los datos correctamente.

RESULTADO ESPERADO:

    Debe aparecer el resumen de compra.


PRUEBA 7
--------

Solicitar una cantidad de unidades que permita aplicar descuento,
siempre que exista stock suficiente.

RESULTADO ESPERADO:

    Debe aparecer la fila correspondiente al descuento.


PRUEBA 8
--------

Pulsar el enlace "Realizar otra compra".

RESULTADO ESPERADO:

    Debe volver a aparecer el formulario.


===============================================================================
PREGUNTAS PARA REFLEXIONAR
===============================================================================

1. ¿Por qué utilizamos POST para enviar el formulario?

2. ¿Para qué sirve el atributo name de un input?

3. ¿Qué diferencia existe entre id y name?

4. ¿Por qué utilizamos escapar() al mostrar el nombre?

5. ¿Por qué utilizamos formatearPrecio()?

6. ¿Por qué necesitamos un foreach para generar el select?

7. ¿Para qué sirve el atributo selected?

8. ¿Qué diferencia existe entre $errores y $compraRealizada?

9. ¿Por qué mostramos el formulario cuando hay errores?

10. ¿Por qué no debemos confiar solamente en la validación HTML?


===============================================================================
OBJETIVO DE APRENDIZAJE
===============================================================================

Al terminar la práctica debéis comprender cómo se conectan:

    HTML
      |
      v
    FORMULARIO
      |
      v
    POST
      |
      v
    PHP
      |
      v
    VALIDACIONES
      |
      v
    VARIABLES
      |
      v
    HTML GENERADO DINÁMICAMENTE


La idea más importante es:

    PHP DECIDE QUÉ HTML SE GENERA SEGÚN EL RESULTADO
    DEL PROCESAMIENTO DEL FORMULARIO.

===============================================================================
-->

</body>
</html>
