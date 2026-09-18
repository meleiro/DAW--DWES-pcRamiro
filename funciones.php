<?php
function formatearPrecio (int $centimos) : string {
    $euros = $centimos / 100;
    return number_format($euros, 2, ",", ".") . " €";

}