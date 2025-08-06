<?php
// includes/funciones.php

function limpiarDatos($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato, ENT_QUOTES, 'UTF-8');
    return $dato;
}

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function redirigirSiNoLogueado($url = 'index.php') {
    if(!estaLogueado()) {
        header("Location: $url");
        exit;
    }
}
?>