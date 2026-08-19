<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="icon" type="image/png" href="./images/icon.png">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <a href="index.php"><img class="header-logo"  src="./images/header-logo.png" alt="pizzeria logo en link naar homepage"></a> 
        Pizzeria Sole Machina

    </header>
    <main class="login-page">
        <section class="login-card">
            <h2>Inloggen</h2>
            <img class="login-logo" src="./images/logo.png" alt="pizzeria logo">
            <form method="post">
                <div class="formulier-veld">
                    <label for="gebruikersnaam">Gebruikersnaam:</label>
                    <input type="text" name="gebruikersnaam" id="gebruikersnaam">
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
    <footer>
        <a href="privacy.php">Privacy Statement</a>
    </footer>
</body>
</html>