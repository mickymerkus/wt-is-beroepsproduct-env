<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/data/bestellingen.php';
    require_once __DIR__ . '/data/producten.php';
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

        header('Location: bestellingsoverzicht_personeel.php');
        exit;
    }

    // Haal de bestellingen en de inhoud ervan op die relevant zijn voor de keuken.
    $bestellingen = haalBestellingenMetStatus($db, KEUKEN_VAN, KEUKEN_TOT);
    $ingredientenPerProduct = haalIngredientenPerProduct($db);

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
    $inhoud      = __DIR__ . '/presentatie/bestellingsoverzicht_personeel.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
