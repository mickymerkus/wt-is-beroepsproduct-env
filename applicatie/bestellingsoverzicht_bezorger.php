<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bezorger Homepage</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="bestellingsoverzicht_bezorger.html"><img class="header-logo" src="./images/header-logo.png" alt="pizzeria logo en link naar bezorger-overzicht"></a>
        <a class="rol-wissel" href="bestellingsoverzicht_personeel.html">Bekijk keuken-overzicht</a>
        <a class="logout-knop" href="login.html">Uitloggen</a>
    </header>
    <main class="column-card-container">
        <article class="bestelling-card">
            <header class="bestelling-card-header">
                <div class="status-box klaar-voor-bezorging">Klaar voor bezorging</div>
                <p class="order-no">Bestelnummer: 1019</p>
            </header>
            <section class="order-details">
                <div class="klant-gegevens">
                    <p class="klant-naam">Jaap de Vries</p>
                    <p class="klant-adres">Meijhorst 3010, 4545DD Nijmegen </p>
                    <p class="klant-betaalmethode">Betaalmethode: iDeal</p>
                </div>
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
                </ul>
            </section>
            <footer class="bestelling-card-footer">
                <button type="button" class="status-knop">Wordt bezorgd</button>
                <button type="button" class="print-knop">Print bon</button>
            </footer>
        </article>
        <article class="bestelling-card">
            <header class="bestelling-card-header">
                <div class="status-box wordt-bezorgd">Wordt bezorgd</div>
                <p class="order-no">Bestelnummer: 1018</p>
            </header>
            <section class="order-details">
                <div class="klant-gegevens">
                    <p class="klant-naam">Voornaam achternaam</p>
                    <p class="klant-adres">straat huisnummer stad postcode</p>
                    <p class="klant-betaalmethode">Betaalmethode: Contant</p>
                </div>
                <ul class="bestelling-regels">
                    <li class="bestelling-regel">
                        <span class="aantal">2x</span>
                        <span class="naam-product">pizza fungi</span>
                        <span class="prijs-product">€12.00</span>
                    </li>
                </ul>
            </section>
            <footer class="bestelling-card-footer">
                <button type="button" class="status-knop" disabled>Wordt bezorgd</button>
                <button type="button" class="print-knop">Print bon</button>
            </footer>
        </article>
    </main>
    <footer>

    </footer>
</body>
</html>
