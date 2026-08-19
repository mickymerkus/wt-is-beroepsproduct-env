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
    <header>

    </header>
    <div class="banner"><img class="banner-img" src="./images/banner.png" alt="een getekend plaatje met een pizza-oven en een italiaans landschap in zonnige kleuren."></div>
    <nav class="product-tabs">
        <ul>
            <li><a href="?category=Pizza">Pizza</a></li>
            <li><a href="?category=Voorgerecht">Voorgerecht</a></li>
            <li><a href="?category=Maaltijd">Maaltijd</a></li>
            <li><a href="?category=Drank">Drank</a></li>
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
                <form action="bevestig_bestelling.html" method="post">
                    <button type="submit">Bestellen</button>
                </form>
                
            </footer>

        </aside>
    </main>
    <footer>

    </footer>
</body>
</html>