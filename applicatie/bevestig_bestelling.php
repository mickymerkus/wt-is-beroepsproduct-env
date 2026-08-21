<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';

    startSessie();

    $db = maakVerbinding();

    // De klant mag op deze pagina ook nog het winkelmandje aanpassen
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verwerkWinkelmandjeActie($db, $_POST);

        header('Location: bevestig_bestelling.php');
        exit;
    }

    $winkelmandjeRegels = haalWinkelmandjeRegels($db);
    $winkelmandjeTotaal = berekenTotaal($winkelmandjeRegels);
    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Bevestig bestelling';
    $bodyKlasse = 'bevestig-bestelling';
    $toonBanner = true;
    $toonBestelknop = false;
    $winkelmandjeActie = 'bevestig_bestelling.php';
    $categorie = ''; // geen categorietabs op deze pagina
    $inhoud = __DIR__ . '/presentatie/bestelformulier.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
?>





