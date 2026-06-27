<?php

require_once './db_connectie.php';

// Haal producten op van een bepaalde categorie (pizza, drank etc.)
function haalProducten($verbinding, $type_id = null) {
    $sql = '
        SELECT p.id, p.name, p.price, p.type_id, i.name AS ingredient_name
        FROM Product p
        LEFT JOIN Product_Ingredient pi ON pi.product_id = p.id
        LEFT JOIN Ingredient i ON i.id = pi.ingredient_id
        WHERE p.type_id = :type_id
        ORDER BY p.name DESC
    ';

    $query = $verbinding->prepare($sql);
    $query->execute([':type_id' => $type_id]);
    $data = $query->fetchAll();
    return $data;
}



?>