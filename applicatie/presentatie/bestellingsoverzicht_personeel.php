<?php
// Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
// Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
// het script voordat er iets wordt uitgevoerd of getoond.
if (!defined('TOEGANG_VIA_CONTROLLER')) {
    http_response_code(403);
    exit;
}
?>
<main class="personeel-overzicht">
    <h1>Keukenoverzicht</h1>

    <?php if (!$bestellingen): ?>
        <p>Er staan op dit moment geen bestellingen in de keuken.</p>
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
                    <h3>Producten</h3>

                    <ul class="bestelling-regels">
                        <?php foreach ($bestelling['regels'] as $regel): ?>
                            <li class="bestelling-regel">
                                <span class="aantal"><?= (int) $regel['aantal'] ?>x</span>
                                <span class="naam-product"><?= htmlspecialchars($regel['product_naam']) ?></span>
                                <?php if ($regel['ingredienten']): ?>
                                    <ul class="ingredienten">
                                        <?php foreach ($regel['ingredienten'] as $ingredient): ?>
                                            <li><?= htmlspecialchars($ingredient) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <footer class="bestelling-card-footer">
                    <form action="bestellingsoverzicht_personeel.php" method="post" class="status-formulier">
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
