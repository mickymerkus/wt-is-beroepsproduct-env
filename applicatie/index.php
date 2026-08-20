<?php 
    require_once __DIR__ . '/data/categorieen.php';
    require_once __DIR__ . '/data/producten.php';

    $db = maakVerbinding();

    // Haal alle categorieeën op uit de database voor de nav tabs en validatie
    $categorieen = haalProductTypes($db);

    $gevraagd = $_GET['categorie'] ?? 0;

    // Afscherming van de parameter, default staat op pizza
    $categorie = in_array($gevraagd, $categorieen, true) ? $gevraagd : 'Pizza';

    // ophalen van de data
    $producten = haalProductenMetIngredienten($db, $categorie);

    // Config
    $paginaTitel = 'Bestellen';
    $bodyKlasse = 'home';
    $toonBanner = true;
    $inhoud = __DIR__ . '/presentatie/menu.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
    
?>
