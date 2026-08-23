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

    $fouten = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fouten = registreerGebruiker(
            $db,
            $_POST['gebruikersnaam'] ?? '',
            $_POST['wachtwoord'] ?? '',
            $_POST['bevestig-wachtwoord'] ?? '',
            $_POST['voornaam'] ?? '',
            $_POST['achternaam'] ?? '',
            $_POST['straat'] ?? '',
            $_POST['huisnummer'] ?? '',
            $_POST['postcode'] ?? '',
            $_POST['stad'] ?? ''
        );

        if (!$fouten) {
            header('Location: index.php');
            exit;
        }
    }

    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Account aanmaken';
    $bodyKlasse = 'registratie';
    $toonBanner = true;
    $toonBestelknop = false;
    $inhoud = __DIR__ . '/presentatie/registratie.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
?>