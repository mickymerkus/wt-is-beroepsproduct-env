<main class="bestelling-geschiedenis">
    <h1>Besteloverzicht</h1>

    <!-- Bevestiging van bestelling-->
    <?php if ($isGeplaatst): ?>
        <p class="melding-geslaagd">Bedankt! Je bestelling is geplaatst.</p>
    <?php endif; ?>

    <!-- Als je geen bestellingen hebt dit laten zien. -->
    <?php if (!$bestellingen): ?>
        <p>Je hebt nog geen bestellingen geplaatst.</p>
    <?php endif; ?>

    <?php foreach ($bestellingen as $bestelling): ?>
        <article class="bestelling-met-status">
            <header>
                <h2>Bestelnummer: <?= (int) $bestelling['bestel_nummer'] ?></h2>
                <p class="status-box <?= htmlspecialchars($bestelling['statusKlasse']) ?>">
                    Status: <?= htmlspecialchars($bestelling['statusTekst']) ?>
                </p>
                <span>Datum: <?= htmlspecialchars(date('d-m-Y', strtotime($bestelling['datum']))) ?></span>
                <span>Bezorgadres: <?= htmlspecialchars($bestelling['adres'] ?? '') ?></span>
            </header>

            <h3>Details:</h3>

            <ul class="bestelling-regels">
                <?php foreach ($bestelling['regels'] as $regel): ?>
                    <li class="bestelling-regel">
                        <span class="aantal"><?= $regel['aantal'] ?>x</span>
                        <span class="naam-product"><?= htmlspecialchars($regel['product_naam']) ?></span>
                        <span class="prijs-product">&euro; <?= number_format($regel['regelTotaal'], 2, ',', '.') ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>

            <footer>
                <p class="totaal">Totaal: &euro; <?= number_format($bestelling['totaal'], 2, ',', '.') ?></p>
            </footer>
        </article>
    <?php endforeach; ?>
</main>
