<?php

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


    // Vangt fouten op die nergens anders worden afgehandeld.
    //
    // display_errors staat uit (zie webserver-setup/php-ini/beveiliging.ini), dus
    // een onafgevangen fout levert zonder deze functie een lege witte pagina op.
    // Dat lekt niets, maar het is voor de bezoeker onbegrijpelijk. Deze functie
    // schrijft de fout naar het logboek en toont de bezoeker een nette pagina
    // zonder technische details.
    //
    // Twee lagen dus: de configuratie verbergt de fout voor de bezoeker, deze
    // functie zorgt dat de ontwikkelaar hem nog wel kan terugvinden met
    // "docker compose logs web_server".
    //
    // Wordt bovenin elke controller aangeroepen, vóór alle andere require's,
    // want data/db_connectie.php maakt de databaseverbinding al tijdens het
    // inladen en kan dus meteen een fout opleveren.
    function installeerFoutafhandeling(): void
    {
        set_exception_handler(function (Throwable $fout): void {

            // Het volledige verhaal gaat naar het logboek, niet naar de browser.
            error_log(
                'Onafgevangen fout: ' . $fout->getMessage() .
                ' in ' . $fout->getFile() . ' op regel ' . $fout->getLine()
            );

            // Alleen headers sturen als er nog niets verzonden is; anders
            // veroorzaakt dat zelf weer een waarschuwing.
            if (!headers_sent()) {
                http_response_code(500);
                header('Content-Type: text/html; charset=UTF-8');
            }

            // Bewust geen bestandsnamen, regelnummers of foutmeldingen.
            echo '<!DOCTYPE html>'
               . '<html lang="nl"><head><meta charset="UTF-8">'
               . '<title>Er ging iets mis</title></head><body>'
               . '<h1>Er ging iets mis</h1>'
               . '<p>De pagina kon niet geladen worden. Probeer het later opnieuw.</p>'
               . '<p><a href="index.php">Terug naar de menukaart</a></p>'
               . '</body></html>';
        });
    }

    // Stuurt de beveiligingsheaders die bij elke pagina horen.
    //
    // Deze functie moet worden aangeroepen VOORDAT er ook maar één teken naar de
    // browser is geschreven: zodra PHP output verstuurt, gaan de headers mee en
    // kunnen ze niet meer gewijzigd worden. Daarom staat de aanroep bovenin elke
    // controller, direct na startSessie(), en nooit in een template.
    function stuurBeveiligingsheaders(): void
    {
        // De PHP-versie stond in elke response (X-Powered-By: PHP/8.4.16).
        // Dat is gratis verkenningsinformatie voor een aanvaller.
        header_remove('X-Powered-By');

        // Content Security Policy: de browser krijgt te horen wat deze pagina
        // mag laden. Elke regel hieronder is een aparte beperking.
        $csp = implode('; ', [
            // Alles wat hieronder niet apart genoemd wordt, mag alleen van onze eigen server komen.
            "default-src 'self'",

            // Deze applicatie bevat geen JavaScript; dat is een eis van de opdracht.
            // Daardoor kan de strengst mogelijke waarde gezet worden: de browser
            // voert hier onder geen enkele omstandigheid script uit. Mocht er ooit
            // ergens een htmlspecialchars() vergeten worden, dan is de ingesloten
            // code daarmee alsnog onschadelijk.
            "script-src 'none'",

            // style.css is een eigen bestand; er worden geen externe stylesheets
            // of style=""-attributen gebruikt.
            "style-src 'self'",

            // De afbeeldingen staan in applicatie/images/.
            "img-src 'self'",

            // Een formulier op deze site kan alleen naar deze site verzenden.
            // Voorkomt dat ingesloten HTML ingevulde gegevens naar buiten stuurt.
            "form-action 'self'",

            // Deze pagina mag niet in een iframe van een andere site staan.
            // Beschermt tegen clickjacking: een onzichtbare laag over een
            // kwaadaardige pagina waarmee een personeelslid ongemerkt op
            // 'Status opslaan' klikt.
            "frame-ancestors 'none'",

            // Voorkomt dat een ingesloten <base>-tag alle relatieve links
            // van de pagina naar een andere server omleidt.
            "base-uri 'none'",
        ]);

        header('Content-Security-Policy: ' . $csp);

        // Oudere browsers kennen frame-ancestors nog niet; deze header doet
        // voor hen hetzelfde. Bewust dubbel.
        header('X-Frame-Options: DENY');

        // Verbiedt de browser om het meegestuurde Content-Type te negeren en
        // zelf het bestandstype te raden. Zonder dit kan een bestand dat wij
        // als tekst versturen alsnog als script uitgevoerd worden.
        header('X-Content-Type-Options: nosniff');

        // Bij het volgen van een link naar een andere site wordt de herkomst-URL
        // niet meegestuurd. Zo lekken bestelnummers en categorieën niet naar buiten.
        header('Referrer-Policy: same-origin');
    }
