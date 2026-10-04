<?php include __DIR__ . '/categorietabs.php' ?>

<main>
    <section class="producten">
        <?php if (!$producten): ?>
            <p> Er zijn geen producten in deze categorie.</p>
        <?php endif; ?>

        <?php foreach ($producten as $index => $product): ?>
            <?php include __DIR__ . '/productkaart.php'; ?>
        <?php endforeach ?>

        <?php include __DIR__ . '/gedeeld/paginatie.php'; ?>
    </section>

    <?php include __DIR__ . '/winkelmandje.php'; ?>
</main>