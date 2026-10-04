<?php

require_once __DIR__ . '/db_connectie.php';

// Bovengrens op de categorietabs. Pagineren heeft geen zin voor navigatie, dus een TOP i.p.v. OFFSET.
const MAX_CATEGORIEEN = 25;

// Haal de legitieme categorieën op (begrensd op MAX_CATEGORIEEN)
function haalProductTypes($verbinding): array {
    $sql = '
        SELECT TOP (:maximum) name
        FROM ProductType
        ORDER BY name
    ';

    $query = $verbinding->prepare($sql);

    // TOP eist net als FETCH NEXT een integer
    $query->bindValue(':maximum', MAX_CATEGORIEEN, PDO::PARAM_INT);
    $query->execute();

    $data = $query->fetchAll(PDO::FETCH_COLUMN); // Geef alleen de kolomnamen terug
    return $data;
}


?>