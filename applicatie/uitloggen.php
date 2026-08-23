<?php

    // Deze constante markeert dat de aanvraag via een controller binnenkomt.
    // Bestanden in data/, logica/ en presentatie/ weigeren te draaien zonder.
    define('TOEGANG_VIA_CONTROLLER', true);

    // Foutafhandeling als eerste, zodat ook een fout tijdens het inladen van de
    // overige bestanden netjes wordt opgevangen in plaats van getoond.
    require_once __DIR__ . '/logica/beveiliging.php';
    installeerFoutafhandeling();

    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();
    stuurBeveiligingsheaders();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        logUitGebruiker();
    }

    header('Location: index.php');
    exit;
?>
