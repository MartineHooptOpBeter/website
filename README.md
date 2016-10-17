# Website
Deze repository bevat de bronbestanden voor het WordPress theme voor de website [www.martinehooptopbeter.nl](https://www.martinehooptopbeter.nl). Deze website is gratis ontwikkeld door [Marco Miltenburg](https://github.com/MarcoMiltenburg) van [Virtual Pages](http://www.virtualpages.nl) als sponsoring voor deze crowdfunding actie. Om ook andere MS-patienten de kans te geven om een website op te zetten voor crowdfunding is de broncode open source beschikbaar gemaakt onder de [MIT licentie](https://github.com/MartineHooptOpBeter/website/blob/develop/LICENSE). 

# Installatie
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

Kopieer het bestand `config.php` uit de root folder naar de folder  `wwwroot/wp-content/themes/martinehooptopbeter/`. Open dit bestand uit deze folder in je favoriete teksteditor en pas de configuratie voor zover nodig aan. Met name de configuratie voor het verbinden met de database moet worden ingevuld. Zie de hoofdstukken [Database](#database) en [Configuratie](#configuratie) voor meer informatie.

Kopieer het bestand `sponsors.json` uit de root folder naar de folder  `wwwroot/wp-content/themes/martinehooptopbeter/sponsors/`. Open dit bestand uit deze folder in je favoriete teksteditor en pas het voor zover nodig aan. In dit bestand kunnen in JSON formaat de logo's van de sponsors worden gedefinieerd. Zie het hoofdstuk [Sponsors](#sponsors) voor meer informatie.

# Database
Alhoewel het niet noodzakelijk is, is het verstandig om voor de tabel voor de donaties en voor de WordPress tabellen twee aparte databases aan te maken. Maar de tabellen kunnen eventueel ook in dezelfde database worden geplaatst. De tabellen voor WordPress worden door WordPress zelf aangemaakt bij het [installeren van WordPress](https://codex.wordpress.org/nl:Installatie). Open hiervoor de homepage van jouw website in een browser en WordPress zal de installatie starten.

De tabel voor de donaties kan worden aangemaakt met het SQL script `database/donations.sql`. Maak eerst een nieuwe database aan in MySQL en maak een gebruikersaccount aan welke `DELETE, INSERT, SELECT, UPDATE` rechten heeft in deze database. Open vervolgens het script in MySQL Workbench (of een ander database management tool voor MySQL) en voor dit uit in de zojuist aangemaakte database. De tabel voor de donaties wordt nu aangemaakt. 

# Ontwikkelen
Om het WordPress theme zelf te kunnen aanpassen kan Gulp gebruikt worden tijdens het ontwikkelen. Om het theme
kan nu simpelweg worden gegenereerd met Gulp zonder extra opties:

```
gulp
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

Omdat WordPress automatisch links aanmaakt met de domeinnaam welke geconfigureerd is in WordPress kan het belangrijk zijn om de juiste ontwikkelserver op te geven en deze servernaam te laten verwijzen naar het IP adres van de ontwikkelserver.

Als de WordPress website gebruik maakt van SSL/TLS kan een extra optie worden meegegeven om de website over HTTPS te laden:

```
gulp --devhostname=www.mijnwebsite.nl --devhostsecure=yes
```

De browser zal op de lokale machine zal de website nog steeds inladen via HTTP vanaf de Browsersync proxy maar deze zal de website wel via HTTPS inladen. De Browsersync proxy vervangt de URL's on-the-fly zodat je toch gewoon op links kan blijven klikken tijdens het ontwikkelen en testen.

# Configuratie
De configuratie wordt gedefinieerd in het bestand `wwwroot/wp-content/themes/martinehooptopbeter/config.php`. Een voorbeeldbestand `config.php` staat in de root folder van het project.

Hieronder zijn alle configuratie opties beschreven welke betrekking hebben op de donaties.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['donate_dsn']` | Ja | *Geen* | De **data source name** voor het verbinden met de MySQL database voor de donaties. Bijvoorbeeld: `mysql:host=localhost;dbname=martinehooptopbeter` |
| `$config['donate_username']` | Ja | *Geen* | De gebruikersnaam waarmee de applicatie moet inloggen op de database server voor de donaties. |
| `$config['donate_password']` | Ja | *Geen* | Het wachtwoord van de gebruikersnaam waarmee de applicatie moet inloggen op de database server voor de donaties. |
| `$config['donate_goal']` | Nee | 7500000 | Het doelbedrag op te halen met donaties (in euro centen). Dit bedrag is optioneel en als het niet is opgegeven wordt alleen het opgehaalde bedrag getoond zonder een doelbedrag en zonder een grafische weergave van hoeveel van het doelbedrag al is opgehaald. |
| `$config['donate_minamount']` | Nee | 500 | Het minimumbedrag van een donatie (in euro centen). Als er geen minimumbedrag is opgegeven dan is dit 0. |
| `$config['donate_maxamount']` | Nee | 211200 | Het maximumbedrag van een donatie (in euro centen). Als er geen maximumbedrag is opgegeven dan geldt er geen maximum. |
| `$config['donate_email_fromaddress']` | Nee | *Geen* | Het e-mailadres waarmee een bevestigingse-mail wordt gestuurd na ontvangst van de donatie. Als er geen e-mailadres is gedefinieerd wordt er geen bevestigingse-mail verstuurd. |
| `$config['donate_email_fromname']` | Nee | *Geen* | De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de donatie. Als er geen naam is gedefinieerd dan zal alleen het e-mailadres worden gebruikt (`$config['donate_email_fromaddress']`). |

Er wordt gebruik gemaakt van online betalingen via [Mollie](https://www.mollie.com/nl/). Hiervoor moet een account worden aangemaakt en een website worden gedefinieerd waarvan de volgende gegevens moeten worden ingevuld:

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['mollie_apikey']` | Ja | *Geen* | Dit is de API key welke door Mollie wordt gegeven voor de aangemaakte website. Mollie geeft per websites twee type key's, een test key en een live key. De test key begint altijd met de letter `test_` en de live key begint altijd met de letter `live_`. Let op dat je de juiste key gebruikt voor testen en voor de live website! |
| `$config['mollie_webhookurl']` | Ja | *Geen* | De webhook URL welke door Mollie zal worden aangeroepen op het moment dat er een statuswijziging is in één van de lopende betalingen. Deze meot verwijzen naar het bestand `mollie-webhook.php` in de WordPress theme folder. Let op dat je hier de volledige URL inclusief domeinnaam en folder vermeld. Als je website gebruik maakt van HTTPS dan is het verstandig om deze webhook ook over HTTPS te laten aanroepen. |

Als je gebruik wilt maken van Google Analytics kan je de Tracking ID configureren welke door Google wordt gegeven.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['googleanalytics_trackingid']` | Nee | *Geen* | De Google Analytics Tracking ID van de website. Als er geen Tracking ID wordt opgegeven wordt er Google Analytics gebruikt. |

Het theme heeft een eenvoudig contactformulier ingebouwd. Met dit formulier kunnen bezoekers eenvoudig en snel een e-mail sturen met een vraag of opmerking. Het contactformulier werkt op iedere pagina welke het **Contact Page** template gebruikt.

| Parameter | Verplicht | Standaardwaarde | Omschrijving |
| --------- | --------- | --------------- | ------------ |
| `$config['contact_sendmailto']` | Nee | *Geen* | Het e-mailadres waar het contact formulier naar toe gestuurd moet worden. Als er geen e-mailadres is geconfiureerd dan zal het contactformulier zijn uitgeschakeld. |

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
