<!-- Verborgen checkbox die de open/dicht-status van de lade bijhoudt -->
<input type="checkbox" id="mandje-toggle" class="mandje-toggle-input" <?= !empty($mandjeOpen) ? 'checked' : '' ?>>

<nav class="product-tabs">
    <ul>
        <?php foreach ($categorieen as $naam): ?>
            <li>
                <a href="?categorie=<?= urlencode($naam) ?>"
                class="<?= $naam === $categorie ? 'actief' : ''?>"
                >
                    <?= htmlspecialchars($naam) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <label class="mandje-schakelaar" for="mandje-toggle">Winkelmandje</label>
</nav>
