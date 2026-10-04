<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/data/bestellingen.php';
    require_once __DIR__ . '/data/producten.php';
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

        // Het paginanummer gaat mee terug, anders springt de medewerker na het
        // opslaan van een status naar pagina 1.
        header('Location: bestellingsoverzicht_personeel.php?pagina=' . huidigePaginaNummer($_POST));
        exit;
    }

    // Haal de bestellingen en de inhoud ervan op die relevant zijn voor de keuken.
    // De wachtrij kan op een drukke avond lang worden, dus één pagina per keer.
    $totaalBestellingen = telBestellingenMetStatus($db, KEUKEN_VAN, KEUKEN_TOT);
    $paginering = bouwPaginering($_GET, $totaalBestellingen);

    $bestellingen = haalBestellingenMetStatus(
        $db,
        KEUKEN_VAN,
        KEUKEN_TOT,
        $paginering['perPagina'],
        $paginering['offset']
    );

    // Verzamel de productnamen die op deze pagina voorkomen, zodat we alleen
    // de ingrediënten van die producten opvragen in plaats van de hele tabel.
    $productNamenOpPagina = [];

    foreach ($bestellingen as $bestelling) {
        foreach ($bestelling['regels'] as $regel) {
            $productNamenOpPagina[$regel['product_naam']] = true;
        }
    }

    $ingredientenPerProduct = haalIngredientenPerProduct($db, array_keys($productNamenOpPagina));

    // Alles wat het template moet tonen alvast klaarzetten
    foreach ($bestellingen as $index => $bestelling) {
        $bestellingen[$index]['statusTekst']  = statusOmschrijving($bestelling['status']);
        $bestellingen[$index]['statusKlasse'] = statusCssKlasse($bestelling['status']);

        // Voor elk product de ingrediënten erbij zetten
        foreach ($bestelling['regels'] as $regelIndex => $regel) {
            $bestellingen[$index]['regels'][$regelIndex]['ingredienten'] =
                $ingredientenPerProduct[$regel['product_naam']] ?? [];
        }
    }

    // Haal alle statussen
    $statussen = alleStatussen();
    // Nodig doordat die in layout.php bevat is
    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Keukenoverzicht';
    $bodyKlasse  = 'personeel-pagina';
    $toonBanner  = false;
    $toonFooter  = false;
    // Voor de paginaknoppen onder het overzicht
    $pagineringBasisUrl = 'bestellingsoverzicht_personeel.php';
    $inhoud      = __DIR__ . '/presentatie/bestellingsoverzicht_personeel.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
