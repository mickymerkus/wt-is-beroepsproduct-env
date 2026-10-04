<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/data/bestellingen.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';
    require_once __DIR__ . '/logica/bestelling.php';
    require_once __DIR__ . '/logica/paginatie.php';

    startSessie();

    $db = maakVerbinding();

    // Mag leeg zijn: ook een gast moet de bestelling kunnen volgen die hij net plaatste
    $gebruiker = huidigeGebruiker();

    // Standaard geen paginering: een gast ziet precies één bestelling.
    $paginering = null;

    if ($gebruiker) {
        // Een ingelogde klant ziet zijn historie, één pagina per keer
        $totaalBestellingen = telBestellingenVanKlant($db, $gebruiker['username']);
        $paginering = bouwPaginering($_GET, $totaalBestellingen);

        $bestellingen = haalBestellingenVanKlant(
            $db,
            $gebruiker['username'],
            $paginering['perPagina'],
            $paginering['offset']
        );
    } else {
        // Een gast heeft alleen het bestelnummer dat bij het plaatsen in de sessie is gezet
        $bestellingen = haalBestellingMetBestelnummer($db, $_SESSION['laatsteBestelling'] ?? 0);
    }

    // Zorgen dat alle waarden zijn berekend voor het tonen van de bestellingen
    // in het template.
    foreach ($bestellingen as $index => $bestelling) {
        $bestellingen[$index]['totaal']       = berekenTotaal($bestelling['regels']);
        $bestellingen[$index]['statusTekst']  = statusOmschrijving($bestelling['status']);
        $bestellingen[$index]['statusKlasse'] = statusCssKlasse($bestelling['status']);
    }


    // Staat in de url na de redirect vanaf het bestelformulier
    $isGeplaatst = ($_GET['geplaatst'] ?? '') === '1';

    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Besteloverzicht';
    $bodyKlasse  = 'geschiedenis-pagina';
    $toonBanner  = true;
    // Voor de paginaknoppen onder het overzicht
    $pagineringBasisUrl = 'bestelling_geschiedenis.php';
    $inhoud      = __DIR__ . '/presentatie/bestelling_geschiedenis.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
?>
