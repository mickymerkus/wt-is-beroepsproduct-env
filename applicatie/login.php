<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';

    startSessie();

    $db = maakVerbinding();

    $foutmelding = '';

    // Verwerk de inlogpoging en als er iets niet klopt geef een foutmelding
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['gebruikersnaam'] ?? '';
        $wachtwoord = $_POST['wachtwoord'] ?? '';

        if (logInGebruiker($db, $username, $wachtwoord)) {
            header('Location: index.php');
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