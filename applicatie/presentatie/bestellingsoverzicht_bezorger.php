<main class="personeel-overzicht">
    <h1>Bezorgeroverzicht</h1>

    <?php if (!$bestellingen): ?>
        <p>Er zijn op dit moment geen bestellingen die moeten worden bezorgd.</p>
    <?php endif; ?>

    <div class="column-card-container">
        <?php foreach ($bestellingen as $bestelling): ?>
            <article class="bestelling-card">
                <header class="bestelling-card-header">
                    <div class="status-box <?= htmlspecialchars($bestelling['statusKlasse']) ?>">
                        <?= htmlspecialchars($bestelling['statusTekst']) ?>
                    </div>
                    <h2 class="order-no">Bestelnummer: <?= (int) $bestelling['bestel_nummer'] ?></h2>
                </header>

                <section class="order-details">
                    <h3>Bestelgegevens</h3>

                    <div class="klant-gegevens">
                        <p class="klant-naam"><?= htmlspecialchars($bestelling['klant_naam']) ?></p>
                        <p class="klant-adres"><?= htmlspecialchars($bestelling['adres'] ?? '') ?></p>
                    </div>

                    <ul class="bestelling-regels">
                        <?php foreach ($bestelling['regels'] as $regel): ?>
                            <li class="bestelling-regel">
                                <span class="aantal"><?= (int) $regel['aantal'] ?>x</span>
                                <span class="naam-product"><?= htmlspecialchars($regel['product_naam']) ?></span>
                                <span class="prijs-product">&euro; <?= number_format($regel['regelTotaal'], 2, ',', '.') ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p class="totaal">Totaal: &euro; <?= number_format($bestelling['totaal'], 2, ',', '.') ?></p>
                </section>

                <footer class="bestelling-card-footer">
                    <form action="bestellingsoverzicht_bezorger.php" method="post" class="status-formulier">
                        <input type="hidden" name="bestelling" value="<?= (int) $bestelling['bestel_nummer'] ?>">

                        <label for="status-<?= (int) $bestelling['bestel_nummer'] ?>">Status veranderen:</label>
                        <select name="status" id="status-<?= (int) $bestelling['bestel_nummer'] ?>">
                            <?php foreach ($statussen as $code => $tekst): ?>
                                <option value="<?= (int) $code ?>" <?= ((int) $bestelling['status'] === $code) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tekst) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <button type="submit" name="actie" value="status_wijzigen">Opslaan</button>
                    </form>
                </footer>
            </article>
        <?php endforeach; ?>
    </div>
</main>
