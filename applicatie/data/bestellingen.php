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

    // Tel hoeveel bestellingen deze klant in totaal heeft.
    // Nodig om te weten hoeveel pagina's het overzicht krijgt.
    function telBestellingenVanKlant($verbinding, $klantGebruikersnaam): int
    {
        $sql = '
            SELECT COUNT(*)
            FROM Pizza_Order
            WHERE client_username = :gebruikersnaam_klant
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':gebruikersnaam_klant' => $klantGebruikersnaam]);

        return (int) $query->fetchColumn();
    }

    // Haal één pagina bestellingen van één klant op, inclusief de producten.
    //
    // Let op de opbouw: de paginering zit in de CTE 'pagina' en werkt dus op
    // BESTELLINGEN, niet op de rijen van de join. Dat is bewust. Zet je
    // OFFSET/FETCH op het resultaat van de join, dan knipt de database midden in
    // een bestelling: de eerste producten komen op pagina 1 en de rest op pagina 2.
    // "10 per pagina" zou dan ook 10 productregels betekenen in plaats van
    // 10 bestellingen.
    //
    // Eerst dus de order_id's van deze pagina bepalen, en pas daarna de regels
    // erbij joinen. Daardoor is het aantal opgehaalde rijen altijd begrensd.
    function haalBestellingenVanKlant($verbinding, $klantGebruikersnaam, int $perPagina, int $offset): array
    {
        $sql = '
            WITH pagina AS (
                SELECT po.order_id
                FROM Pizza_Order po
                WHERE po.client_username = :gebruikersnaam_klant
                ORDER BY po.order_id DESC
                OFFSET :offset ROWS
                FETCH NEXT :per_pagina ROWS ONLY
            )
            SELECT  po.order_id,
                    po.datetime,
                    po.status,
                    po.address,
                    po.client_name,
                    pop.product_name,
                    pop.quantity,
                    pop.quantity * p.price AS regel_totaal
            FROM pagina
            JOIN Pizza_Order po ON po.order_id = pagina.order_id
            JOIN Pizza_Order_Product pop ON pop.order_id = po.order_id
            JOIN Product p ON p.name = pop.product_name
            ORDER BY po.order_id DESC, pop.product_name
        ';

        $query = $verbinding->prepare($sql);

        // OFFSET en FETCH NEXT moeten als getal gebonden worden. Met de gewone
        // execute([...]) gaat alles als string naar de database en weigert SQL Server
        // de query: "The number of rows provided for a TOP or FETCH clauses row count
        // parameter must be an integer." Daarom hier bindValue met PDO::PARAM_INT.
        $query->bindValue(':gebruikersnaam_klant', $klantGebruikersnaam);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->bindValue(':per_pagina', $perPagina, PDO::PARAM_INT);
        $query->execute();

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

    // Tel hoeveel bestellingen er binnen dit statusbereik vallen.
    // Nodig om te weten hoeveel pagina's het overzicht krijgt.
    function telBestellingenMetStatus($verbinding, $vanStatus, $totStatus): int
    {
        $sql = '
            SELECT COUNT(*)
            FROM Pizza_Order
            WHERE status BETWEEN :van AND :tot
        ';

        $query = $verbinding->prepare($sql);
        $query->execute([':van' => $vanStatus, ':tot' => $totStatus]);

        return (int) $query->fetchColumn();
    }

    // Haal één pagina bestellingen op binnen een statusbereik.
    // De keuken vraagt 1 t/m 4 op, de bezorger 5 t/m 6.
    // Afgeleverd en geannuleerd worden niet meegenomen, zodat alleen de to do bestellingen er zijn.
    //
    // Zelfde opbouw als haalBestellingenVanKlant: de CTE pagineert de bestellingen,
    // daarna komen de regels erbij. De sortering in de CTE is gelijk aan die in de
    // buitenste query, anders kan dezelfde bestelling op twee pagina's opduiken.
    function haalBestellingenMetStatus($verbinding, $vanStatus, $totStatus, int $perPagina, int $offset): array
    {
        $sql = '
            WITH pagina AS (
                SELECT po.order_id
                FROM Pizza_Order po
                WHERE po.status BETWEEN :van AND :tot
                ORDER BY po.datetime, po.order_id
                OFFSET :offset ROWS
                FETCH NEXT :per_pagina ROWS ONLY
            )
            SELECT  po.order_id,
                    po.datetime,
                    po.status,
                    po.address,
                    po.client_name,
                    pop.product_name,
                    pop.quantity,
                    pop.quantity * p.price AS regel_totaal
            FROM pagina
            JOIN Pizza_Order po ON po.order_id = pagina.order_id
            JOIN Pizza_Order_Product pop ON pop.order_id = po.order_id
            JOIN Product p ON p.name = pop.product_name
            ORDER BY po.datetime, po.order_id, pop.product_name
        ';

        $query = $verbinding->prepare($sql);

        // Zie de uitleg bij haalBestellingenVanKlant: OFFSET en FETCH NEXT
        // moeten expliciet als integer gebonden worden.
        $query->bindValue(':van', $vanStatus, PDO::PARAM_INT);
        $query->bindValue(':tot', $totStatus, PDO::PARAM_INT);
        $query->bindValue(':offset', $offset, PDO::PARAM_INT);
        $query->bindValue(':per_pagina', $perPagina, PDO::PARAM_INT);
        $query->execute();

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