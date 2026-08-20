<?php include __DIR__ . '/categorietabs.php' ?>

<main>
    <section class="producten">
        <?php if (!$producten): ?>
            <p> Er zijn geen producten in deze categorie.</p>
        <?php endif; ?>

        <?php foreach ($producten as $product): ?>
            <?php include __DIR__ . '/productkaart.php'; ?>
        <?php endforeach ?>
    </section>

    <?php include __DIR__ . '/winkelmandje.php'; ?>
</main>