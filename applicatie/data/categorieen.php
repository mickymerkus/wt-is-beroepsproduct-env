<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


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