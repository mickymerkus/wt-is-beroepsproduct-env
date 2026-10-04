<?php

    require_once __DIR__ . '/db_connectie.php';

    // Totaal aantal producten in de categorie, voor het aantal pagina's
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
    // De CTE pagineert op producten, anders valt een pizza met veel ingrediënten over twee pagina's.
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

        // Als integer binden, zie bestellingen.php
        $query->bindValue(':categorie', $categorie);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->bindValue(':per_pagina', $perPagina, PDO::PARAM_INT);
        $query->execute();

        $data = $query->fetchAll();
        return _groepeerOpProduct($data);
    }

    // Placeholders (:naam0, :naam1, ...) voor een IN-clausule. Alleen het aantal is dynamisch,
    // de waarden gaan als parameter mee, dus er komt geen invoer in de SQL-string.
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

    // Haal de prijzen op van precies de gevraagde producten (naam => prijs).
    // Bewust niet gepagineerd: haalWinkelmandjeRegels gooit producten zonder prijs weg.
    function haalPrijzenVanProducten($verbinding, array $productNamen): array
    {
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

    // Haal de ingrediënten op van precies de gevraagde producten, voor het bestellingsoverzicht.
    // Net als de prijzen begrensd op naam in plaats van gepagineerd.
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