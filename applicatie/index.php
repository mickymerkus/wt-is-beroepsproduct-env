<?php 
    require_once __DIR__ . '/data/categorieen.php';
    require_once __DIR__ . '/data/producten.php';
    require_once __DIR__ . '/logica/sessie.php';
    require_once __DIR__ . '/logica/winkelmandje.php';
    require_once __DIR__ . '/logica/authenticatie.php';
    require_once __DIR__ . '/logica/paginatie.php';


    // Sessie starten zodat de sessiecookie wordt meegestuurd.
    startSessie();

    $db = maakVerbinding();

    // Haal alle categorieeën op uit de database voor de nav tabs en validatie
    $categorieen = haalProductTypes($db);

    // Bij post staat het in het formulier, bij get in de url
    $gevraagd = $_POST['categorie'] ?? $_GET['categorie'] ?? '';

    // Afscherming van de parameter, default staat op de eerste waarde in de database
    $categorie = in_array($gevraagd, $categorieen, true) ? $gevraagd : $categorieen[0] ?? '';

    // Of de lade na een redirect meteen open moet staan
    $mandjeOpen = ($_GET['mandje'] ?? '') === 'open';

    // Toevoegen, wijzigen of verwijderen in het winkelmandje
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verwerkWinkelmandjeActie($db, $_POST);

        // Het paginanummer gaat mee in de redirect, anders springt de klant na
        // het toevoegen van een product terug naar pagina 1 van het menu.
        header('Location: index.php?categorie=' . urlencode($categorie)
            . '&pagina=' . huidigePaginaNummer($_POST)
            . '&mandje=open');
        exit;
    }

    // ophalen van de data. Het menu wordt per pagina opgehaald, zodat een lange
    // kaart nooit in één query de hele Product-tabel langs hoeft.
    $totaalProducten = telProductenInCategorie($db, $categorie);
    $paginering = bouwPaginering($_GET, $totaalProducten);

    $producten = haalProductenMetIngredienten(
        $db,
        $categorie,
        $paginering['perPagina'],
        $paginering['offset']
    );
    $winkelmandjeRegels = haalWinkelmandjeRegels($db);
    $winkelmandjeTotaal = berekenTotaal($winkelmandjeRegels);
    $aantalInMandje = aantalArtikelenInMandje();

    // Config
    $paginaTitel = 'Bestellen';
    $bodyKlasse = 'home';
    $toonBanner = true;
    $toonBestelknop = true;
    $winkelmandjeActie = 'index.php';
    $mandjeAlsLade = true;      // menupagina: mandje schuift in en uit beeld
    // Voor de paginaknoppen onder het menu. De categorie gaat mee in de links,
    // zodat je binnen dezelfde categorie blijft als je doorbladert.
    $pagineringBasisUrl = 'index.php';
    $pagineringExtra    = ['categorie' => $categorie];
    $inhoud = __DIR__ . '/presentatie/menu.php';

    include __DIR__ . '/presentatie/gedeeld/layout.php';
    
?>
