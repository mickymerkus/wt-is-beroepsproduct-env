<article class="product-card">
    <h3><?=  htmlspecialchars($product['naam']) ?></h3>
    <!-- Verander de decimalen naar nederlandse notatie -->
    <p class="prijs">€; <?= number_format($product['prijs'], 2, ',', '.') ?></p>
    
    <?php if ($product['ingredienten']): ?>
        <ul class="ingredienten">
            <?php foreach ($product['ingredienten'] as $ingredient): ?>
                <li><?= htmlspecialchars($ingredient) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</article>