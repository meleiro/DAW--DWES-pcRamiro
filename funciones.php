<?php

// ======================================================
// FUNCIÓN PARA FORMATEAR UN PRECIO
// ======================================================

// La función recibe un precio expresado en CÉNTIMOS.
//
// Ejemplo:
// 2999 céntimos → 29,99 €
//
// int $centimos
//     Indica que el parámetro debe ser un número entero.
//
// : string
//     Indica que la función devolverá un string.

function formatearPrecio(int $centimos): string
{

    // Convertimos los céntimos a euros.
    //
    // Ejemplo:
    //
    // 2999 / 100 = 29.99

    $euros = $centimos / 100;


    // number_format() permite dar formato a un número.
    //
    // En este caso:
    //
    // number_format(
    //     $euros,  // número que queremos formatear
    //     2,       // número de decimales
    //     ",",     // separador decimal
    //     "."      // separador de miles
    // )
    //
    // Ejemplo:
    //
    // 123456 céntimos
    //       ↓
    // 1234.56 euros
    //       ↓
    // "1.234,56"
    //
    // Finalmente concatenamos " €".

    return number_format(
        $euros,
        2,
        ",",
        "."
    ) . " €";
}