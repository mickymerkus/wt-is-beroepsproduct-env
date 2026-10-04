<?php

    require_once __DIR__ . '/db_connectie.php';

    // Tel hoeveel producten er in een categorie zitten.
    // Nodig om te weten hoeveel pagina's het menu krijgt.
    function telProductenInCategorie($verbinding, $categorie): int
    {
        $sql = '
            SELECT COUNT(*)
            FROM Product
            WHERE type_id = :categorie
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':categorie' => $categorie]);

        return (int) $query->fetchColumn();
    }

    // Haal één pagina producten op van een bepaalde categorie (pizza, drank etc.)
    //
    // Zelfde reden voor de CTE als bij de bestellingen: door de LEFT JOIN op de
    // ingrediënten levert één product meerdere rijen op. Pagineer je die rijen,
    // dan valt een pizza met veel ingrediënten over twee pagina's uiteen.
    // De CTE kiest daarom eerst de producten van deze pagina.
    function haalProductenMetIngredienten($verbinding, $categorie, int $perPagina, int $offset): array
    {
        $sql = '
            WITH pagina AS (
                SELECT p.name
                FROM Product p
                WHERE p.type_id = :categorie
                ORDER BY p.name
                OFFSET :offset ROWS
                FETCH NEXT :per_pagina ROWS ONLY
            )
            SELECT p.name, p.price, pi.ingredient_name
            FROM pagina
            JOIN Product p ON p.name = pagina.name
            LEFT JOIN Product_Ingredient pi ON pi.product_name = p.name
            ORDER BY p.name, pi.ingredient_name
        ';

        $query = $verbinding->prepare($sql);

        // OFFSET en FETCH NEXT eisen een integer, zie de uitleg in bestellingen.php.
        $query->bindValue(':categorie', $categorie);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->bindValue(':per_pagina', $perPagina, PDO::PARAM_INT);
        $query->execute();

        $data = $query->fetchAll();
        return _groepeerOpProduct($data);
    }

    // Bouw een lijst placeholders (:naam0, :naam1, ...) voor een IN-clausule.
    // De waarden gaan nog steeds als parameter mee; alleen het AANTAL
    // placeholders is dynamisch. Zo blijft de query een prepared statement en
    // komt er nooit gebruikersinvoer in de SQL-string terecht.
    function _maakPlaceholders(array $waarden, string $prefix): array
    {
        $placeholders = [];
        $parameters   = [];

        foreach (array_values($waarden) as $index => $waarde) {
            $sleutel = ':' . $prefix . $index;

            $placeholders[]       = $sleutel;
            $parameters[$sleutel] = $waarde;
        }

        return [implode(', ', $placeholders), $parameters];
    }

    //Zorg dat de ingredienten bij elk product komen te staan als list in een array
    function _groepeerOpProduct(array $rijen): array 
    {
        $producten = [];

        foreach ($rijen as $rij) {
            $naam = $rij['name'];


            if (!isset($producten[$naam])) {
                $producten[$naam] = [
                    'naam' => $naam,
                    'prijs' => $rij['price'],
                    'ingredienten' => [],
                ];
            }

            if ($rij['ingredient_name'] !== null) {
                $producten[$naam]['ingredienten'][] = $rij['ingredient_name'];
            }
        }
        return array_values($producten);
    }

    // Haal de prijzen op van precies de producten die gevraagd worden.
    //
    // Deze functie wordt NIET gepagineerd en dat is bewust: het resultaat is een
    // opzoeklijst (naam => prijs) die het winkelmandje compleet nodig heeft.
    // Zou je hier een pagina van maken, dan verdwijnen producten stilletjes uit
    // het mandje, want haalWinkelmandjeRegels gooit alles weg waarvan het de prijs
    // niet kan vinden. In plaats van pagineren begrenzen we de query daarom op de
    // namen die we echt nodig hebben: nooit meer rijen dan er producten in het
    // mandje zitten, in plaats van de hele Product-tabel.
    function haalPrijzenVanProducten($verbinding, array $productNamen): array
    {
        // Geen namen gevraagd? Dan hoeft de database niks te doen.
        if (!$productNamen) {
            return [];
        }

        [$placeholders, $parameters] = _maakPlaceholders($productNamen, 'naam');

        $sql = '
            SELECT name, price
            FROM Product
            WHERE name IN (' . $placeholders . ')
        ';

        $query = $verbinding->prepare($sql);
        $query->execute($parameters);

        $prijzen = [];

        foreach ($query->fetchAll() as $rij) {
            $prijzen[$rij['name']] = $rij['price'];
        }

        return $prijzen;
    }

    // Valideer of een bepaald product ook echt in de database te vinden is.
    function bestaatProduct($verbinding, $naam): bool
    {
        $sql = '
            SELECT 1
            FROM Product
            WHERE name = :naam
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':naam' => $naam]);

        //Check of er een rij is mbv fetchColumn()
        return $query->fetchColumn() !== false;
    }

    // Haal de ingrediënten op van precies de producten die gevraagd worden.
    // Is nodig voor het bestellingsoverzicht anders moet er moeilijk
    // gedaan worden met arrays.
    //
    // Ook deze is een opzoeklijst en wordt dus niet gepagineerd, maar begrensd:
    // het keukenoverzicht vraagt alleen de producten op die op de huidige pagina
    // met bestellingen staan. De hele Product_Ingredient-tabel ophalen is niet
    // nodig en groeit mee met de kaart.
    function haalIngredientenPerProduct($verbinding, array $productNamen): array
    {
        if (!$productNamen) {
            return [];
        }

        [$placeholders, $parameters] = _maakPlaceholders($productNamen, 'product');

        $sql = '
            SELECT product_name, ingredient_name
            FROM Product_Ingredient
            WHERE product_name IN (' . $placeholders . ')
            ORDER BY product_name, ingredient_name
        ';

        $query = $verbinding->prepare($sql);
        $query->execute($parameters);

        $ingredienten = [];

        foreach ($query->fetchAll() as $rij) {
            // maak een rij aan voor het product als die nog niet bestaat
            // en voeg alle ingredienten toe. Zijn al gesorteerd vanuit de database.
            $ingredienten[$rij['product_name']][] = $rij['ingredient_name'];
        }

        return $ingredienten;
    }

?>