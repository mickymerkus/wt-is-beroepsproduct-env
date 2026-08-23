<?php
    require_once __DIR__ . '/data/db_connectie.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';
    require_once __DIR__ . '/logica/bestelling.php';
    require_once __DIR__ . '/data/gebruikers.php';
    require_once __DIR__ . '/logica/adres.php';

    startSessie();

    $db = maakVerbinding();

    $fouten = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (($_POST['actie'] ?? '') === 'bestellen') {
            // Check of er iets mis is, zoniet dan geef dit weer in de url
            $fouten = plaatsBestelling($db, $_POST);

            if (!$fouten) {
                header('Location: bestelling_geschiedenis.php?geplaatst=1');
                exit;
            }
        } else {
            // De klant past op deze pagina ook nog het winkelmandje aan
            verwerkWinkelmandjeActie($db, $_POST);

            header('Location: bevestig_bestelling.php');
            exit;
        }
    }


    $winkelmandjeRegels = haalWinkelmandjeRegels($db);
    $winkelmandjeTotaal = berekenTotaal($winkelmandjeRegels);
    $aantalInMandje = aantalArtikelenInMandje();

    $gebruiker = huidigeGebruiker();

    // Adres van het account uit elkaar knippen om het formulier vast in te vullen
    $opgeslagenAdres = splitsAdresRegel('');

    if ($gebruiker) {
        $opgeslagenAdres = splitsAdresRegel(haalAdresVanGebruiker($db, $gebruiker['username']));
    }

    // Config
    $paginaTitel = 'Bevestig bestelling';
    $bodyKlasse = 'bevestig-bestelling';
    $toonBanner = true;
    $toonBestelknop = false;
    $winkelmandjeActie = 'bevestig_bestelling.php';
    $mandjeAlsLade = false;     // altijd zichtbaar op de bevestigpagina
    $categorie = ''; // geen categorietabs op deze pagina
    $inhoud = __DIR__ . '/presentatie/bestelformulier.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
?>





