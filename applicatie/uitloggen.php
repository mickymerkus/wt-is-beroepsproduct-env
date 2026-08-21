<?php
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        logUitGebruiker();
    }

    header('Location: index.php');
    exit;
?>
