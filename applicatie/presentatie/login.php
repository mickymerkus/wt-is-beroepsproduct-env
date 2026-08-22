<main>
    <section class="login-card">
        <h2>Inloggen</h2>
        <img class="login-logo" src="./images/logo.png" alt="pizzeria logo">

        <?php if ($foutmelding !== ''): ?>
            <p class="foutmelding"><?= htmlspecialchars($foutmelding) ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="formulier-veld">
                <label for="gebruikersnaam">Gebruikersnaam:</label>
                <input type="text" name="gebruikersnaam" id="gebruikersnaam"
                       value="<?= htmlspecialchars($_POST['gebruikersnaam'] ?? '') ?>">
            </div>
            <div class="formulier-veld">
                <label for="wachtwoord">Wachtwoord</label>
                <input type="password" name="wachtwoord" id="wachtwoord">
            </div>
            <button type="submit">Inloggen</button>
        </form>
        <a href="registratie.php">Account aanmaken</a>
    </section>
</main>
