<?php

    // Deze constante markeert dat de aanvraag via een controller binnenkomt.
    // Bestanden in data/, logica/ en presentatie/ weigeren te draaien zonder.
    define('TOEGANG_VIA_CONTROLLER', true);

    // Foutafhandeling als eerste, zodat ook een fout tijdens het inladen van de
    // overige bestanden netjes wordt opgevangen in plaats van getoond.
    require_once __DIR__ . '/logica/beveiliging.php';
    installeerFoutafhandeling();

    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();
    stuurBeveiligingsheaders();

    $db = maakVerbinding();

    $foutmelding = '';

    // Verwerk de inlogpoging en als er iets niet klopt geef een foutmelding
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['gebruikersnaam'] ?? '';
        $wachtwoord = $_POST['wachtwoord'] ?? '';

        if (logInGebruiker($db, $username, $wachtwoord)) {
            // routing van personeel naar het bestellingsoverzicht en normale gebruikers naar het menu.
            if (isPersoneel()) {
                header('Location: bestellingsoverzicht_personeel.php');
            } else {
                header('Location: index.php');
            }
            exit;
        }

        $foutmelding = 'Gebruikersnaam of wachtwoord is onjuist.';
    }

    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Inloggen';
    $bodyKlasse = 'login-page';
    $toonBanner = false;
    $toonBestelknop = false;
    $inhoud = __DIR__ . '/presentatie/login.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
?>