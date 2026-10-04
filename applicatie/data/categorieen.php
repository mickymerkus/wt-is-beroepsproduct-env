<?php

require_once __DIR__ . '/db_connectie.php';

// Bovengrens op het aantal categorietabs. De categorieën vormen de navigatie
// bovenaan de pagina, dus ze moeten alle tegelijk in beeld passen; pagineren
// heeft hier geen zin. Een TOP houdt de query toch begrensd, zodat een
// uitgedijde ProductType-tabel de navigatie niet onbeperkt laat groeien.
const MAX_CATEGORIEEN = 25;

// Haal de legitieme categorieën op (begrensd op MAX_CATEGORIEEN)
function haalProductTypes($verbinding): array {
    $sql = '
        SELECT TOP (:maximum) name
        FROM ProductType
        ORDER BY name
    ';

    $query = $verbinding->prepare($sql);

    // TOP eist net als FETCH NEXT een echt getal, geen string.
    $query->bindValue(':maximum', MAX_CATEGORIEEN, PDO::PARAM_INT);
    $query->execute();

    $data = $query->fetchAll(PDO::FETCH_COLUMN); // Geef alleen de kolomnamen terug
    return $data;
}


?>