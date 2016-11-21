# Website
Deze repository bevat de bronbestanden voor het WordPress theme voor de website [www.martinehooptopbeter.nl](https://www.martinehooptopbeter.nl). Deze website is gratis ontwikkeld door [Marco Miltenburg](https://github.com/MarcoMiltenburg) van [Virtual Pages](http://www.virtualpages.nl) als sponsoring voor deze crowdfunding actie. Om ook andere MS-patienten de kans te geven om een website op te zetten voor crowdfunding is de broncode open source beschikbaar gemaakt onder de [MIT licentie](https://github.com/MartineHooptOpBeter/website/blob/develop/LICENSE). 

# Installatie
Als je upgrade van een 1.x versie van dit theme, kijk dan bij de hoofdstuk [Upgraden vanaf 1.x](#upgraden%20vanaf%201.x) voor meer informatie.

De bestanden voor het WordPress theme worden gegenereerd met behulp van de Gulp task runner. De bestanden in de `src` folder is dus **NIET** het WordPress theme. Voor het generen van het WordPress theme is [Node en NPM (Node Package Manager)](https://nodejs.org/) vereist. 

Maak een folder aan waar je de bestanden wilt plaatsen en zorg ervoor dat deze folder de huidige folder is. Clone de repository met GIT naar deze folder:

```
git clone https://github.com/MartineHooptOpBeter/website ./
```

Installeer vervolgens de dependencies met Composer:

```
php composer.phar install
```

Installeer tenslotte de NodeJS modules met NPM:

```
npm install
```

Alle dependencies zijn nu geinstalleerd.

[Download](https://wordpress.org/download/) de laatste versie van WordPress en installeer deze in de `wwwroot` folder. Deze folder is ook de folder waar je de webserver naar wilt verwijzen als de root folder van je website.

Het WordPress theme kan nu met Gulp worden gegenereerd met het volgende commando:

```
gulp --build=production
```

Het theme zal nu worden gegenereerd in de folder `wwwroot/wp-content/themes/martinehooptopbeter/`. Dit theme is geschikt om gebruikt te worden op een live website.

Kopieer het bestand `config.php` uit de root folder naar de folder  `wwwroot/wp-content/themes/martinehooptopbeter/`. Open dit bestand in deze folder in je favoriete teksteditor en pas de configuratie voor zover nodig aan. Met name de configuratie voor het verbinden met de database moet worden ingevuld. Zie de hoofdstukken [Database](#database) en [Configuratie](#configuratie) voor meer informatie.

Kopieer het bestand `sponsors.json` uit de root folder naar de folder  `wwwroot/wp-content/themes/martinehooptopbeter/sponsors/`. Open dit bestand in deze folder in je favoriete teksteditor en pas het voor zover nodig aan. In dit bestand kunnen in JSON formaat de logo's van de sponsors worden gedefinieerd. Zie het hoofdstuk [Sponsors](#sponsors) voor meer informatie.

# Database
Alhoewel het niet noodzakelijk is, is het verstandig om voor de tabel voor de donaties en voor de WordPress tabellen twee aparte databases aan te maken. Maar de tabellen kunnen eventueel ook in dezelfde database worden geplaatst. De tabellen voor WordPress worden door WordPress zelf aangemaakt bij het [installeren van WordPress](https://codex.wordpress.org/nl:Installatie). Open hiervoor de homepage van jouw website in een browser en WordPress zal de installatie starten.

De tabel voor de donaties kan worden aangemaakt met het SQL script `database/donations.sql`. Maak eerst een nieuwe database aan in MySQL en maak een gebruikersaccount aan welke `INSERT, SELECT, UPDATE` rechten heeft in deze database. Open vervolgens het script in MySQL Workbench (of een ander database management tool voor MySQL) en voer dit uit in de zojuist aangemaakte database. De tabel voor de donaties wordt nu aangemaakt. 

# Configuratie
De configuratie wordt gedefinieerd in het bestand `wwwroot/wp-content/themes/martinehooptopbeter/config.php`. Een voorbeeldbestand `config.php` staat in de root folder van het project.

Hieronder zijn alle configuratie opties beschreven welke betrekking hebben op betalingen.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['payments_dsn']` | Ja | *Geen* | De **data source name** voor het verbinden met de MySQL database voor de betalingen. Bijvoorbeeld: `mysql:host=localhost;dbname=martinehooptopbeter` |
| `$config['payments_username']` | Ja | *Geen* | De gebruikersnaam waarmee de applicatie moet inloggen op de database server voor de betalingen. |
| `$config['payments_password']` | Ja | *Geen* | Het wachtwoord van de gebruikersnaam waarmee de applicatie moet inloggen op de database server voor de betalingen. |

Hieronder zijn alle configuratie opties beschreven welke betrekking hebben op de donaties.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['donate_goal']` | Nee | 7500000 | Het doelbedrag op te halen met donaties (in euro centen). Dit bedrag is optioneel en als het niet is opgegeven wordt alleen het opgehaalde bedrag getoond zonder een doelbedrag en zonder een grafische weergave van hoeveel van het doelbedrag al is opgehaald. |
| `$config['donate_startdate']` | Nee | `mktime(0, 0, 0, 12, 31, 2015)` | De startdatum van de actie. Deze datum is optioneel en kan op `null` worden gezet als deze niet gebruikt moet worden. Als deze is opgegeven wordt deze gebruikt om aan te geven hoeveel donaties er zijn gedaan sinds deze datum. Zie [mktime()](http://php.net/manual/en/function.mktime.php) voor uitleg van de parameters van deze functie. |
| `$config['donate_minamount']` | Nee | 500 | Het minimumbedrag van een donatie (in euro centen). Als er geen minimumbedrag is opgegeven dan is dit 0. |
| `$config['donate_maxamount']` | Nee | 211200 | Het maximumbedrag van een donatie (in euro centen). Als er geen maximumbedrag is opgegeven dan geldt er geen maximum. |
| `$config['donate_email_fromaddress']` | Nee | *Array* | Het e-mailadres waarmee een bevestigingse-mail wordt gestuurd na ontvangst van de donatie. Als er geen e-mailadres is gedefinieerd wordt er geen bevestigingse-mail verstuurd. In plaats van een `string` met een enkel e-mailadres kan ook een `array` worden opgegeven met verschillende e-mailadressen per locale (zie uitleg verderop). |
| `$config['donate_email_fromname']` | Nee | *Array* | De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de donatie. Als er geen naam is gedefinieerd dan zal alleen het e-mailadres worden gebruikt (`$config['donate_email_fromaddress']`). In plaats van een `string` met een enkele naam kan ook een `array` worden opgegeven met verschillende namen per locale (zie uitleg verderop). |

Hieronder zijn alle configuratie opties beschreven welke betrekking hebben op de Ponyspeeldag.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['ponyplayday_price']` | Nee | 1750 | De kosten per kind voor het inschrijven voor de ponyspeeldag (in euro centen). Dit bedrag is optioneel en als het niet is opgegeven wordt is het niet mogelijk om in te schrijven voor de ponyspeeldag. |
| `$config['ponyplayday_events']` | Nee | '2036/1/19' | Een array met de start- en einddatum / tijd waarop de ponyspeeldag wordt gehouden. Zie het hoofdstuk [Ponyspeeldag](#ponyspeeldag) voor meer informatie over de syntax van deze parameter. | 
| `$config['ponyplayday_minage']` | Nee | 6 | De minimumleeftijd voor kinderen om zich te mogen inschrijven voor de ponyspeeldag. |
| `$config['ponyplayday_maxage']` | Nee | 12 | De maximumleeftijd voor kinderen om zich te mogen inschrijven voor de ponyspeeldag. |
| `$config['ponyplayday_email_fromaddress']` | Nee | *Array* | Het e-mailadres waarmee een bevestigingse-mail wordt gestuurd na ontvangst van de inschrijving. Als er geen e-mailadres is gedefinieerd wordt er geen bevestigingse-mail verstuurd. In plaats van een `string` met een enkel e-mailadres kan ook een `array` worden opgegeven met verschillende e-mailadressen per locale (zie uitleg verderop). |
| `$config['ponyplayday_email_fromname']` | Nee | *Array* | De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de inschrijving. Als er geen naam is gedefinieerd dan zal alleen het e-mailadres worden gebruikt (`$config['ponyplayday_email_fromaddress']`). In plaats van een `string` met een enkele naam kan ook een `array` worden opgegeven met verschillende namen per locale (zie uitleg verderop). |

Er wordt gebruik gemaakt van online betalingen via [Mollie](https://www.mollie.com/nl/). Hiervoor moet een account worden aangemaakt en een website worden gedefinieerd waarvan de volgende gegevens moeten worden ingevuld:

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['mollie_apikey']` | Ja | *Geen* | Dit is de API key welke door Mollie wordt gegeven voor de aangemaakte website. Mollie geeft per websites twee type key's, een test key en een live key. De test key begint altijd met de letter `test_` en de live key begint altijd met de letter `live_`. Let op dat je de juiste key gebruikt voor testen en voor de live website! |
| `$config['mollie_webhookurl']` | Ja | *Geen* | De webhook URL welke door Mollie zal worden aangeroepen op het moment dat er een statuswijziging is in één van de lopende betalingen. Deze moet verwijzen naar het bestand `mollie-webhook.php` in de WordPress theme folder. Let op dat je hier de volledige URL inclusief domeinnaam en folder vermeld. Als je website gebruik maakt van HTTPS dan is het verstandig om deze webhook ook over HTTPS te laten aanroepen. |

Als je gebruik wilt maken van Google Analytics kan je de Tracking ID configureren welke door Google wordt gegeven.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['googleanalytics_trackingid']` | Nee | *Array* | De Google Analytics Tracking ID van de website. Als er geen Tracking ID wordt opgegeven wordt er Google Analytics gebruikt. In plaats van een `string` met een enkele Tracking ID kan ook een `array` worden opgegeven met verschillende Tracking ID's per locale (zie uitleg verderop). |

Als je gebruik wilt maken van Google Search Console (voorheen Google Webmaster Tools) kan je de site verification tag configureren welke door Google wordt gegeven.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['googlesearch_siteverification']` | Nee | *Array* | De Google Search Console site verification tag van de website. Als er geen site verification tag wordt opgegeven wordt deze niet toegevoegd aan de meta tags. In plaats van een `string` met een enkele site verification tag kan ook een `array` worden opgegeven met verschillende site verification tags per locale (zie uitleg verderop). |

Het theme heeft een eenvoudig contactformulier ingebouwd. Met dit formulier kunnen bezoekers eenvoudig en snel een e-mail sturen met een vraag of opmerking. Het contactformulier werkt op iedere pagina welke het **Contact Page** template gebruikt.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['contact_sendmailto']` | Nee | *Array* | Het e-mailadres waar het contact formulier naar toe gestuurd moet worden. Als er geen e-mailadres is geconfiureerd dan zal het contactformulier zijn uitgeschakeld. In plaats van een `string` met een enkel e-mailadres kan ook een `array` worden opgegeven met verschillende e-mailadressen per locale (zie uitleg hieronder). |

Bij `$config['donate_email_fromaddress']`, `$config['donate_email_fromname']`, `$config['googleanalytics_trackingid']`, `$config['googlesearch_siteverification']` en `$config['contact_sendmailto']` kan in plaats van een `string` ook een `array` worden opgegeven om per locale een ander e-mailadres, naam of Tracking ID te kunnen opgeven. Het formaat voor de array is:

```php
$config['configuration_property'] = [
    ['locale' => 'en_US', 'value' => 'value_for_en_US'],
    ['locale' => 'nl_NL', 'value' => 'value_for_nl_NL'],
    ['locale' => '*',     'value' => 'value_for_other_locales']
];
```
Bijvoorbeeld:
```php
$config['contact_sendmailto'] = [
    ['locale' => 'en_US', 'value' => 'somebody@domain.com'],
    ['locale' => 'nl_NL', 'value' => 'iemand@domein.nl'],
    ['locale' => '*',     'value' => 'somebody.else@domain.org']
];
```
Er kunnen meerdere locale's worden opgegeven of er kan een fallback worden opgegeven met een `*` welke voor iedere andere niet gespecificeerde locale zal worden gebruikt. De fallback met een `*` moet als laatste element in de array worden opgenomen. Als er geen match is met één van de opgegeven locale's dan is dit hetzelfde alsof er geen waarde is opgegeven.  

# Ponyspeeldag
Voor de crowdfunding actie van de Stichting Martine Hoopt Op Beter wordt er ook een Ponyspeeldag georganiseerd. Dit onderdeel kan echter ook voor andere evenementen worden gebruikt waarvoor registratie vereist is. 

Voor de ponyspeeldag kan op verschillende manieren een datum en tijd worden opgegeven. De meest eenvoudige manier is een `string` voor als er maar een ponyspeeldag wordt georganiseerd. Deze string wordt dan letterlijk opgslagen als de datum/tijd bij de inschrijving. In plaats van een `string` kan ook een `array` worden opgegeven. De syntax voor deze array is:

```
[
    'startdatetime' => mktime(10, 30, 0, 1, 19, 2032),
    'enddatetime' => mktime(13, 0, 0, 1, 19, 2032),
    'closedays' => 3
]
```

De waarde van `closedays` bepaald wanneer de inschrijving voor de ponyspeeldag wordt gesloten. Bij de inschrijving wordt de opgegeven startdatum/tijd opgeslagen. De einddatum/tijd is alleen voor weergave op de website. 

Als er meerdere ponyspeeldagen worden gehouden dan kan er ook een multidimensional array worden opgegeven:

```
[
    [
        'startdatetime' => mktime(10, 30, 0, 1, 19, 2032),
        'enddatetime' => mktime(13, 0, 0, 1, 19, 2032),
        'closedays' => 3
    ],
    [
        'startdatetime' => mktime(14, 00, 0, 1, 26, 2032),
        'enddatetime' => mktime(16, 30, 0, 1, 26, 2032),
        'closedays' => 3
    ]
]
```

Als er meerdere ponyspeeldagen zijn opgegeven dan wordt er een meerkeuze getoond en kan de bezoeker van de website een keuze maken uit de dag en tijd (mits nog beschikbaar). 

# Sponsors
De sponsors worden gedefinieerd in het bestand `wwwroot/wp-content/themes/martinehooptopbeter/sponsors/sponsors.json`. Een voorbeeldbestand `sponsors.json` staat in de root folder van het project.

De opbouw van dit bestand is:

```json
{ "sponsors" : [
    {
        "name" : "Virtual Pages",
        "logo" : "virtual-pages.svg",
        "website" : "http://www.virtualpages.nl/"
    }
]}
```

Er kunnen extra sponsors worden toegevoegd door een extra element aan de `sponsors` array toe te voegen. Het is vestandig om voor de logo's gebruik te maken van SVG bestanden zodat deze onbeperkt en zonder verlies van kwaliteit kunnen worden vergroot en verkleind voor alle schermresoluties. Eventueel kunnen ook PNG bestanden worden gebruikt maar dit kan er minder mooit uit zien.

De bestanden van de logo's moeten ook in in de folder `wwwroot/wp-content/themes/martinehooptopbeter/sponsors` worden geplaatst. De logo's worden standaard even breed weergegeven. Afhankelijk van de schermresolutie worden er één of meerdere logo's naast elkaar weergegeven. Omdat het mogelijk is dat een logo visueel groter lijkt dan andere logo's kan middels `"logowidth"` de maximale breedte van een logo worden beperkt tot bijvoorbeed 70%:

```json
{ "sponsors" : [
    {
        "name" : "Virtual Pages",
        "logo" : "virtual-pages.svg",
        "website" : "http://www.virtualpages.nl/"
    },
    {
        "name" : "Andere Sponsor",
        "logo" : "ander-logo.svg",
        "logowidth" : "70%",
        "website" : "http://www.anderesponsor.nl/"
    }
]}
```

De logo's van de sponsors worden weergegeven op de pagina welke gebruik maakt van de **Sponsors Page** template.

# Ontwikkelen

## Aanmaken eigen fork
Om het WordPress theme zelf te kunnen aanpassen is het verstandig om eerst een eigen fork van het project te maken op Github. Ga hiervoor naar de [repository van de Martine Hoopt Op Beter website](https://github.com/MartineHooptOpBeter/website) op Github en zorg dat je bent ingelogd met je eigen account. Klik dan op de **Fork** knop om het project te forken naar je eigen account. Klik nu op de **Clone or download** knop om de clone URL van jouw eigen fork van de repository te zien / kopieren naar het clipboard. Deze repository kun je vervolgens clonen naar je lokale computer met het commando:

```
git clone https://github.com/JouwAccountNaam/website ./
``` 

Als je een eigen fork maakt van het project dan ontvang je niet automatisch wijzigigen en updates die door ons worden doorgevoerd nadat je de fork hebt gemaakt. Om dit wel mogelijk te maken kun je een extra remote toevoegen met de URL van de originele repository. Hiervan kun je dan op het moment dat je wilt de wijzigingen middels de `git fetch` en `git merge` of `git pull` commando's in je eigen fork integreren. Het aanmaken van de extra remote kan met het volgende git commando:

```
git remote add upstream https://github.com/MartineHooptOpBeter/website.git
```

Met het `git remote -v` commando kun je nu de twee remotes zien.

Het integreren van de wijzigingen van de originele Martine Hoopt Op Beter website repository begint met een fetch commando:

```
git fetch upstream
```

Om de wijzigingen van de laatste release uit de master branch te integrereren doe je eerst een checkout van de master branch in je lokale repository gevolgd door een merge vanaf de upstream remote:

```
git checkout master
git merge upstream/master
```

Een `fetch` en `merge` kunnen ook worden gecombineerd in één commando:

```
git pull upstream/master
```

De wijzigingen zijn nu in je lokale repository gemerged. Met een `git push origin master` commando kunnen deze wijzigingen nu ook naar je eigen Github repository worden gepushed.

## Pull requests
Als je zelf verbeteringen maakt die je met ons wilt delen of als je bugs hebt gevonden en de bugfixes hiervoor wilt maken dan accepteren we uiteraard ook pull requests. Maak hiervoor altijd een eigen branch vanaf de `develop` branch en wijzig alleen de bestanden die nodig zijn voor specifiek die wijziging of bugfix. Commit de wijzigingen in één of meerdere commits en push deze naar je eigen Github repository. Vanaf Github kun je nu voor deze branch een Pull request naar ons sturen. 

## Gulp
Het Martine Hoopt Op beter Wordpress Theme maakt ook gebruik van Gulp tijdens het ontwikkelen. Een ontwikkelversie van het theme
kan simpelweg worden gegenereerd met het `gulp` commando zonder extra opties:

```
gulp
```

of specifiek met de `development` build optie:

```
gulp --build=development
```

Het Gulp script `gulpfile.js` maakt gebruik van de [gulp-watch](https://www.npmjs.com/package/gulp-watch) en [BrowserSync](https://www.browsersync.io/) modules zodat bij wijzigingen in bronbestanden de bestanden van het WordPress theme direct worden gewijzigd en de wijzigingen in de browser worden geinjecteerd of de pagina automatisch wordt herladen. 

Als ontwikkelserver wordt standaard `http://127.0.0.1/` gebruikt. Het is mogelijk om een andere ontwikkelserver op te geven:

```
gulp --devhostname=192.168.1.12
```

of

```
gulp --devhostname=www.mijnwebsite.nl
```

Omdat WordPress automatisch links aanmaakt met de domeinnaam welke geconfigureerd is in WordPress is het belangrijk om de juiste ontwikkelserver op te geven en deze servernaam te laten verwijzen naar het IP adres van de ontwikkelserver. De Browsersync proxy vervangt de URL's van de ontwikkelserver on-the-fly zodat je toch gewoon op links kan blijven klikken tijdens het ontwikkelen en testen.

Als de WordPress website gebruik maakt van SSL/TLS kan een extra optie worden meegegeven om de website over HTTPS te laden:

```
gulp --devhostname=www.mijnwebsite.nl --devhostsecure=yes
```

Browsesync zal de website ook via HTTPS proxy'en en de browser kan daarom een foutmelding geven dat het certificaat van de site niet klopt. Je kunt hiervoor in de meeste browsers een tijdelijke (of permanente) uitzondering opgegeven. 

# Upgraden vanaf 1.x
Als je upgrade vanaf een 1.x versie van dit theme zijn er een aantal zaken gewijzigd.

## Configuratie
Enkele configuratie parameters zijn gewijzigd. Dit komt omdat de **donations** tabel / database zijn vervangen door meer generieke **payments** tabel. De configuratieparameters `$config['donate_dsn']`, `$config['donate_username']` en `$config['donate_password']` zijn vervangen door `$config['payments_dsn']`, `$config['payments_username']` en `$config['payments_password']`. De betekenis van de parameters is niet gewijzigd.

## Database
De database tabel voor de donaties is vervangen door een meer generieke **payments** tabel. Hierin kunnen naast donaties ook betalingen en gegevens van andere type betalingen (zoals de ponyspeeldag) worden opgeslagen. Om de data van bestaande donaties over te zetten is er een script `convert-donations-to-payments.php` in folder `wwwroot/wp-content/themes/martinehoopoptbeter/ `welke de data van de tabel `tbl_donations` converteert naar de tabel `tbl_payments`. Het script moet worden uitgevoerd vanaf de commandline.
