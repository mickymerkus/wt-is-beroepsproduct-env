<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }

    require_once __DIR__ . '/../data/gebruikers.php';
    require_once __DIR__ . '/adres.php';

    // Probeert in te loggen, als het lukt komt de gebruiker in de sessie te staan, zoniet dan returnt de functie false
    function logInGebruiker($verbinding, $username, $wachtwoord): bool
    {
        $gebruiker = haalGebruikerOpMetGebruikersnaam($verbinding, $username);

        if (!$gebruiker) {
            return false;
        }

        if (!password_verify($wachtwoord, $gebruiker['password'])) {
            return false;
        }

        // Het rechtenniveau van deze sessie verandert nu van 'bezoeker' naar
        // 'ingelogde gebruiker', dus krijgt de sessie een nieuw ID. Een ID dat
        // een aanvaller vóór het inloggen in de browser had geplant (session
        // fixation) is daarna waardeloos: het hoort niet meer bij deze sessie.
        // true betekent dat de oude sessie meteen wordt verwijderd, zodat hij
        // niet naast de nieuwe blijft bestaan.
        session_regenerate_id(true);

        $_SESSION['gebruiker'] = [
            'username' => $gebruiker['username'],
            'voornaam' => $gebruiker['first_name'],
            'achternaam' => $gebruiker['last_name'],
            'rol' => $gebruiker['role'],
        ];

        return true;
    }

    // Log de gebruiker uit
    function logUitGebruiker(): void
    {
        // Alle sessiegegevens weggooien, niet alleen de sleutel 'gebruiker'.
        // Anders blijft bijvoorbeeld het laatste bestelnummer achter en kan de
        // volgende bezoeker op deze computer dat nog opvragen.
        $_SESSION = [];

        // Ook na het uitloggen een nieuw sessie-ID, zodat een ID dat iemand
        // onderweg heeft opgevangen of geplant na de logout niets meer waard is.
        session_regenerate_id(true);
    }

    // Check of de gebruiker is ingelogd
    function isIngelogd(): bool
    {
        return isset($_SESSION['gebruiker']);
    }

    // Geef de info terug van de huidig ingelogde gebruiker (als die er is)
    function huidigeGebruiker(): ?array
    {
        return $_SESSION['gebruiker'] ?? null;
    }

    // Check of de ingelogde gebruiker een personeelslid is.
    function isPersoneel(): bool
    {
        $gebruiker = huidigeGebruiker();

        return $gebruiker !== null && $gebruiker['rol'] === 'Personnel';
    }

    // Registreer een nieuwe klant en doe validatie op de mogelijke gebruikersfouten.
    function registreerGebruiker(
        $verbinding, 
        $username, 
        $wachtwoord, 
        $wachtwoordBevestiging, 
        $voornaam, 
        $achternaam, 
        $straat, 
        $huisnummer, 
        $postcode, 
        $stad
        ): array
    {
        $fouten = [];

        if (trim($username) === '') {
            $fouten[] = 'Gebruikersnaam is verplicht.';
        } elseif (gebruikersnaamBestaatAl($verbinding, $username)) {
            $fouten[] = 'Deze gebruikersnaam is al in gebruik.';
        }

        if (trim($voornaam) === '' || trim($achternaam) === '') {
            $fouten[] = 'Voor- en achternaam zijn verplicht.';
        }

        if (trim($straat) === '' || trim($huisnummer) === '' || trim($postcode) === '' || trim($stad) === '') {
            $fouten[] = 'Vul een volledig adres in.';
        }

        if ($wachtwoord === '') {
            $fouten[] = 'Wachtwoord is verplicht.';
        } elseif ($wachtwoord !== $wachtwoordBevestiging) {
            $fouten[] = 'De wachtwoorden komen niet overeen.';
        }

        if ($fouten) {
            return $fouten;
        }

        // Adres is één veld in de database, dus we concateneren alle info
        $adres = maakAdresRegel($straat, $huisnummer, $postcode, $stad);
        $wachtwoordHash = password_hash($wachtwoord, PASSWORD_DEFAULT);

        maakGebruikerAan($verbinding, $username, $wachtwoordHash, $voornaam, $achternaam, $adres);

        // Registreren logt meteen in, dus geldt hier hetzelfde als bij
        // logInGebruiker(): het rechtenniveau van de sessie verandert, dus
        // krijgt de sessie een nieuw ID.
        session_regenerate_id(true);

        // Meteen inloggen na het aanmaken van het account
        $_SESSION['gebruiker'] = [
            'username' => $username,
            'voornaam' => $voornaam,
            'achternaam' => $achternaam,
            'rol' => 'Client',
        ];

        return [];
    }