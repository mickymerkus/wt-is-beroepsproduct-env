<?php

require_once __DIR__ . '/db_connectie.php';

// Haal producten op van een bepaalde categorie (pizza, drank etc.)
function haalProductenMetIngredienten($verbinding, $type_id = null) {
    $sql = '
        SELECT p.name, p.price, p.type_id, pi.ingredient_name
        FROM Product p
        LEFT JOIN Product_Ingredient pi ON pi.product_name = p.name
        WHERE p.type_id = :type_id
        ORDER BY p.name, pi.ingredient_name
    ';

    $query = $verbinding->prepare($sql);
    $query->execute([':type_id' => $type_id]);
    $data = $query->fetchAll();
    return _groepeerOpProduct($data);
}

//Zorg dat de ingredienten bij elk product komen te staan als list in een array
function _groepeerOpProduct(array $rijen) {
    foreach ($rijen as $rij) {
        $naam = $rij[$name];


        if (!isset($gegroepeerd[$naam])) {
            $gegroepeerd[$naam] = [
                'naam' => $naam,
                'prijs' => $rij[$price],
                'ingredienten' => [],
            ];
        }

        if ($rij['ingredient_name' !== null]) {
            $gegroepeerd[$naam]['ingredienten'][] = $rij['ingredient_name']
        }


    }
}


?>