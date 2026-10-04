<?php 
    require_once __DIR__ . '/../data/producten.php';

    // Max zetten op de producthoeveelheid voor de zekerheid
    const MAX_AANTAL = 50;

    // Haal winkelmandje op uit de sessie
    function haalWinkelmandje() {
        return $_SESSION['winkelmandje'] ?? [];
    }

    function bewaarWinkelmandje($winkelmandje) {
        $_SESSION['winkelmandje'] = $winkelmandje;
    }

    // CRUD functie's

    // update het winkelmandje met de nieuwe aantallen en bewaar in de sessie
    function voegToeAanWinkelmandje($naam, $aantal) {
        $winkelmandje = haalWinkelmandje();

        $nieuwAantal = ($winkelmandje[$naam] ?? 0) + $aantal;

        //Zorgen dat het aantal onder de max blijft
        if ($nieuwAantal > MAX_AANTAL) {
            $nieuwAantal = MAX_AANTAL;
        }

        $winkelmandje[$naam] = $nieuwAantal;
        bewaarWinkelmandje($winkelmandje);
    }

    // update het aantal van een product in het mandje. Bij 0 verwijderen we hem uit het mandje
    function wijzigAantal($naam, $nieuwAantal) {
        if ($nieuwAantal <= 0) {
            verwijderuitWinkelmandje($naam);
            return;
        }

        $winkelmandje = haalWinkelmandje();

        if ($nieuwAantal > MAX_AANTAL) {
            $nieuwAantal = MAX_AANTAL;
        }

        //Kan alleen wijzigen als het al bestaat, dus check dit
        if (isset($winkelmandje[$naam])) {
            $winkelmandje[$naam] = $nieuwAantal;
            bewaarWinkelmandje($winkelmandje);
        }
    }

    // verlaag het aantal van een product met één. Bij 0 verwijdert wijzigAantal het product uit het mandje
    function verlaagInWinkelmandje($naam) {
        $winkelmandje = haalWinkelmandje();

        wijzigAantal($naam, ($winkelmandje[$naam] ?? 0) - 1);
    }


    // Haal een product uit het mandje
    function verwijderUitWinkelmandje($naam) {
        $winkelmandje = haalWinkelmandje();
        unset($winkelmandje[$naam]);
        bewaarWinkelmandje($winkelmandje);
    }

    function aantalArtikelenInMandje() {
        return array_sum(haalWinkelmandje());
    }

    // Combineert de producten die in het winkelmandje zitten en de hoeveelheid met de prijs per product uit de database
    function haalWinkelmandjeRegels($verbinding) {
        $winkelmandje = haalWinkelmandje();

        if (!$winkelmandje) {
            return [];
        }

        // Vraag alleen de prijzen op van wat er daadwerkelijk in het mandje ligt,
        // in plaats van de hele Product-tabel. De keys van het mandje zijn de
        // productnamen.
        $prijzen = haalPrijzenVanProducten($verbinding, array_keys($winkelmandje));

        $regels = [];

        foreach ($winkelmandje as $naam => $aantal) {
            // als prijs niet in database te vinden is, dan verwijder hem uit het mandje
            if (!isset($prijzen[$naam])) {
                verwijderUitWinkelmandje($naam);
                continue;
            }

            $prijs = (float) $prijzen[$naam];

            // Voeg toe aan array
            $regels[] = [
                'naam' => $naam,
                'aantal' => $aantal,
                'prijs' => $prijs,
                'regelTotaal' => $prijs * $aantal
            ];
        }

        return $regels;
    }


    function berekenTotaal($regels) {
        $totaal = 0;

        foreach ($regels as $regel) {
            $totaal += $regel['regelTotaal'];
        }

        return $totaal;
    }

    // Verwerk een POST op het winkelmandje. Past de sessie aan.
    function verwerkWinkelmandjeActie($verbinding, $invoer) {
        $actie = $invoer['actie'] ?? '';
        $naam = $invoer['product'] ?? '';
        $aantal = (int) ($invoer['aantal'] ?? 0);

        // beschermen tegen verkeerde datatypen
        if (!is_string($naam) || $naam === '') {
            return;
        }

        // Valideren of het product wel in de database bestaat
        if (!bestaatProduct($verbinding, $naam)) {
            return;
        }

        if ($actie === 'toevoegen') {
            // als 0 dan doen we niks
            if ($aantal >= 1) {
                voegToeAanWinkelmandje($naam, $aantal);
            }
        } elseif ($actie === 'verhogen') {
            // 0 of minder betekent de regel weghalen
            voegToeAanWinkelmandje($naam, 1);
        } elseif ($actie === 'verlagen') {
            verlaagInWinkelmandje($naam);
        } elseif ($actie === 'verwijderen') {
            verwijderUitWinkelmandje($naam);
        }
    }