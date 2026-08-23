<?php 

    // Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
    // Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
    // het script voordat er iets wordt uitgevoerd of getoond.
    if (!defined('TOEGANG_VIA_CONTROLLER')) {
        http_response_code(403);
        exit;
    }


// Wordt gebruikt voor het starten en managen van de sessies
function startSessie(): void
{
    
    // Als er al een sessie bestaat dan doe niks
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0, //cookie verdwijnt wanneer browser sluit
        'path' => '/', // geldt voor de hele website
        'httponly' => true, // Cross-site scripting guard
        'samesite' => 'Lax', // Bij een cross-site POST gaat de cookie niet mee (tegen CSRF)
    ]);

    session_start();
}