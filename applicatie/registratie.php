
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account aanmaken</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>

    </header>
    <div class="banner"><img class="banner-img" src="./images/banner.png" alt="een getekend plaatje met een pizza-oven en een italiaans landschap in zonnige kleuren."></div>
    <main>
        <section class="formulier">
            <a href="index.php">Terug naar Homepage.</a>
            <form action="index.php" method="post">
                
                <fieldset class="formulier-sectie">
                    <legend>Accountgegevens</legend>
                    <div class="formulier-veld">
                        <label for="gebruikersnaam">Gebruikersnaam</label>
                        <input type="text" id="gebruikersnaam" name="gebruikersnaam">
                        
                        <label for="wachtwoord">Wachtwoord</label>
                        <input type="text" id="wachtwoord" name="wachtwoord">

                        <label for="bevestig-wachtwoord">Bevestig wachtwoord</label>
                        <input type="text" id="bevestig-wachtwoord" name="bevestig-wachtwoord">
                    </div>
                </fieldset>

                <fieldset class="formulier-sectie">
                    <legend>Adresgegevens</legend>
                    <div class="formulier-veld straat">
                        <label for="straat">Straat</label>
                        <input type="text" id="straat" name="straat">
                    </div>
                    <div class="formulier-veld">
                        <label for="huisnummer">Huisnummer</label>
                        <input type="text" id="huisnummer" name="huisnummer">
                    </div>
                    <div class="formulier-veld">
                        <label for="stad">Stad</label>
                        <input type="text" id="stad" name="stad">
                    </div>
                    <div class="formulier-veld">
                        <label for="postcode">Postcode</label>
                        <input type="text" id="postcode" name="postcode">
                    </div>
                </fieldset>
               <button type="submit">Account aanmaken</button>

            </form>
        </section>
    </main>
    <footer>

    </footer>
</body>
</html>