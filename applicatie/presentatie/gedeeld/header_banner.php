<?php
// Beveiliging: dit bestand hoort alleen via een controller geladen te worden.
// Zonder die constante is het rechtstreeks in de browser opgevraagd; dan stopt
// het script voordat er iets wordt uitgevoerd of getoond.
if (!defined('TOEGANG_VIA_CONTROLLER')) {
    http_response_code(403);
    exit;
}
?>
<div class="banner"><img class="banner-img" src="./images/banner.png" alt="een getekend plaatje met een pizza-oven en een italiaans landschap in zonnige kleuren."></div>
