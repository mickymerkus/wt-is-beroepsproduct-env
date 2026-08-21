<aside class="winkelmandje">
    <header>
        <img src="./images/receipt-img.png" alt="Het pizzeria logo op de bon, een kat die een pizza presenteert met een moerbout op de achtergrond.">
        <h2>Je bestelling</h2>
    </header>

    <?php if (!$winkelmandjeRegels): ?>
        <p class="winkelmandje-leeg">Je winkelmandje is nog leeg.</p>
    <?php else: ?>
        <ul class="bestelling-regels">
            <?php foreach ($winkelmandjeRegels as $regel): ?>
                <li class="bestelling-regel winkelmandje-regel">
                    <form class="regel-formulier" action="<?= htmlspecialchars($winkelmandjeActie) ?>" method="post">
                        <input type="hidden" name="product" value="<?= htmlspecialchars($regel['naam']) ?>">
                        <input type="hidden" name="categorie" value="<?= htmlspecialchars($categorie ?? '') ?>">

                        <button class="knop-aantal" type="submit" name="actie" value="verlagen"
                                aria-label="Eén <?= htmlspecialchars($regel['naam']) ?> minder">&minus;</button>

                        <span class="aantal"><?= (int) $regel['aantal'] ?>x</span>

                        <button class="knop-aantal" type="submit" name="actie" value="verhogen"
                                aria-label="Eén <?= htmlspecialchars($regel['naam']) ?> meer"
                                <?= $regel['aantal'] >= MAX_AANTAL ? 'disabled' : '' ?>>+</button>

                        <span class="naam-product"><?= htmlspecialchars($regel['naam']) ?></span>
                        <span class="prijs-product">&euro; <?= number_format($regel['regelTotaal'], 2, ',', '.') ?></span>

                        <button type="submit" name="actie" value="verwijderen"
                                aria-label="<?= htmlspecialchars($regel['naam']) ?> verwijderen">Verwijderen</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <footer>
        <p class="totaal">Totaal: &euro; <?= number_format($winkelmandjeTotaal, 2, ',', '.') ?></p>

        <?php if (!empty($toonBestelknop)): ?>
            <form action="bevestig_bestelling.php" method="get">
                <!-- Mag alleen bestellen als er iets in het mandje zit. -->
                <button type="submit" <?= $winkelmandjeRegels ? '' : 'disabled' ?>>Bestellen</button>
            </form>
        <?php endif; ?>
    </footer>
</aside>