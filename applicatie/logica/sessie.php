
<?php 

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

?>