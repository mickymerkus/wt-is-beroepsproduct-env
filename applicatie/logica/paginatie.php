<?php

    // Gedeelde paginering voor alle overzichten die een lijst uit de database tonen.
    // Het doel: een query haalt nooit een onbeperkt aantal rijen op, hoe groot
    // de tabel ook wordt. Elke lijstquery krijgt dus een offset en een limiet mee.

    // Hoeveel items standaard op één pagina staan.
    const ITEMS_PER_PAGINA = 10;

    // Harde bovengrens op de paginagrootte. De paginagrootte komt bewust niet
    // uit de URL, maar wordt door de pagina zelf meegegeven. Zo kan een bezoeker
    // er geen ?per=100000 van maken. Deze constante is de laatste vangrail.
    const MAX_ITEMS_PER_PAGINA = 50;

    // Haal het paginanummer uit de GET-parameters en maak er een veilig getal van.
    // (int) maakt van alles een getal, dus 'abc' wordt 0 en wordt hieronder 1.
    function huidigePaginaNummer($invoer, string $sleutel = 'pagina'): int
    {
        $pagina = (int) ($invoer[$sleutel] ?? 1);

        return $pagina < 1 ? 1 : $pagina;
    }

    // Reken uit hoeveel pagina's er nodig zijn voor dit aantal items.
    // Altijd minimaal 1, zodat een leeg overzicht nog steeds "pagina 1 van 1" is.
    function aantalPaginas(int $totaalItems, int $perPagina): int
    {
        if ($totaalItems < 1) {
            return 1;
        }

        return (int) ceil($totaalItems / $perPagina);
    }

    // Bouw alles wat een overzicht nodig heeft om één pagina op te halen en
    // de paginaknoppen te tonen. Het totaal aantal items komt uit een COUNT-query,
    // want zonder totaal weten we niet hoeveel pagina's er zijn.
    //
    // Belangrijk: het paginanummer wordt afgekapt op de laatste pagina. Zonder dat
    // zou ?pagina=999999 een enorme OFFSET opleveren en de database alsnog laten
    // zoeken door de hele tabel.
    function bouwPaginering($invoer, int $totaalItems, int $perPagina = ITEMS_PER_PAGINA): array
    {
        // De paginagrootte binnen de grenzen houden.
        if ($perPagina < 1) {
            $perPagina = ITEMS_PER_PAGINA;
        }

        if ($perPagina > MAX_ITEMS_PER_PAGINA) {
            $perPagina = MAX_ITEMS_PER_PAGINA;
        }

        $paginas = aantalPaginas($totaalItems, $perPagina);
        $pagina  = huidigePaginaNummer($invoer);

        // Afkappen op de laatste bestaande pagina.
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

    // Bouw de URL naar een andere pagina en houd de bestaande parameters
    // (zoals ?categorie=Pizza) in stand.
    function paginaUrl(string $basisUrl, int $pagina, array $extraParameters = []): string
    {
        $parameters = array_merge($extraParameters, ['pagina' => $pagina]);

        return $basisUrl . '?' . http_build_query($parameters);
    }
