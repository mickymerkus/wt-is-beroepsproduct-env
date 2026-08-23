<?php
// Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
// Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
// het script voordat er iets wordt uitgevoerd of getoond.
if (!defined('TOEGANG_VIA_CONTROLLER')) {
    http_response_code(403);
    exit;
}
?>
<?php include __DIR__ . '/categorietabs.php' ?>

<main>
    <section class="producten">
        <?php if (!$producten): ?>
            <p> Er zijn geen producten in deze categorie.</p>
        <?php endif; ?>

        <?php foreach ($producten as $index => $product): ?>
            <?php include __DIR__ . '/productkaart.php'; ?>
        <?php endforeach ?>
    </section>

    <?php include __DIR__ . '/winkelmandje.php'; ?>
</main>