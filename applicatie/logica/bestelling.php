<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


    require_once __DIR__ . '/../data/bestellingen.php';
    require_once __DIR__ . '/winkelmandje.php';
    require_once __DIR__ . '/authenticatie.php';
    require_once __DIR__ . '/adres.php';

    // In de database staan alleen maar de getallen, dus de mapping naar de betekenis ervan
    // is hier vastgezet.
    // De volgorde is belangrijk. 
    const STATUS_IN_WACHTRIJ          = 1;
    const STATUS_WORDT_GEMAAKT        = 2;
    const STATUS_IN_DE_OVEN           = 3;
    const STATUS_ON_HOLD              = 4;
    const STATUS_KLAAR_VOOR_BEZORGING = 5;
    const STATUS_WORDT_BEZORGD        = 6;
    const STATUS_AFGELEVERD           = 7;
    const STATUS_GEANNULEERD          = 8;

    // De keuken werkt aan 1 t/m 4, de bezorger aan 5 en 6. 
    // Deze constantes worden gebruikt voor de bestellingoverzichten.
    const KEUKEN_VAN   = STATUS_IN_WACHTRIJ;
    const KEUKEN_TOT   = STATUS_ON_HOLD;
    const BEZORGER_VAN = STATUS_KLAAR_VOOR_BEZORGING;
    const BEZORGER_TOT = STATUS_WORDT_BEZORGD;


    // Bouw de opties voor het statusmenu: statuscode => tekst.
    function alleStatussen(): array
    {
        $statussen = [];

        for ($code = STATUS_IN_WACHTRIJ; $code <= STATUS_GEANNULEERD; $code++) {
            $statussen[$code] = statusOmschrijving($code);
        }
        return $statussen;
    }
    

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
    function statusOmschrijving($status): string
    {
        // Cast status naar een int om bugs te voorkomen. Wordt als string uit de database gehaald.
        switch ((int) $status) {
            case STATUS_IN_WACHTRIJ:
                return 'In de wachtrij';
            case STATUS_WORDT_GEMAAKT:
                return 'Wordt gemaakt';
            case STATUS_IN_DE_OVEN:
                return 'In de oven';
            case STATUS_ON_HOLD:
                return 'On hold';
            case STATUS_KLAAR_VOOR_BEZORGING:
                return 'Klaar voor bezorging';
            case STATUS_WORDT_BEZORGD:
                return 'Wordt bezorgd';
            case STATUS_AFGELEVERD:
                return 'Afgeleverd';
            case STATUS_GEANNULEERD:
                return 'Geannuleerd';

            // status is nullable in de database, dus een onbekende waarde vangen we hier op
            default:
                return 'Onbekend';
        }
    }

    // Verander de kleur afhankelijk van de status. Geef de naam van de CSS klasse terug
    function statusCssKlasse($status): string
    {
        switch ((int) $status) {
            case STATUS_IN_WACHTRIJ:
                return 'in-wachtrij';
            case STATUS_WORDT_GEMAAKT:
                return 'wordt-gemaakt';
            case STATUS_IN_DE_OVEN:
                return 'in-de-oven';
            case STATUS_ON_HOLD:
                return 'on-hold';
            case STATUS_KLAAR_VOOR_BEZORGING:
                return 'klaar-voor-bezorging';
            case STATUS_WORDT_BEZORGD:
                return 'wordt-bezorgd';
            case STATUS_AFGELEVERD:
                return 'afgeleverd';
            case STATUS_GEANNULEERD:
                return 'geannuleerd';

            default:
                return 'onbekend';
        }
    }


    // Validatiefunctie, alleen een status die wij zelf gedefinieerd hebben mag de database in.
    function isGeldigeStatus($status): bool
    {
        switch ((int) $status) {
            case STATUS_IN_WACHTRIJ:
            case STATUS_WORDT_GEMAAKT:
            case STATUS_IN_DE_OVEN:
            case STATUS_KLAAR_VOOR_BEZORGING:
            case STATUS_WORDT_BEZORGD:
            case STATUS_ON_HOLD:
            case STATUS_AFGELEVERD:
            case STATUS_GEANNULEERD:
                return true;

            default:
                return false;
        }
    }

    // Wijzig de status van een bestelling vanuit het personeelsoverzicht.
    function wijzigBestellingStatus($verbinding, $invoer): array
    {
        $bestelNummer = (int) ($invoer['bestelling'] ?? 0);
        $nieuweStatus = (int) ($invoer['status'] ?? 0);

        if ($bestelNummer <= 0) {
            return ['Onbekende bestelling.'];
        }

        // Validatie
        if (!isGeldigeStatus($nieuweStatus)) {
            return ['Onbekende status.'];
        }

        werkBestellingStatusBij($verbinding, $bestelNummer, $nieuweStatus);
        return [];
    }
