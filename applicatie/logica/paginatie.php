<?php

    // Gedeelde paginering, zodat geen lijstquery een onbeperkt aantal rijen ophaalt.

    const ITEMS_PER_PAGINA = 10;

    // Harde bovengrens; de paginagrootte komt bewust niet uit de URL
    const MAX_ITEMS_PER_PAGINA = 50;

    // Paginanummer uit de GET-parameters als veilig getal ('abc' wordt 0, en dus 1)
    function huidigePaginaNummer($invoer, string $sleutel = 'pagina'): int
    {
        $pagina = (int) ($invoer[$sleutel] ?? 1);

        return $pagina < 1 ? 1 : $pagina;
    }

    // Aantal pagina's voor dit aantal items, minimaal 1 ("pagina 1 van 1")
    function aantalPaginas(int $totaalItems, int $perPagina): int
    {
        if ($totaalItems < 1) {
            return 1;
        }

        return (int) ceil($totaalItems / $perPagina);
    }

    // Alles wat een overzicht nodig heeft voor de query (offset/limiet) en de paginaknoppen.
    // $totaalItems komt uit een COUNT-query.
    function bouwPaginering($invoer, int $totaalItems, int $perPagina = ITEMS_PER_PAGINA): array
    {
        if ($perPagina < 1) {
            $perPagina = ITEMS_PER_PAGINA;
        }

        if ($perPagina > MAX_ITEMS_PER_PAGINA) {
            $perPagina = MAX_ITEMS_PER_PAGINA;
        }

        $paginas = aantalPaginas($totaalItems, $perPagina);
        $pagina  = huidigePaginaNummer($invoer);

        // Afkappen op de laatste pagina, anders levert ?pagina=999999 een enorme OFFSET op
        if ($pagina > $paginas) {
            $pagina = $paginas;
        }

        return [
            'pagina'        => $pagina,
            'perPagina'     => $perPagina,
            'offset'        => ($pagina - 1) * $perPagina,
            'aantalPaginas' => $paginas,
            'totaalItems'   => $totaalItems,
            'heeftVorige'   => $pagina > 1,
            'heeftVolgende' => $pagina < $paginas,
        ];
    }

    // URL naar een andere pagina, met behoud van parameters zoals ?categorie=Pizza
    function paginaUrl(string $basisUrl, int $pagina, array $extraParameters = []): string
    {
        $parameters = array_merge($extraParameters, ['pagina' => $pagina]);

        return $basisUrl . '?' . http_build_query($parameters);
    }
