<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


// Alle verbindingsgegevens komen uit 'variables.env'. Dat bestand wordt door
// docker compose aan beide containers doorgegeven, dus getenv() werkt hier.
// Zo staat het wachtwoord niet in de broncode van de applicatie zelf.
$db_host = getenv('DB_HOST') ?: 'database_server';
$db_name = 'pizzeria';

// Deze login heeft alleen de rechten die de applicatie gebruikt: lezen op de
// zeven tabellen, schrijven op de drie waarin de applicatie invoegt, en UPDATE
// op Pizza_Order. Geen DELETE en geen beheerrechten. Aangemaakt onderaan
// webserver-setup/pizzeria.sql. 'sa' wordt door de applicatie niet meer gebruikt.
$db_user     = getenv('DB_APP_USER');
$db_password = getenv('DB_APP_PASSWORD');

// Zonder deze gegevens kan de applicatie niet werken. Direct en duidelijk
// afbreken is beter dan verderop een onbegrijpelijke fout krijgen.
// Het wachtwoord zelf staat bewust niet in de melding.
if ($db_user === false || $db_user === '' || $db_password === false || $db_password === '') {
    throw new RuntimeException(
        'DB_APP_USER of DB_APP_PASSWORD ontbreekt. Staan ze in variables.env?'
    );
}

// Het 'ssl certificate' wordt altijd geaccepteerd (niet overnemen op productie, verder dan altijd "TrustServerCertificate=1"!!!)
$verbinding = new PDO('sqlsrv:Server=' . $db_host . ';Database=' . $db_name . ';ConnectionPooling=0;TrustServerCertificate=1', $db_user, $db_password);

// Bewaar het wachtwoord niet langer onnodig in het geheugen van PHP.
unset($db_password);

// Zorg ervoor dat eventuele fouttoestanden ook echt als fouten (exceptions) gesignaleerd worden door PHP.
$verbinding->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Vastgezet zodat we niet elke keer beide soorten arrays terugkrijgen
$verbinding->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Functie om in andere files toegang te krijgen tot de verbinding.
function maakVerbinding(): PDO 
{
  global $verbinding;
  return $verbinding;
}

?>