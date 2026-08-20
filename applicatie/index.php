<?php 
    require_once __DIR__ . '/data/categorieen.php';
    require_once __DIR__ . '/data/producten.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';

    // Sessie starten zodat de sessiecookie wordt meegestuurd.
    startSessie();

    $db = maakVerbinding();

    // Haal alle categorieeën op uit de database voor de nav tabs en validatie
    $categorieen = haalProductTypes($db);

    // Bij post staat het in het formulier, bij get in de url
    $gevraagd = $_POST['categorie'] ?? $_GET['categorie'] ?? '';

    // Afscherming van de parameter, default staat op pizza
    $categorie = in_array($gevraagd, $categorieen, true) ? $gevraagd : 'pizza';

    // Toevoegen, wijzigen of verwijderen in het winkelmandje
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verwerkWinkelmandjeActie($db, $_POST);

        header('Location: index.php?categorie=' .urlencode($categorie));
        exit;
    }

    // ophalen van de data
    $producten = haalProductenMetIngredienten($db, $categorie);
    $winkelmandjeRegels = haalWinkelmandjeRegels($db);
    $winkelmandjeTotaal = berekenTotaal($winkelmandjeRegels);
    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Bestellen';
    $bodyKlasse = 'home';
    $toonBanner = true;
    $toonBestelknop = true;
    $winkelmandjeActie = 'index.php';
    $inhoud = __DIR__ . '/presentatie/menu.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
    
?>
