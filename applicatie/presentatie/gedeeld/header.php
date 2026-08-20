    <header>
        <a href="index.php">
            <img class="header-logo" src="./images/icon.png" alt="Logo van pizzeria Sole Machina">
        </a>

        <nav class="hoofdnavigatie">
            <ul>
                <li><a href="index.php">Menu</a></li>
                <li>
                    <a href="bevestig_bestelling.php">
                        Winkelmandje
                        <?php if ($aantalInMandje > 0):   ?>
                            <span class="mandje-teller"><?= (int) $aantalInMandje ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="login.php">Inloggen</a></li>
                <li><a href="registratie.php">Registreren</a></li>
                <li><a href="privacy.php">Privacy</a></li>
            </ul>

        </nav>
    </header>