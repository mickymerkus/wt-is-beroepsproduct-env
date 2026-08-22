    <header>
        <a href="index.php">
            <img class="header-logo" src="./images/icon.png" alt="Logo van pizzeria Sole Machina">
        </a>

        <nav class="hoofdnavigatie">
            <ul>
                <?php if (isPersoneel()): ?>
                    <li><a href="bestellingsoverzicht_personeel.php">Keuken</a></li>
                    <li><a href="bestellingsoverzicht_bezorger.php">Bezorging</a></li>
                <?php else: ?>
                    <li><a href="index.php">Menu</a></li>
                    <li>
                        <a href="bevestig_bestelling.php">
                            Winkelmandje
                            <?php if ($aantalInMandje > 0): ?>
                                <span class="mandje-teller"><?= (int) $aantalInMandje ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="bestelling_geschiedenis.php">Mijn bestellingen</a></li>

                    <?php if (!isIngelogd()): ?>
                        <li><a href="login.php">Inloggen</a></li>
                        <li><a href="registratie.php">Registreren</a></li>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- Uitloggen en privacy gelden voor iedereen, ongeacht de rol -->
                <?php if (isIngelogd()): ?>
                    <li>
                        <form action="uitloggen.php" method="post" class="uitlog-formulier">
                            <button type="submit">Uitloggen</button>
                        </form>
                    </li>
                <?php endif; ?>

                <li><a href="privacy.php">Privacy</a></li>
            </ul>
        </nav>
    </header>