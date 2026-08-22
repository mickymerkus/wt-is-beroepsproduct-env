<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/data/bestellingen.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';
    require_once __DIR__ . '/logica/bestelling.php';

    startSessie();

    // Check of het echt personeel is, zoniet dan wordt je naar de inlogpagina gestuurd.
    if (!isPersoneel()) {
        header('Location: login.php');
        exit;
    }

    $db = maakVerbinding();

    // Statuswijziging verwerken en daarna redirecten, zodat F5 niet opnieuw opslaat
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['actie'] ?? '') === 'status_wijzigen') {
        wijzigBestellingStatus($db, $_POST);

        header('Location: bestellingsoverzicht_bezorger.php');
        exit;
    }

    // Haal de bestellingen op die klaarstaan voor bezorging of al onderweg zijn.
    $bestellingen = haalBestellingenMetStatus($db, BEZORGER_VAN, BEZORGER_TOT);

    // Alles wat het template moet tonen alvast klaarzetten
    foreach ($bestellingen as $index => $bestelling) {
        $bestellingen[$index]['totaal']       = berekenTotaal($bestelling['regels']);
        $bestellingen[$index]['statusTekst']  = statusOmschrijving($bestelling['status']);
        $bestellingen[$index]['statusKlasse'] = statusCssKlasse($bestelling['status']);
    }

    // Haal alle statussen
    $statussen = alleStatussen();
    // Nodig doordat die in layout.php bevat is
    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Bezorgoverzicht';
    $bodyKlasse  = 'bezorger-pagina';
    $toonBanner  = false;
    $inhoud      = __DIR__ . '/presentatie/bestellingsoverzicht_bezorger.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
