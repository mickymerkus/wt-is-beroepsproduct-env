
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bevestig Bestelling</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bevestig-bestelling">
    <header>

    </header>
    <div class="banner"><img class="banner-img" src="./images/banner.png" alt="een getekend plaatje met een pizza-oven en een italiaans landschap in zonnige kleuren."></div>
    <main>
        <section class="formulier">
            <a href="index.html">Terug naar Homepage.</a>
            <form action="bestelling_geschiedenis.html" method="post">
                <fieldset  class="formulier-sectie">
                    <legend>Afleveradres</legend>
                    <div class="formulier-veld straat">
                        <label for="straat">Straat</label>
                        <input type="text" id="straat" name="straat">
                    </div>
                    <div class="formulier-veld huisnummer">
                        <label for="huisnummer">Huisnummer</label>
                        <input type="text" id="huisnummer" name="huisnummer">
                    </div>
                    <div class="formulier-veld">
                        <label for="stad">Stad</label>
                        <input type="text" id="stad" name="stad">
                    </div>
                    <div class="formulier-veld postcode">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode">
                    </div>
                </fieldset>

                <fieldset  class="formulier-sectie">
                    <legend>Betaalwijze</legend>
                    <div class="formulier-veld radio">
                        <label for="ideal">Ideal</label>
                        <input type="radio" id="ideal" name="betaalwijze" value="ideal">
                    </div>
                    <div class="formulier-veld radio">
                        <label for="contant">Contant</label>
                        <input type="radio" id="contant" name="betaalwijze" value="contant">
                    </div>                                     
                    <select class="bank-dropdown" name="bank" id="bank">
                        <option value="ing">ING</option>
                        <option value="ASN">ASN</option>
                        <option value="rabobank">Rabobank</option>
                    </select>
                </fieldset>
               <button type="submit">Bestelling bevestigen</button>

            </form>
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
            </footer>
        </aside>
    </main>
    <footer>

    </footer>
</body>
</html>