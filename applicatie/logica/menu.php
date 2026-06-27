<?php

require_once './db_connectie.php';


$db = maakVerbinding();

$query = '
    SELECT name, price, type_id
    FROM Product
    ORDER BY name DESC
';

$data = $db->query($query);

$table = '<table>';

$table = $table . '<tr><th>Naam</th><th>Prijs</th><th>type</th></tr>';

foreach($data as $rij) {
    $naam = $rij['name'];
    $prijs = $rij['price'];
    $type = $rij['type_id'];
    $table = $table . "<tr><td>$naam</td><td>$prijs</td><td>$type</td></tr>";
}

$table = $table . '<table>';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Producten</title>
</head>
<body>
  <h1>Alle producten op een rijtje</h1>
  <!-- 3. Weergeven van de data -->
  <?= $table ?>
</body>
</html>