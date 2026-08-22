<?php
    // Omdat adres in de database één regel is en het in de tool 4 moet zijn
    // zijn er knip- en plakfuncties nodig en aangezien die door meerdere
    // scripts worden gebruikt, krijgen ze hun eigen module.

    // Maak één regel van de losse velden
    function maakAdresRegel($straat, $huisnummer, $postcode, $stad): string
    {
        return $straat . ' ' . $huisnummer . ', ' . $postcode . ', ' . $stad;
    }

    // Knip de adresregel in stukjes
    // Het format van het adres is dus belangrijk.
    function splitsAdresRegel($adres): array
    {
        $leeg = [
            'straat' => '',
            'huisnummer' => '',
            'postcode' => '',
            'stad' => ''
        ];

        if (!$adres) {
            return $leeg;
        }

        // splits het adres op in stukjes met de komma als separator
        $delen = explode(', ', $adres);

        // Moet straat en huisnummer, postcode, stad zijn. 
        // Als dit niet zo is, is er iets fout gegaan, dus geef dan de lege array terug
        if (count($delen) !== 3) {
            return $leeg;
        }

        // Het eerste deel is de straatnaam en huisnummer. 
        // Het laatste woord is het huisnummer, alles daarvoor is de straatnaam (kan meerdere woorden zijn).
        $woorden = explode(' ', $delen[0]);

        if (count($woorden) < 2) {
            return $leeg;
        }

        // Pop laatste veld uit de array (huisnummer)
        $huisnummer = array_pop($woorden);
        // Plak de straatdelen weer aan elkaar
        $straat = implode(' ', $woorden);

        return [
            'straat'     => $straat,
            'huisnummer' => $huisnummer,
            'postcode'   => $delen[1],
            'stad'       => $delen[2],
        ];
    }