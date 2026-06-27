<!-- <?php
$consumpties = [
    'Bier' => 3.50,
    'Wijn' => 4.00,
    'Borrelnoten' => 2.20
];

$totaal = 0;
$bon = '';

foreach ($consumpties as $consumptie => $prijs) {
    $bon .= "$consumptie: $prijs<br>";
    $totaal += $prijs;
}

echo $bon . "--------------------<br>Totaal: " . $totaal




// We plakken nu het resultaat van de functie titel aan de variabele $html
// en slaan dit op in de variabele $html

?> -->
<!DOCTYPE html>
<html>
<body>
    <?php
    $consumpties = [
        'Bier' => 3.50,
        'Wijn' => 4.00,
        'Borrelnoten' => 2.20
    ];

    $totaal = 0;
    $bon = '';

    foreach ($consumpties as $consumptie => $prijs) {
        $bon .= "$consumptie: $prijs<br>";
        $totaal += $prijs;
    }

    echo $bon . "--------------------<br>Totaal: " . $totaal

    ?>
</body>
</html>
