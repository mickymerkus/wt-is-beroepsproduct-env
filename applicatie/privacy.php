<?php

    // Deze constante markeert dat de aanvraag via een controller binnenkomt.
    // Bestanden in data/, logica/ en presentatie/ weigeren te draaien zonder.
    define('TOEGANG_VIA_CONTROLLER', true);

    // Foutafhandeling als eerste, zodat ook een fout tijdens het inladen van de
    // overige bestanden netjes wordt opgevangen in plaats van getoond.
    require_once __DIR__ . '/logica/beveiliging.php';
    installeerFoutafhandeling();

    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();
    stuurBeveiligingsheaders();

    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Privacyverklaring';
    $bodyKlasse  = 'privacy-pagina';
    $toonBanner  = true;
    $inhoud      = __DIR__ . '/presentatie/privacy.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
