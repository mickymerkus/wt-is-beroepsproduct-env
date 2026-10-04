<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/data/bestellingen.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';
    require_once __DIR__ . '/logica/bestelling.php';
    require_once __DIR__ . '/logica/paginatie.php';

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

        // Paginanummer mee, anders springt het overzicht terug naar pagina 1
        header('Location: bestellingsoverzicht_bezorger.php?pagina=' . huidigePaginaNummer($_POST));
        exit;
    }

    // Eén pagina bestellingen die klaarstaan voor bezorging of al onderweg zijn
    $totaalBestellingen = telBestellingenMetStatus($db, BEZORGER_VAN, BEZORGER_TOT);
    $paginering = bouwPaginering($_GET, $totaalBestellingen);

    $bestellingen = haalBestellingenMetStatus(
        $db,
        BEZORGER_VAN,
        BEZORGER_TOT,
        $paginering['perPagina'],
        $paginering['offset']
    );

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
    $toonFooter  = false;
    // Voor de paginaknoppen onder het overzicht
    $pagineringBasisUrl = 'bestellingsoverzicht_bezorger.php';
    $inhoud      = __DIR__ . '/presentatie/bestellingsoverzicht_bezorger.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
