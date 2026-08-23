<?php

// Alle waarden komen uit 'variables.env' (zie env_file in docker-compose.yml).
$db_host     = getenv('DB_HOST');
$db_name     = getenv('DB_NAME');
$db_user     = getenv('APP_DB_USER');
$db_password = getenv('APP_DB_PASSWORD');

// Liever hier stoppen dan verderop een onduidelijke verbindingsfout krijgen.
if ($db_host === false || $db_name === false || $db_user === false || $db_password === false) {
  throw new RuntimeException('Databaseconfiguratie ontbreekt in de omgevingsvariabelen.');
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