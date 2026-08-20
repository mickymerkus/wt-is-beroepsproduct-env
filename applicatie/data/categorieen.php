<?php

require_once __DIR__ . '/db_connectie.php';

// Haal alle legitieme categorieën op
function haalProductTypes($verbinding): array {
    $sql = '
        SELECT name
        FROM ProductType
        ORDER BY name
    ';

    $query = $verbinding->prepare($sql);
    $query->execute();
    $data = $query->fetchAll(PDO::FETCH_COLUMN); // Geef alleen de kolomnamen terug
    return $data; 
} 


?>