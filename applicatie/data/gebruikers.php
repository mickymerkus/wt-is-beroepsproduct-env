<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


    require_once __DIR__ . '/db_connectie.php';

    // Haal een gebruiker op via de gebruikersnaam.
    function haalGebruikerOpMetGebruikersnaam($verbinding, $username)
    {
        $sql = '
            SELECT username, password, first_name, last_name, role
            FROM [User]
            WHERE username = :username
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':username' => $username]);

        return $query->fetch();
    }

    // Check of een gebruikersnaam al in gebruik is
    function gebruikersnaamBestaatAl($verbinding, $username): bool
    {
        $sql = '
            SELECT 1
            FROM [User]
            WHERE username = :username
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':username' => $username]);

        return $query->fetchColumn() !== false;
    }

    // Maak een nieuw account aan. Wachtwoord moet hiervoor al gehashed zijn.
    // Ivm security hardcoden we de rol. Personeelsaccount doen we apart zodat dat nooit gehacked kan worden.
    function maakGebruikerAan(
        $verbinding, 
        $username, 
        $wachtwoordHash, 
        $voornaam, 
        $achternaam, 
        $adres
        ): void
    {
        $sql = '
            INSERT INTO [User] (username, password, first_name, last_name, address, role)
            VALUES (:username, :wachtwoord, :voornaam, :achternaam, :adres, :rol)
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([
            ':username' => $username,
            ':wachtwoord' => $wachtwoordHash,
            ':voornaam' => $voornaam,
            ':achternaam' => $achternaam,
            ':adres' => $adres,
            ':rol' => 'Client',
        ]);
    }


    // Adres van gebruiker ophalen zodat ze het niet opnieuw hoeven in te vullen in het bestelformulier
    function haalAdresVanGebruiker($verbinding, $gebruikersnaam)
    {
        $sql = '
            SELECT [address]
            FROM [User]
            WHERE username = :gebruikersnaam
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':gebruikersnaam' => $gebruikersnaam]);

        return $query->fetchColumn();
    }

?>