<?php

    require_once __DIR__ . '/../data/bestellingen.php';
    require_once __DIR__ . '/winkelmandje.php';
    require_once __DIR__ . '/authenticatie.php';
    require_once __DIR__ . '/adres.php';

    // In de database staan alleen maar de getallen, dus de mapping naar de betekenis ervan
    // is hier vastgezet.
    // TODO: bedenken of ik dit toch in de database wil zetten, maar dan moet ik ook iets van 
    // een migratiescriptje maken en volgens de reader mag dit niet.
    const STATUS_IN_WACHTRIJ = 1;
    const STATUS_WORDT_GEMAAKT = 2;
    const STATUS_KLAAR_VOOR_BEZORGING = 3;

    // Plaats de bestelling die op dat moment in het winkelmandje zit.
    // Geeft een lijst met foutmeldingen terug, of een lege lijst als het gelukt is.
    function plaatsBestelling($verbinding, $invoer): array
    {
        $fouten = [];

        // Producten, aantallen en prijzen komen uit de sessie en de database
        $regels = haalWinkelmandjeRegels($verbinding);

        if (!$regels) {
            return ['Je winkelmandje is leeg.'];
        }

        // Mag leeg zijn, want je hoeft niet per se een account te hebben om te mogen bestellen
        $gebruiker = huidigeGebruiker();

        if ($gebruiker) {
            $klantGebruikersnaam = $gebruiker['username'];
            $klantNaam = $gebruiker['voornaam'] . ' ' . $gebruiker['achternaam'];
        } else {
            // client_username mag NULL zijn, client_name niet, dus deze moeten we nog opvragen
            $klantGebruikersnaam = null;
            $klantNaam = trim($invoer['naam'] ?? '');

            if ($klantNaam === '') {
                $fouten[] = 'Vul je naam in.';
            }
        }

        // Haal mogelijk bestaande adresgegevens uit de invoer
        $straat     = trim($invoer['straat'] ?? '');
        $huisnummer = trim($invoer['huisnummer'] ?? '');
        $postcode   = trim($invoer['postcode'] ?? '');
        $stad       = trim($invoer['stad'] ?? '');

        if ($straat === '' || $huisnummer === '' || $postcode === '' || $stad === '') {
            $fouten[] = 'Vul een volledig adres in.';
        }

        // Zelfde opbouw als bij registratie
        $adres = maakAdresRegel($straat, $huisnummer, $postcode, $stad);

        if (($invoer['betaalwijze'] ?? '') === '') {
            $fouten[] = 'Kies een betaalwijze.';
        }

        $personeelslid = haalStandaardPersoneelslid($verbinding);

        if (!$personeelslid) {
            $fouten[] = 'Er is geen medewerker beschikbaar. Probeer het later opnieuw.';
        }

        if ($fouten) {
            return $fouten;
        }

        // Eerst de bestelling zelf, want de regels hebben het order_id nodig
        $bestellingId = maakBestellingAan(
            $verbinding,
            $klantGebruikersnaam,
            $klantNaam,
            $personeelslid,
            STATUS_IN_WACHTRIJ,
            $adres
        );

        // Aangezien een gast geen account heeft, slaan we het bestelnummer ook op in de sessie
        $_SESSION['laatsteBestelling'] = $bestellingId;

        // Voeg alle producten toe aan de bestelling in de database
        foreach ($regels as $regel) {
            maakBestelregelAan($verbinding, $bestellingId, $regel['naam'], $regel['aantal']);
        }

        // Leeg het mandje nu de bestelling is verwerkt
        bewaarWinkelmandje([]);

        return [];
    }


    // Mapping voor de database status (int) naar wat getoond kan worden aan de gebruiker
    function statusOmschrijving(string $status): string
    {
        // Cast status naar een int om bugs te voorkomen. Wordt als string uit de database gehaald.
        switch ((int) $status) {
            case STATUS_IN_WACHTRIJ:
                return 'In de wachtrij';

            case STATUS_WORDT_GEMAAKT:
                return 'Wordt gemaakt';

            case STATUS_KLAAR_VOOR_BEZORGING:
                return 'Klaar voor bezorging';

            // status is nullable in de database en ik wil meer statussen toevoegen, dus tot dan moeten deze opgevangen worden
            default:
                return 'Onbekend';
        }
    }

    // Verander de kleur afhankelijk van de status. Geeft de naam van de CSS klasse terug
    function statusCssKlasse($status): string
    {
        switch ((int) $status) {
            case STATUS_IN_WACHTRIJ:
                return 'in-wachtrij';

            case STATUS_WORDT_GEMAAKT:
                return 'wordt-gemaakt';

            case STATUS_KLAAR_VOOR_BEZORGING:
                return 'klaar-voor-bezorging';

            default:
                return 'onbekend';
        }
    }
