<?php

    require_once __DIR__ . '/db_connectie.php';

    // personnel_username is niet nullable, dus we pakken de eerste medewerker op alfabetische volgorde
    function haalStandaardPersoneelslid($verbinding)
    {
        $sql = "
            SELECT TOP 1 username
            FROM [User]
            WHERE role = 'Personnel'
            ORDER BY username
        ";

        $query = $verbinding->prepare($sql);
        $query->execute();

        // fetchColumn geeft false als er niks gevonden is
        return $query->fetchColumn();
    }

    // Zet de bestelling in de database en geef het nieuwe order_id terug
    function maakBestellingAan(
        $verbinding, 
        $klantGebruikersnaam, 
        $klantNaam, 
        $personeelGebruikersnaam, 
        $status,
        $adres
        ): int
    {
        $sql = '
            INSERT INTO Pizza_Order
                (client_username, client_name, personnel_username, datetime, status, address)
            VALUES (:klant, :klantnaam, :personeelslid, GETDATE(), :status, :adres)
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([
            ':klant' => $klantGebruikersnaam,
            ':klantnaam' => $klantNaam,
            ':personeelslid' => $personeelGebruikersnaam,
            ':status' => $status,
            ':adres' => $adres,
        ]);

        // order_id wordt door de database zelf aangemaakt, dus we halen hem op met lastInsertId
        return (int) $verbinding->lastInsertId();
    }

    // Maak een regel aan voor een product in Pizza_Order_Product
    function maakBestelregelAan($verbinding, $bestellingId, $productNaam, $aantal)
    {
        $sql = '
            INSERT INTO Pizza_Order_Product (order_id, product_name, quantity)
            VALUES (:bestelling, :product, :aantal)
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([
            ':bestelling' => $bestellingId,
            ':product'    => $productNaam,
            ':aantal'     => $aantal,
        ]);
    }

    // Haal alle bestellingen van één klant op, inclusief de producten.
    function haalBestellingenVanKlant($verbinding, $klantGebruikersnaam): array
    {
        $sql = '
            SELECT  po.order_id,
                    po.datetime,
                    po.status,
                    po.address,
                    po.client_name,
                    pop.product_name,
                    pop.quantity,
                    pop.quantity * p.price AS regel_totaal
            FROM Pizza_Order po
            JOIN Pizza_Order_Product pop ON pop.order_id = po.order_id
            JOIN Product p ON p.name = pop.product_name
            WHERE po.client_username = :gebruikersnaam_klant
            ORDER BY po.order_id DESC, pop.product_name
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':gebruikersnaam_klant' => $klantGebruikersnaam]);

        return _groepeerOpBestelling($query->fetchAll());
    }


    // Haal de bestelling op via het bestelnummer, nodig voor gasten zonder account.
    // TODO: zou deze en die hierboven kunnen generaliseren, want bijna dezelfde functie.
    // Wel een ander doel, dus misschien ook goed om los te houden.
    function haalBestellingMetBestelnummer($verbinding, $bestelNummer): array
    {
        $sql = '
            SELECT  po.order_id,
                    po.datetime,
                    po.status,
                    po.address,
                    po.client_name,
                    pop.product_name,
                    pop.quantity,
                    pop.quantity * p.price AS regel_totaal
            FROM Pizza_Order po
            JOIN Pizza_Order_Product pop ON pop.order_id = po.order_id
            JOIN Product p ON p.name = pop.product_name
            WHERE po.order_id = :bestelNummer
            ORDER BY po.order_id DESC, pop.product_name
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':bestelNummer' => $bestelNummer]);

        return _groepeerOpBestelling($query->fetchAll());
    }


    // Zet de sql output om naar één key per bestelling ipv losse regels per product
    // Dezelfde structuur als _groepeerOpProduct
    function _groepeerOpBestelling(array $rijen): array 
    {
        $bestellingen = [];

        foreach ($rijen as $rij) {
            $bestelnummer = $rij['order_id'];

            if (!isset($bestellingen[$bestelnummer])) {
                $bestellingen[$bestelnummer] = [
                    'bestel_nummer' => $bestelnummer,
                    'datum' => $rij['datetime'],
                    'status' => $rij['status'],
                    'adres' => $rij['address'],
                    'klant_naam' => $rij['client_name'],
                    'regels' => [],
                ];
            }

            $bestellingen[$bestelnummer]['regels'][] =[
                'product_naam' => $rij['product_name'],
                'aantal' => $rij['quantity'],
                'regelTotaal' => (float) $rij['regel_totaal']
            ];
        }

        return array_values($bestellingen);
    }

    // Haal alle bestellingen op met 
    // De keuken vraagt 1 t/m 3 op, de bezorger 4 t/m 5. 
    // Afgeleverd en geannuleerd worden niet meegenomen, zodat alleen de to do bestellingen er zijn.
    function haalBestellingenMetStatus($verbinding, $vanStatus, $totStatus): array
    {
        $sql = '
            SELECT  po.order_id,
                    po.datetime,
                    po.status,
                    po.address,
                    po.client_name,
                    pop.product_name,
                    pop.quantity,
                    pop.quantity * p.price AS regel_totaal
            FROM Pizza_Order po
            JOIN Pizza_Order_Product pop ON pop.order_id = po.order_id
            JOIN Product p ON p.name = pop.product_name
            WHERE po.status BETWEEN :van AND :tot
            ORDER BY po.datetime, po.order_id, pop.product_name
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':van' => $vanStatus, ':tot' => $totStatus]);

        return _groepeerOpBestelling($query->fetchAll());
    }


    // Update de status van een bestelling. 
    function werkBestellingStatusBij($verbinding, $bestelNummer, $status): void
    {
        $sql = '
            UPDATE Pizza_Order
            SET status = :status
            WHERE order_id = :bestelling
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([
            ':status'     => $status,
            ':bestelling' => $bestelNummer,
        ]);
    }