<?php

require_once __DIR__ . '/db_connectie.php';

// Haal producten op van een bepaalde categorie (pizza, drank etc.)
function haalProductenMetIngredienten($verbinding, $categorie): array 
{
    $sql = '
        SELECT p.name, p.price, pi.ingredient_name
        FROM Product p
        LEFT JOIN Product_Ingredient pi ON pi.product_name = p.name
        WHERE p.type_id = :categorie
        ORDER BY p.name, pi.ingredient_name
    ';

    $query = $verbinding->prepare($sql);
    $query->execute([':categorie' => $categorie]);
    $data = $query->fetchAll();
    return _groepeerOpProduct($data);
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

// Haal de prijzen van een lijst producten op
function haalPrijzenVanProducten($verbinding): array 
{
    $sql = '
        SELECT name, price
        FROM Product
    ';

    $query = $verbinding->prepare($sql);
    $query->execute();

    $prijzen = [];

    foreach ($query->fetchAll() as $rij) {
        $prijzen[$rij['name']] = $rij['price'];
    }

    //var_dump($prijzen); die();
    return $prijzen;

}

// Valideer of een bepaald product ook echt in de database te vinden is.
function bestaatProduct($verbinding, $naam): bool
{
    $sql = '
        SELECT 1
        FROM Product
        WHERE name := $name
    ';

    $query = $verbinding->prepare($sql);
    $query->execute([':naam' => $naam]);

    //Check of er een rij is mbv fetchColumn()
    return $query->fetchColumn() !== false;
}

?>