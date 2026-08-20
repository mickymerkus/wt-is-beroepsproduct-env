<?php 
    require_once __DIR__ . '/categorieen.php';

    $db = maakVerbinding();

    $categorieen = haalProductTypes($db);

    $gevraagd = $_GET['categorie'] ?? 0;

    // Afscherming van de parameter, default staat op pizza
    $categorie = in_array($gevraagd, $categorieen, true) ? $gevraagd : 'Pizza';

    // ophalen van de data
    $producten = haalProductenMetIngredienten($db, $categorie);

    
?>







<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bestellen</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="home">
    <?php include 'presentatie/gedeeld/header.php'; ?>
    <?php include 'presentatie/gedeeld/header_banner.php'; ?>
        <nav class="product-tabs">
        <ul>
            <li><a href="?categorie=Pizza">Pizza</a></li>
            <li><a href="?categorie=Voorgerecht">Voorgerecht</a></li>
            <li><a href="?categorie=Maaltijd">Maaltijd</a></li>
            <li><a href="?categorie=Drank">Drank</a></li>
        </ul>
    </nav>
    <main>
        <section class="producten">
            <article class="product-card">
                <h3>Naam</h3>
                <p class="prijs">€x.xx</p>
                <ul class="ingredienten">
                    <li>tomaat</li>
                    <li>mozzarella</li>
                </ul>
            </article>
            <article class="product-card">
                <h3>Naam</h3>
                <p class="prijs">€x.xx</p>
                <ul class="ingredienten">
                    <li>tomaat</li>
                    <li>mozzarella</li>
                    <li>tomaat</li>
                    <li>mozzarella</li>
                    <li>tomaat</li>
                    <li>mozzarella</li>
                    <li>tomaat</li>
                    <li>mozzarella</li>
                </ul>
            </article>
            <article class="product-card">
                <h3>Naam</h3>
                <p class="prijs">€x.xx</p>
                <ul class="ingredienten">
                    <li>tomaat</li>
                    <li>mozzarella</li>
                </ul>
            </article>
            <article class="product-card">
                <h3>Naam</h3>
                <p class="prijs">€x.xx</p>
                <ul class="ingredienten">
                    <li>tomaat</li>
                    <li>mozzarella</li>
                </ul>
            </article>
        </section>

        <aside class="winkelmandje">
            <header>
                <img src="./images/receipt-img.png" alt="Het pizzeria logo op de bon, een kat die een pizza presenteert met een moerbout op de achtergrond.">
                <h2>Je bestelling</h2>
            </header>
            <ul class="bestelling-regels">
                <li class="bestelling-regel">
                    <span class="aantal">1x</span>
                    <span class="naam-product">pizza mozzerella</span>
                    <span class="prijs-product">€12.00</span>
                </li>
                <li class="bestelling-regel">
                    <span class="aantal">1x</span>
                    <span class="naam-product">pizza salami</span>
                    <span class="prijs-product">€12.00</span>
                </li>
                <li class="bestelling-regel">
                    <span class="aantal">1x</span>
                    <span class="naam-product">pizza fungi</span>
                    <span class="prijs-product">€12.00</span>
                </li>
                <li class="bestelling-regel">
                    <span class="aantal">1x</span>
                    <span class="naam-product">pizza pepperoni</span>
                    <span class="prijs-product">€12.00</span>
                </li>
            </ul>
            <footer>
                <p class="totaal">Totaal: €x.xx</p>
                <form action="bevestig_bestelling.php" method="post">
                    <button type="submit">Bestellen</button>
                </form>
                
            </footer>

        </aside>
    </main>
    <?php include 'presentatie/gedeeld/footer.php'; ?>
</body>
</html>