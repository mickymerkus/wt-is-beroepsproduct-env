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
</nav>