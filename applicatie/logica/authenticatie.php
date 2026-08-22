<?php
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
        unset($_SESSION['gebruiker']);
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

        // Meteen inloggen na het aanmaken van het account
        $_SESSION['gebruiker'] = [
            'username' => $username,
            'voornaam' => $voornaam,
            'achternaam' => $achternaam,
            'rol' => 'Client',
        ];

        return [];
    }