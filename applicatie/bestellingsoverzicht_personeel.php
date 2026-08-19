<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personeel Homepage</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="bestellingsoverzicht_personeel.php"><img class="header-logo" src="./images/header-logo.png" alt="pizzeria logo en link naar keuken-overzicht"></a>
        <a class="rol-wissel" href="bestellingsoverzicht_bezorger.php">Bekijk bezorger-overzicht</a>
        <a class="logout-knop" href="login.php">Uitloggen</a>
    </header>
    <main class="column-card-container">
        <article class="bestelling-card">
            <header class="bestelling-card-header">
                <div class="status-box in-wachtrij">In wachtrij</div>
                <p class="order-no">Bestelnummer: 1021</p>
            </header>
            <section class="order-details">
                <ul class="bestelling-regels">
                    <li class="bestelling-regel">
                        <span class="aantal">1x</span>
                        <span class="naam-product">pizza mozzerella</span>
                        <span class="ingredienten">mozzarella, tomaat</span>
                    </li>
                    <li class="bestelling-regel">
                        <span class="aantal">1x</span>
                        <span class="naam-product">pizza salami</span>
                        <span class="ingredienten">salami, mozzarella, tomaat</span>
                    </li>
                </ul>
            </section>
            <footer class="bestelling-card-footer">
                <label for="status-1021">Status veranderen:</label>
                <select name="verander-status-bestelling" id="status-1021">
                    <option value="in-wachtrij">In wachtrij</option>
                    <option value="wordt-gemaakt">Wordt gemaakt</option>
                    <option value="in-de-oven">In de oven</option>
                    <option value="klaar-voor-bezorging">Klaar voor bezorging</option>
                    <option value="on-hold">On hold</option>
                    <option value="geannuleerd">Geannuleerd</option>
                </select>
            </footer>
        </article>
        <article class="bestelling-card">
            <header class="bestelling-card-header">
                <div class="status-box wordt-gemaakt">Wordt gemaakt</div>
                <p class="order-no">Bestelnummer: 1022</p>
            </header>
            <section class="order-details">
                <ul class="bestelling-regels">
                    <li class="bestelling-regel">
                        <span class="aantal">2x</span>
                        <span class="naam-product">pizza fungi</span>
                        <span class="ingredienten">champignons, mozzarella, tomaat</span>
                    </li>
                </ul>
            </section>
            <footer class="bestelling-card-footer">
                <label for="status-1022">Status veranderen:</label>
                <select name="verander-status-bestelling" id="status-1022">
                    <option value="in-wachtrij">In wachtrij</option>
                    <option value="wordt-gemaakt" selected>Wordt gemaakt</option>
                    <option value="in-de-oven">In de oven</option>
                    <option value="klaar-voor-bezorging">Klaar voor bezorging</option>
                    <option value="on-hold">On hold</option>
                    <option value="geannuleerd">Geannuleerd</option>
                </select>
            </footer>
        </article>
        <article class="bestelling-card">
            <header class="bestelling-card-header">
                <div class="status-box in-de-oven">In de oven</div>
                <p class="order-no">Bestelnummer: 1023</p>
            </header>
            <section class="order-details">
                <ul class="bestelling-regels">
                    <li class="bestelling-regel">
                        <span class="aantal">1x</span>
                        <span class="naam-product">pizza pepperoni</span>
                        <span class="ingredienten">pepperoni, mozzarella, tomaat</span>
                    </li>
                </ul>
            </section>
            <footer class="bestelling-card-footer">
                <label for="status-1023">Status veranderen:</label>
                <select name="verander-status-bestelling" id="status-1023">
                    <option value="in-wachtrij">In wachtrij</option>
                    <option value="wordt-gemaakt">Wordt gemaakt</option>
                    <option value="in-de-oven" selected>In de oven</option>
                    <option value="klaar-voor-bezorging">Klaar voor bezorging</option>
                    <option value="on-hold">On hold</option>
                    <option value="geannuleerd">Geannuleerd</option>
                </select>
            </footer>
        </article>
    </main>
    <footer>

    </footer>
</body>
</html>
