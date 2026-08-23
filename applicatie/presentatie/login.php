<?php
// Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
// Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
// het script voordat er iets wordt uitgevoerd of getoond.
if (!defined('TOEGANG_VIA_CONTROLLER')) {
    http_response_code(403);
    exit;
}
?>
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
