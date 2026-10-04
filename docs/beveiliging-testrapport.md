# Beveiligingsrapport — Pizzeria Sole Machina

Dit document beschrijft welke beveiligingsmaatregelen in de applicatie zijn
ingebouwd, en met welke tests is gecontroleerd of die maatregelen echt werken.
Elke test is uitgevoerd tegen de draaiende applicatie, niet alleen op papier.

- **Applicatie:** PHP 8.4 met PDO, Microsoft SQL Server 2022, beide in Docker
- **Testomgeving:** `docker compose up`, webserver op `localhost:8080`
- **Testdatum:** 3 oktober 2026
- **Testmethode:** handmatige requests met `curl` en directe queries met `sqlcmd`

---

## 1. Opzet van de test

De tests zijn opgedeeld in de volgende categorieën:

| # | Categorie | Waarom getest |
|---|---|---|
| A | SQL-injectie | De applicatie bouwt queries met gebruikersinvoer |
| B | Autorisatie | Personeelspagina's mogen niet voor klanten toegankelijk zijn |
| C | Rechtenverhoging | Een klant mag zich geen personeelsrol kunnen geven |
| D | Invoervalidatie | Formulieren en URL-parameters komen van buiten |
| E | XSS | Klantgegevens worden op het personeelsscherm getoond |
| F | Sessiebeveiliging | De sessie bepaalt wie je bent |
| G | Databaserechten | Schade beperken als er tóch iets doorkomt |
| H | Informatielekken | Configuratie en foutmeldingen mogen niet uitlekken |
| I | Beschikbaarheid | Een query mag niet onbeperkt kunnen groeien |

Een test is **geslaagd** als de aanval niet lukt én de applicatie netjes blijft
werken (geen crash, geen lege pagina, geen zichtbare foutmelding).

---

## 2. Ingebouwde maatregelen

| Maatregel | Waar in de code |
|---|---|
| Prepared statements met benoemde parameters voor élke query; nergens invoer in een SQL-string geplakt | hele map `applicatie/data/`, bijv. `gebruikers.php:14-16` |
| Databasefouten als exception, zodat een mislukte query niet stil doorgaat | `data/db_connectie.php:21` |
| Databasegegevens uit omgevingsvariabelen in plaats van hardcoded | `data/db_connectie.php:4-7` |
| Applicatie stopt direct als de configuratie ontbreekt | `data/db_connectie.php:10-12` |
| Wachtwoord wordt na verbinden uit het geheugen gehaald | `data/db_connectie.php:18` |
| Databaseaccount met minimale rechten: alleen `SELECT`, `INSERT`, `UPDATE`; geen `DELETE` en geen DDL | `webserver-setup/pizzeria.sql:287` |
| Wachtwoorden gehasht met bcrypt (`password_hash` / `password_verify`) | `logica/authenticatie.php:99` en `:14` |
| Rol staat vast op `'Client'` bij registratie; de rol komt niet uit het formulier | `data/gebruikers.php:58` |
| Rolcontrole aan de serverkant op beide personeelspagina's | `bestellingsoverzicht_personeel.php:14`, `bestellingsoverzicht_bezorger.php:13` |
| Sessiecookie met `HttpOnly` (niet leesbaar voor JavaScript) en `SameSite=Lax` | `logica/sessie.php:15-16` |
| Nieuw sessie-id bij inloggen en registreren, oude sessie wordt verwijderd (tegen session fixation) | `logica/authenticatie.php:19` en `:104` |
| Alle tekst uit de database door `htmlspecialchars()`; getallen door `(int)` of `number_format()` | hele map `applicatie/presentatie/` |
| Categorie uit de URL getoetst aan de lijst uit de database (whitelist) | `index.php:22` |
| Statuscode getoetst aan een vaste lijst voordat die de database in gaat | `logica/bestelling.php:177-193` |
| Product moet in de database bestaan voordat het in het mandje mag | `logica/winkelmandje.php:129` |
| Aantal per product afgetopt op 50 | `logica/winkelmandje.php:5` |
| Bestelnummer komt uit de sessie, niet uit de URL (geen IDOR) | `bestelling_geschiedenis.php:33` |
| POST-redirect-GET, zodat F5 geen tweede wijziging opslaat | `bestellingsoverzicht_personeel.php:22-28` |
| Uitloggen reageert alleen op POST | `uitloggen.php:7` |
| Alle lijstquery's begrensd met paginering (zie §5) | `logica/paginatie.php` |

---

## 3. Uitgevoerde tests

### A. SQL-injectie

**A1 — Inlogformulier omzeilen met SQL-injectie**

Vijf payloads op het veld `gebruikersnaam`:

```
' OR '1'='1' --
admin'--
' OR 1=1--
'; DROP TABLE [User];--
' UNION SELECT username,password,1,1,1 FROM [User]--
```

```bash
curl -s --data-urlencode "gebruikersnaam=<payload>" \
     --data-urlencode "wachtwoord=x" http://localhost:8080/login.php
```

| Verwacht | Resultaat |
|---|---|
| Alle pogingen afgewezen, tabellen intact | **Geslaagd.** Alle vijf gaven "Gebruikersnaam of wachtwoord is onjuist." Alle 7 tabellen bestaan daarna nog. |

Maatregel: prepared statement in `haalGebruikerOpMetGebruikersnaam()`
(`data/gebruikers.php:14-16`). De payload wordt als één string vergeleken met de
kolom `username` en nooit als SQL uitgevoerd.

**A2 — Injectie via de URL-parameter `categorie`**

| Invoer | Resultaat |
|---|---|
| `Maaltijd` (geldig) | tab `Maaltijd` actief |
| `Pizza' OR '1'='1` | valt terug op `Drank` |
| `'; DROP TABLE Product--` | valt terug op `Drank` |
| `NietBestaand` | valt terug op `Drank` |
| `../../etc/passwd` | valt terug op `Drank` |

**Geslaagd.** De parameter wordt eerst getoetst aan de categorieën uit de
database (`index.php:22`), dus onbekende waarden bereiken de query nooit.

**A3 — Injectie via het statusveld van het personeelsformulier**

| Invoer `status` | Status in database na de poging |
|---|---|
| `99` | onveranderd (2) |
| `0` | onveranderd (2) |
| `-1` | onveranderd (2) |
| `abc` | onveranderd (2) |
| `2; DROP TABLE [User]--` | onveranderd (2), tabel `User` bestaat nog (43 rijen) |
| `1 OR 1=1` | gewijzigd naar 1 |

**Geslaagd, met een toelichting bij de laatste regel.** `(int) "1 OR 1=1"` levert
in PHP het getal `1` op. Dat is een *geldige* status, dus de wijziging naar 1 is
een normale statuswijziging en geen injectie. De rest van de payload verdwijnt
bij de cast. Er is dus geen SQL uitgevoerd; `isGeldigeStatus()`
(`logica/bestelling.php:177`) heeft alleen een geldig getal doorgelaten.

**A4 — Injectie via het paginanummer**

Zie I1. Ook `pagina=1; DROP TABLE Pizza_Order--` liet alle tabellen intact.

### B. Autorisatie

**B1 — Personeelspagina's opvragen zonder in te loggen**

```bash
curl -s -i http://localhost:8080/bestellingsoverzicht_personeel.php
curl -s -i http://localhost:8080/bestellingsoverzicht_bezorger.php
```

| Verwacht | Resultaat |
|---|---|
| Doorverwijzing naar inlogpagina | **Geslaagd.** Beide: `HTTP/1.1 302 Found`, `Location: login.php` |

**B2 — Personeelspagina's opvragen als ingelogde klant**

Account `testklant1` aangemaakt via het registratieformulier, daarna beide
pagina's opgevraagd met die sessiecookie.

| Verwacht | Resultaat |
|---|---|
| Doorverwijzing, geen bestelgegevens | **Geslaagd.** Beide `302` naar `login.php`, terwijl dezelfde sessie wél gewoon op `index.php` kwam. |

Maatregel: `isPersoneel()` controleert de rol uit de sessie, niet een veld uit
het verzoek.

### C. Rechtenverhoging

**C1 — Personeelsrol afdwingen via het registratieformulier**

```bash
curl -s -d "gebruikersnaam=hacker1&wachtwoord=Hack123!&bevestig-wachtwoord=Hack123!\
&voornaam=Ha&achternaam=Cker&straat=Straat&huisnummer=1&postcode=1111AA&stad=Stad\
&role=Personnel&rol=Personnel" http://localhost:8080/registratie.php
```

| Verwacht | Resultaat |
|---|---|
| Account krijgt rol `Client` | **Geslaagd.** In de database: `hacker1 → Client`. Met dat account gaf de personeelspagina `302` naar `login.php`. |

Maatregel: de rol staat hardcoded in de INSERT (`data/gebruikers.php:58`); de
velden `role` en `rol` uit het formulier worden niet gebruikt.

**C2 — Opslagvorm van het wachtwoord**

Na registratie begint de kolom `password` met `$2y$12$` — een bcrypt-hash met
cost 12. Het ingetypte wachtwoord staat dus niet leesbaar in de database.

### D. Invoervalidatie

**D1 — Niet-bestaand product in het mandje leggen**

`product=GratisPizza9999` toevoegen.

| Verwacht | Resultaat |
|---|---|
| Niets toegevoegd | **Geslaagd.** Mandje bleef leeg; `bestaatProduct()` wees het af. |

**D2 — Aantallen manipuleren op een bestaand product**

| Invoer `aantal` | Aantal in mandje |
|---|---|
| `99999` | 50 (afgetopt) |
| `1e9` | 50 (afgetopt) |
| `-5` | niets toegevoegd |
| `0` | niets toegevoegd |

**Geslaagd.** `MAX_AANTAL` (50) kapt grote waarden af; nul en negatief worden
genegeerd.

**D3 — Onbekende productnamen bij het bestellen**

Tijdens het testen zijn per ongeluk de namen `Margherita`, `Tiramisu` en `Fanta`
verstuurd. Die bestaan niet; ze zijn stil genegeerd en kwamen niet in de
bestelling terecht. Dit bevestigt D1 nog een keer, via een andere route.

### E. Cross-site scripting (XSS)

**E1 — Opgeslagen XSS via naam en adres van een gastbestelling**

Bestelling geplaatst met:

```
naam=<script>alert("xss")</script>
straat=<svg onload=alert(1)>
```

Controle in de database (hex van `client_name`):

```
3C007300630072006900700074003E0061006C0065007200740028002200780073007300220029003C002F007300630072006900700074003E00
```

Dat is exact `<script>alert("xss")</script>`, lengte 29 — de payload wordt dus
**ongewijzigd opgeslagen**. De bescherming zit niet in de opslag maar in de
weergave. De bestelling is daarna naar de bezorgstatus gezet en als personeel
opgevraagd:

| Verwacht | Resultaat |
|---|---|
| Payload als tekst, niet uitgevoerd | **Geslaagd.** 0 keer uitvoerbaar `<script>alert`, 3 keer ge-escapet. Weergegeven als `&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;` |

Maatregel: `htmlspecialchars()` in `presentatie/bestellingsoverzicht_bezorger.php:22-23`.

**E2 — Controle van alle uitvoer in de templates**

Alle `<?= ... ?>`-plekken in `applicatie/presentatie/` zijn nagelopen. Elke
plek die níet door `htmlspecialchars()` gaat, gaat door `(int)`,
`number_format()` of `urlencode()`, of is een vaste tekst uit de code zelf.
Er is dus geen plek waar ongefilterde databasetekst in de HTML belandt.

### F. Sessiebeveiliging

**F1 — Cookie-instellingen**

```
Set-Cookie: PHPSESSID=...; path=/; HttpOnly; SameSite=Lax
```

**Geslaagd** voor `HttpOnly` (JavaScript kan de cookie niet lezen, wat
cookiediefstal via XSS tegengaat) en `SameSite=Lax`. De vlag `Secure` ontbreekt;
dat kan ook niet anders zolang de testomgeving op `http://` draait. Bij
uitlevering over HTTPS hoort die vlag erbij.

**F2 — Uitloggen via GET**

| Verwacht | Resultaat |
|---|---|
| GET logt niet uit | **Geslaagd.** Na `GET /uitloggen.php` volgde wel een redirect, maar de sessie bleef geldig: de personeelspagina gaf nog `200`. Alleen een POST logt uit (`uitloggen.php:7`). |

Dit voorkomt dat iemand je uitlogt met een `<img src="...uitloggen.php">`.

**F3 — Nieuw sessie-id na inloggen (session fixation)**

Uitgevoerd op 5 oktober 2026, na het oplossen van §4.3. Eerst een sessie
opgehaald met een GET, daarna met diezelfde cookie het formulier verstuurd.

| Stap | Sessie-id voor | Sessie-id na |
|---|---|---|
| Registreren (`regtest_28827`) | `be81e812306ae46f7d0c1055d0963ba8` | `0a11fb6f944f469a3b8beae85b9c7164` |
| Inloggen (`regtest_28827`) | `dfc127307917121ced421a2d23aa117f` | `537c7ee331616154e46fca91570a7887` |

Daarna `index.php` opgevraagd met beide id's van de inlogtest:

| Verwacht | Resultaat |
|---|---|
| Nieuw id is ingelogd, oud id niet | **Geslaagd.** Met het nieuwe id staat de uitlogknop op de pagina, met het oude id niet. |

Een sessie-id dat iemand vooraf kent, is na het inloggen dus waardeloos. De
nieuwe cookie houdt `HttpOnly` en `SameSite=Lax`. Maatregel:
`session_regenerate_id(true)` in `logica/authenticatie.php:19` (inloggen) en
`:104` (registreren, omdat je daarna meteen bent ingelogd). Door `true` wordt de
oude sessie op de server verwijderd en niet alleen het id vervangen.

### G. Databaserechten

**G1 — Rechten van het applicatieaccount**

```sql
SELECT permission_name FROM fn_my_permissions('dbo','SCHEMA');
```

Resultaat: precies `INSERT`, `SELECT`, `UPDATE`. Niets meer.

**G2 — Verboden bewerkingen proberen als dat account**

| Poging | Resultaat |
|---|---|
| `DELETE FROM Pizza_Order WHERE order_id = 1` | `Msg 229: The DELETE permission was denied on the object 'Pizza_Order'` |
| `DROP TABLE Pizza_Order_Product` | `Msg 3701: Cannot drop the table ... you do not have permission` |

**Geslaagd.** Zelfs als een injectie ooit zou slagen, kan er met dit account geen
data verwijderd worden en geen tabel gesloopt. Dit is de tweede verdedigingslaag
onder de prepared statements.

**G3 — Wat het account wél mag**

Het account mag alle rijen van `[User]` lezen, inclusief de wachtwoordkolom. Dat
is nodig om in te loggen (`password_verify` vergelijkt in PHP), maar het betekent
dat de hashes bij een lek meekomen. Omdat het bcrypt-hashes met cost 12 zijn,
zijn ze niet zomaar terug te rekenen.

### H. Informatielekken

**H1 — Zijn configuratiebestanden via de webserver op te vragen?**

| Pad | HTTP | Inhoud |
|---|---|---|
| `/variables.env` | 200 | 4916 bytes |
| `/docker-compose.yml` | 200 | 4916 bytes |
| `/.git/config` | 200 | 4916 bytes |
| `/../webserver-setup/pizzeria.sql` | 200 | 4916 bytes |

De code 200 lijkt alarmerend, maar alle vier geven exact hetzelfde aantal bytes
als `/index.php` (4916): de ingebouwde PHP-webserver stuurt voor onbekende paden
de homepage terug. Gezocht op `SA_PASSWORD`, `Pizz3ria`, `APP_DB_USER` en
`abc123` in de antwoorden: **0 treffers**. De bestanden staan ook niet in de
webroot, want alleen `./applicatie/` is als volume aangekoppeld.

**Geslaagd**, met als aantekening dat dit aan de mapindeling te danken is en niet
aan een regel in de webserver.

**H2 — Foutmeldingen bij direct opvragen van een template**

`GET /presentatie/bestellingsoverzicht_bezorger.php` geeft PHP-waarschuwingen in
de pagina (`Undefined variable $bestellingen`). `display_errors` staat op
`STDOUT`.

**Niet geslaagd — aandachtspunt.** Dit is logisch voor een ontwikkelomgeving,
maar op productie horen fouten in een logbestand en niet in de pagina. Een
aanvaller leest hier bestandspaden en variabelenamen uit. Zie §4.

**H3 — IDOR: bestelling van iemand anders opvragen**

Als gast, zonder eigen bestelling, geprobeerd:

```
/bestelling_geschiedenis.php?bestelling=2
/bestelling_geschiedenis.php?bestelnummer=2
/bestelling_geschiedenis.php?order_id=2
/bestelling_geschiedenis.php?laatsteBestelling=2
```

| Verwacht | Resultaat |
|---|---|
| Geen enkele bestelling zichtbaar | **Geslaagd.** Alle vier: 0 bestellingen. |

Maatregel: de pagina leest het bestelnummer uit `$_SESSION['laatsteBestelling']`
en kijkt niet naar de URL (`bestelling_geschiedenis.php:33`). Een ingelogde klant
krijgt alleen bestellingen waar zijn eigen gebruikersnaam in de `WHERE` staat.

### I. Beschikbaarheid

**I1 — Manipuleren van het paginanummer**

Overzicht met 25 bestellingen, 10 per pagina (3 pagina's):

| Invoer `pagina` | Bestellingen op de pagina | Weergave | PHP-fouten |
|---|---|---|---|
| `1` | 10 | Pagina 1 van 3 | 0 |
| `3` | 5 | Pagina 3 van 3 | 0 |
| `99999` | 5 | Pagina 3 van 3 | 0 |
| `9999999999999999999` | 5 | Pagina 3 van 3 | 0 |
| `-5` | 10 | Pagina 1 van 3 | 0 |
| `0` | 10 | Pagina 1 van 3 | 0 |
| `abc` | 10 | Pagina 1 van 3 | 0 |
| `1.9` | 10 | Pagina 1 van 3 | 0 |
| *(leeg)* | 10 | Pagina 1 van 3 | 0 |
| `2 OR 1=1` | 10 | Pagina 2 van 3 | 0 |
| `1; DROP TABLE Pizza_Order--` | 10 | Pagina 1 van 3 | 0 |

**Geslaagd.** Wat er ook wordt ingevuld, er komen nooit meer dan 10 bestellingen
terug en er verschijnt geen foutmelding. Te hoge waarden worden afgekapt op de
laatste bestaande pagina, zodat de database geen enorme `OFFSET` hoeft te
verwerken. Alle tabellen bleven na deze reeks intact.

**I2 — Brute force op het inlogformulier**

20 foute inlogpogingen achter elkaar.

| Verwacht | Resultaat |
|---|---|
| Afremmen of blokkeren | **Niet geslaagd.** 20 pogingen in 4 seconden, alle met `HTTP 200`, geen vertraging, geen blokkade. Direct daarna lukte inloggen met het juiste wachtwoord gewoon. |

Zie §4.

---

## 4. Bekende aandachtspunten

Deze punten zijn tijdens het testen gevonden en bewust opgeschreven in plaats van
weggelaten.

**4.1 — `variables.env` staat in de repository**

`git ls-files variables.env` vindt het bestand, en
`git log -S "Pizz3ria!App2024"` laat zien dat het wachtwoord in commit `e074d98`
is toegevoegd. De wachtwoorden staan dus niet meer in de PHP-code, maar nog wel
in de versiegeschiedenis. `.gitignore` bevat alleen `.DS_Store`.

Nette oplossing: `variables.env` in `.gitignore`, een `variables.env.example`
zonder echte waarden meeleveren, en de wachtwoorden vervangen omdat ze als gelekt
moeten worden beschouwd.

**4.2 — Geen CSRF-tokens**

De formulieren bevatten geen token. Getest met een POST met
`Origin: http://evil.example.com` en `Referer: http://evil.example.com/attack.html`
naar het bezorgoverzicht: de status van bestelling 2003 ging van 5 naar 6. De
server kijkt dus niet naar de herkomst van het verzoek.

Belangrijk om zuiver te blijven: in deze test is de cookie expres met `curl`
meegestuurd. Een echte browser doet dat bij een cross-site POST niet, want
`SameSite=Lax` houdt de cookie tegen. De aanval werkt dus niet zomaar vanuit een
browser, maar de server heeft er zelf geen controle op. Een token per formulier
zou dit in de applicatie zelf oplossen in plaats van het aan het cookiebeleid van
de browser over te laten.

**4.3 — Geen `session_regenerate_id()` bij inloggen (opgelost)**

Gemeten sessie-id voor en na inloggen, op de oorspronkelijke testdatum:

```
voor:  6969629d74b39a31bb80fc7a3ab59a87
na:    6969629d74b39a31bb80fc7a3ab59a87
```

Het id verandert niet. Wie iemand vooraf een bekend sessie-id kan opdringen,
heeft na het inloggen van die persoon een geldige sessie (session fixation). Eén
regel `session_regenerate_id(true)` direct na een gelukte login lost dit op.

**Opgelost op 5 oktober 2026.** `session_regenerate_id(true)` staat nu in
`logInGebruiker()` en in `registreerGebruiker()`. Hertest: zie F3.

**4.4 — Testaccounts in `pizzeria.sql` kunnen niet inloggen**

De 20 meegeleverde accounts hebben de letterlijke tekst `wachtwoord` in de
wachtwoordkolom staan; er staat geen enkele bcrypt-hash in het bestand
(`grep -c '\$2y\$' webserver-setup/pizzeria.sql` geeft 0). Omdat de applicatie
`password_verify()` gebruikt, wordt zo'n waarde altijd afgewezen:

```
rdeboer / wachtwoord → "Gebruikersnaam of wachtwoord is onjuist."
```

Dat is op zichzelf correct gedrag van de maatregel, maar het betekent dat er geen
werkend personeelsaccount is. Voor deze tests is daarom het account
`testpersoneel` aangemaakt met een echte bcrypt-hash. Voor de demonstratie is het
handig om de seed-wachtwoorden in `pizzeria.sql` te vervangen door hashes.

**4.5 — Geen rem op inlogpogingen**

Zie I2. Mogelijke maatregelen: een korte wachttijd na een fout wachtwoord, of een
teller per gebruikersnaam of IP-adres.

**4.6 — `display_errors` staat aan**

Zie H2. Op productie uitzetten en naar een logbestand schrijven.

**4.7 — `TrustServerCertificate=1`**

Staat in `data/db_connectie.php:15` en is daar in een comment ook al als
aandachtspunt benoemd. De verbinding met de database accepteert elk certificaat,
dus er is geen bescherming tegen een man-in-the-middle tussen webserver en
database. Voor de lokale Docker-omgeving is dat acceptabel, op productie niet.

**4.8 — Testdata in de database**

Voor deze tests zijn `testpersoneel`, `testklant1`, `hacker1`, `regtest_28827` en een aantal
bestellingen aangemaakt. Opruimen kan niet met het applicatieaccount, want dat
heeft geen `DELETE`-recht (zie G2) — dat moet met het `sa`-account of door het
Docker-volume opnieuw op te bouwen.

---

## 5. Begrenzing van query's (paginering)

Een query zonder bovengrens is ook een beveiligingsrisico: hij werkt prima bij 20
bestellingen en legt de pagina plat bij 200.000. Daarom haalt elke lijstquery nu
maximaal één pagina op.

### Wat er is aangepast

| Query | Aanpak | Bestand |
|---|---|---|
| `haalBestellingenVanKlant` | paginering (10 bestellingen per pagina) | `data/bestellingen.php` |
| `haalBestellingenMetStatus` | paginering (keuken- én bezorgoverzicht) | `data/bestellingen.php` |
| `haalProductenMetIngredienten` | paginering per categorie | `data/producten.php` |
| `haalPrijzenVanProducten` | begrensd met `WHERE name IN (...)` op de inhoud van het mandje | `data/producten.php` |
| `haalIngredientenPerProduct` | begrensd met `WHERE product_name IN (...)` op de producten van de huidige pagina | `data/producten.php` |
| `haalProductTypes` | begrensd met `TOP (:maximum)`, max 25 categorieën | `data/categorieen.php` |
| `haalBestellingMetBestelnummer` | geen maatregel nodig: precies één bestelling, en het aantal regels is door de primary key begrensd | `data/bestellingen.php` |

De laatste twee opzoeklijsten zijn met opzet **niet** gepagineerd. Het zijn
complete opzoektabellen (naam → prijs, product → ingrediënten). Een halve pagina
daarvan zou betekenen dat `haalWinkelmandjeRegels()` producten waarvan het de
prijs niet vindt stil uit het mandje gooit. In plaats van pagineren vragen ze nu
alleen de rijen op die daadwerkelijk nodig zijn.

### Waarom er een subquery in de SQL staat

De bestellingquery's koppelen `Pizza_Order` aan `Pizza_Order_Product`, dus één
bestelling levert meerdere rijen op. `OFFSET/FETCH` direct op dat resultaat
zetten gaat mis. Gemeten met 3 per pagina:

```
Fout (paginering op de gekoppelde rijen):
  offset 0 -> 2 bestellingen: #2 (2 regels)  #3 (1 regel)
  offset 3 -> 2 bestellingen: #3 (1 regel)   #4 (2 regels)

Goed (paginering op de bestellingen in een CTE):
  offset 0 -> 3 bestellingen: #2 (2 regels)  #3 (2 regels)  #4 (2 regels)
  offset 3 -> 3 bestellingen: #5 (1 regel)   #6 (2 regels)  #7 (1 regel)
```

In de foute versie staat bestelling #3 op beide pagina's, elke keer met de helft
van de producten, en levert "3 per pagina" maar 2 bestellingen op. Daarom bepaalt
een CTE eerst welke `order_id`'s op deze pagina horen, en komen de regels daarna
erbij.

### Controle

| Controle | Resultaat |
|---|---|
| 25 bestellingen over 3 pagina's | 10 + 10 + 5 = 25 |
| Komt een bestelling op twee pagina's voor? | nee, 25 unieke bestelnummers |
| Blijft een bestelling met 4 producten heel? | ja, alle 4 regels op één pagina |
| Opgehaalde regels versus regels in de database | 8 tegen 8 |
| Pizza-categorie (4 producten) bij 2 per pagina | 2 + 2 + 0, pizza met 7 ingrediënten bleef heel |
| Manipuleren van `?pagina=` | zie I1: nooit meer dan 10 rijen |

### Detail dat opviel

`OFFSET` en `FETCH NEXT` accepteren geen string. De stijl die elders in dit
project wordt gebruikt — `execute([':x' => $waarde])` — stuurt alles als string
en levert deze fout op:

```
SQLSTATE[42000]: The number of rows provided for a TOP or FETCH clauses
row count parameter must be an integer.
```

Daarom gebruiken de gepagineerde query's `bindValue(':offset', $offset,
PDO::PARAM_INT)`. Hetzelfde geldt voor `TOP (:maximum)` in `categorieen.php`.

### Paginagrootte komt niet uit de URL

Alleen het paginanúmmer is door de bezoeker te beïnvloeden; de paginagrootte
staat als constante in `logica/paginatie.php` (`ITEMS_PER_PAGINA = 10`, met
`MAX_ITEMS_PER_PAGINA = 50` als harde bovengrens). Er is dus geen `?per=100000`
mogelijk. Het paginanummer wordt bovendien afgekapt op de laatste bestaande
pagina, zodat een extreem hoog nummer geen grote `OFFSET` oplevert.

---

## 6. Tests herhalen

```bash
# omgeving starten
docker compose up -d

# rechten van het applicatieaccount (G1)
docker compose exec web_server /opt/mssql-tools18/bin/sqlcmd -C -S database_server \
  -U pizzeria_app -P 'Pizz3ria!App2024' -d pizzeria \
  -Q "SELECT permission_name FROM fn_my_permissions('dbo','SCHEMA');"

# DELETE moet geweigerd worden (G2)
docker compose exec web_server /opt/mssql-tools18/bin/sqlcmd -C -S database_server \
  -U pizzeria_app -P 'Pizz3ria!App2024' -d pizzeria \
  -Q "DELETE FROM Pizza_Order WHERE order_id = 1;"

# SQL-injectie op het inlogformulier (A1)
curl -s --data-urlencode "gebruikersnaam=' OR '1'='1' --" \
        --data-urlencode "wachtwoord=x" http://localhost:8080/login.php | grep onjuist

# personeelspagina zonder inloggen (B1)
curl -s -i http://localhost:8080/bestellingsoverzicht_personeel.php | grep -i location

# cookie-instellingen (F1)
curl -s -i http://localhost:8080/index.php | grep -i set-cookie

# nieuw sessie-id na inloggen (F3): vergelijk PHPSESSID voor en na
curl -s -c jar.txt -o /dev/null http://localhost:8080/login.php
grep PHPSESSID jar.txt
curl -s -b jar.txt -c jar.txt -o /dev/null --data-urlencode "gebruikersnaam=regtest_28827" \
        --data-urlencode "wachtwoord=Test123!" http://localhost:8080/login.php
grep PHPSESSID jar.txt

# paginanummer manipuleren (I1)
curl -s "http://localhost:8080/bestellingsoverzicht_personeel.php?pagina=99999"
```

De payloads met `<`, `>` en `/` het beste met `--data-urlencode` versturen. In
Git Bash op Windows worden ze anders onderweg aangepast: `</script>` werd daar
stil `<C:/Program Files/Git/script>`, waardoor een XSS-test iets anders test dan
bedoeld. Dat is tijdens dit onderzoek één keer gebeurd en daarna overgedaan
vanuit de container.
