<?php
    // Paginaknoppen onder een overzicht.
    //
    // Verwacht:
    //   $paginering         - array uit bouwPaginering()
    //   $pagineringBasisUrl - bestandsnaam waar de links naartoe gaan
    //   $pagineringExtra    - optioneel: parameters die mee moeten in de link
    //                         (bijvoorbeeld de gekozen categorie)
    //
    // Bij één pagina heeft navigatie geen zin, dan tonen we niks.

    $extra = $pagineringExtra ?? [];

    if (empty($paginering) || $paginering['aantalPaginas'] <= 1) {
        return;
    }
?>

<nav class="paginering" aria-label="Paginanavigatie">
    <?php if ($paginering['heeftVorige']): ?>
        <a class="paginering-knop"
           href="<?= htmlspecialchars(paginaUrl($pagineringBasisUrl, $paginering['pagina'] - 1, $extra)) ?>"
           rel="prev">&laquo; Vorige</a>
    <?php else: ?>
        <span class="paginering-knop uitgeschakeld">&laquo; Vorige</span>
    <?php endif; ?>

    <ol class="paginering-nummers">
        <?php for ($nummer = 1; $nummer <= $paginering['aantalPaginas']; $nummer++): ?>
            <li>
                <?php if ($nummer === $paginering['pagina']): ?>
                    <span class="paginering-nummer actief" aria-current="page"><?= (int) $nummer ?></span>
                <?php else: ?>
                    <a class="paginering-nummer"
                       href="<?= htmlspecialchars(paginaUrl($pagineringBasisUrl, $nummer, $extra)) ?>"><?= (int) $nummer ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>
    </ol>

    <?php if ($paginering['heeftVolgende']): ?>
        <a class="paginering-knop"
           href="<?= htmlspecialchars(paginaUrl($pagineringBasisUrl, $paginering['pagina'] + 1, $extra)) ?>"
           rel="next">Volgende &raquo;</a>
    <?php else: ?>
        <span class="paginering-knop uitgeschakeld">Volgende &raquo;</span>
    <?php endif; ?>

    <p class="paginering-info">
        Pagina <?= (int) $paginering['pagina'] ?> van <?= (int) $paginering['aantalPaginas'] ?>
        (<?= (int) $paginering['totaalItems'] ?> in totaal)
    </p>
</nav>
