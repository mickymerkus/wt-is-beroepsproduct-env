<main>
    <!-- Geef de mogelijke foutmeldingen weer -->
    <?php if ($fouten): ?>
        <ul class="foutmeldingen">
            <?php foreach ($fouten as $fout): ?>
                <li><?= htmlspecialchars($fout) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <section class="formulier">
        <form action="registratie.php" method="post">

            <fieldset class="formulier-sectie">
                <legend>Accountgegevens</legend>
                <div class="formulier-veld">
                    <label for="gebruikersnaam">Gebruikersnaam</label>
                    <input type="text" id="gebruikersnaam" name="gebruikersnaam"
                           value="<?= htmlspecialchars($_POST['gebruikersnaam'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="voornaam">Voornaam</label>
                    <input type="text" id="voornaam" name="voornaam"
                           value="<?= htmlspecialchars($_POST['voornaam'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="achternaam">Achternaam</label>
                    <input type="text" id="achternaam" name="achternaam"
                           value="<?= htmlspecialchars($_POST['achternaam'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="wachtwoord">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord">
                </div>
                <div class="formulier-veld">
                    <label for="bevestig-wachtwoord">Bevestig wachtwoord</label>
                    <input type="password" id="bevestig-wachtwoord" name="bevestig-wachtwoord">
                </div>
            </fieldset>

            <fieldset class="formulier-sectie">
                <legend>Adres</legend>
                <div class="formulier-veld straat">
                    <label for="straat">Straat</label>
                    <input type="text" id="straat" name="straat"
                           value="<?= htmlspecialchars($_POST['straat'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="huisnummer">Huisnummer</label>
                    <input type="text" id="huisnummer" name="huisnummer"
                           value="<?= htmlspecialchars($_POST['huisnummer'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="stad">Stad</label>
                    <input type="text" id="stad" name="stad"
                           value="<?= htmlspecialchars($_POST['stad'] ?? '') ?>">
                </div>
                <div class="formulier-veld">
                    <label for="postcode">Postcode</label>
                    <input type="text" id="postcode" name="postcode"
                           value="<?= htmlspecialchars($_POST['postcode'] ?? '') ?>">
                </div>
            </fieldset>
            <button type="submit">Account aanmaken</button>
        </form>
    </section>
</main>
