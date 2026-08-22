<?php
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();

    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Privacyverklaring';
    $bodyKlasse  = 'privacy-pagina';
    $toonBanner  = true;
    $inhoud      = __DIR__ . '/presentatie/privacy.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
