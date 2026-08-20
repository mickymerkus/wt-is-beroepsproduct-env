<article class="product-card">
    <h3><?=  htmlspecialchars($product['naam']) ?></h3>
    <!-- Verander de decimalen naar nederlandse notatie -->
    <p class="prijs">&euro; <?= number_format($product['prijs'], 2, ',', '.') ?></p>
    
    <?php if ($product['ingredienten']): ?>
        <ul class="ingredienten">
            <?php foreach ($product['ingredienten'] as $ingredient): ?>
                <li><?= htmlspecialchars($ingredient) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form class="toevoegen-formulier" action="index.php" method="post">
            <input type="hidden" name="actie" value="toevoegen">
            <input type="hidden" name="product" value="<?= htmlspecialchars($product['naam']) ?>">
            <input type="hidden" name="categorie" value= <?= htmlspecialchars($categorie) ?>>

            <label for="aantal-<?= $index ?>">Aantal</label>
            <input type="number" id="aantal-<?= $index ?>" value="1" min="1" max=<?= MAX_AANTAL ?>>

            <button type="submit">Toevoegen</button>
    </form>
</article>