<?php

    // ====================================================================
    // Voorbeeld configuratiebestand
    // Kopieer dit bestand naar de theme folder en pas de configuratie aan.
    // ====================================================================

    $config  = [];


    // ==========
    // BETALINGEN
    // ==========

    /* Database data source name */
    $config['payments_dsn'] = '';

    /* Gebruikersnaam om in te loggen op database server */
    $config['payments_username'] = '';

    /* Wachtwoord om in te loggen op database server */
    $config['payments_password'] = '';


    // ========
    // DONATIES
    // ========
	
	/* Het doelbedrag op te halen met donaties (in euro centen) */
	$config['donate_goal'] = 1000000;

	/* De startdatum van de actie */
	$config['donate_startdate'] = mktime(0, 0, 0, 12, 31, 2015);

	/* Het minimumbedrag van een donatie (in euro centen) */
	$config['donate_minamount'] = 500;
	
	/* Het maximumbedrag van een donatie (in euro centen) */
	$config['donate_maxamount'] = 212200;

    /* Het e-mailadres waarmee een bevestiging wordt gestuurd na ontvangst van de donatie */
    $config['donate_email_fromaddress'] = [
        ['locale' => 'en_US', 'value' => 'somebody@domain.com'],
        ['locale' => 'nl_NL', 'value' => 'iemand@domein.nl'],
        ['locale' => '*',     'value' => 'somebody.else@domain.org']
    ];

    /* De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de donatie */
    $config['donate_email_fromname'] = [
        ['locale' => 'en_US', 'value' => 'Somebody'],
        ['locale' => 'nl_NL', 'value' => 'Iemand'],
        ['locale' => '*',     'value' => 'Somebody Else']
    ];


    // ============
    // PONYSPEELDAG
    // ============
	
	/* De prijs van een registratie voor de ponyspeeldag (in euro centen) */
	$config['ponyplayday_price'] = 1750;

    /* Een string of een (multidimensional) array met de start- en einddatum / tijd waarop de ponyspeeldag wordt gehouden */
    $config['ponyplayday_events'] = '2036/1/19';

	/* De minimum leeftijd om te kunnen inschrijven */
	$config['ponyplayday_minage'] = 6;

	/* De maximum leeftijd om te kunnen inschrijven */
	$config['ponyplayday_maxage'] = 12;

    /* Het e-mailadres waarmee een bevestiging wordt gestuurd na ontvangst van de inschrijving */
    $config['ponyplayday_email_fromaddress'] = [
        ['locale' => 'en_US', 'value' => 'somebody@domain.com'],
        ['locale' => 'nl_NL', 'value' => 'iemand@domein.nl'],
        ['locale' => '*',     'value' => 'somebody.else@domain.org']
    ];

    /* De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de inschrijving */
    $config['ponyplayday_email_fromname'] = [
        ['locale' => 'en_US', 'value' => 'Somebody'],
        ['locale' => 'nl_NL', 'value' => 'Iemand'],
        ['locale' => '*',     'value' => 'Somebody Else']
    ];


    // ======
    // MOLLIE
    // ======

    /* De API key voor Mollie */
    $config['mollie_apikey'] = '';

	/* De webhook URL voor Mollie */
	$config['mollie_webhookurl'] = '';


    // ================
    // GOOGLE ANALYTICS
    // ================

	/* Google Analytics tracking ID */
	$config['googleanalytics_trackingid'] = [
        ['locale' => 'en_US', 'value' => 'track_en_US'],
        ['locale' => 'nl_NL', 'value' => 'track_nl_NL'],
        ['locale' => '*',     'value' => 'track_other']
    ];


    // =====================
    // GOOGLE SEARCH CONSOLE
    // =====================

	/* Google Search Console site verification */
	$config['googlesearch_siteverification'] = [
        ['locale' => 'en_US', 'value' => 'verification_en_US'],
        ['locale' => 'nl_NL', 'value' => 'verification_nl_NL'],
        ['locale' => '*',     'value' => 'verification_other']
    ];


    // =======
    // CONTACT
    // =======

    /* Het e-mailadres waar het contact formulier naar toe gestuurd moet worden */
    $config['contact_sendmailto'] = [
        ['locale' => 'en_US', 'value' => 'somebody@domain.com'],
        ['locale' => 'nl_NL', 'value' => 'iemand@domein.nl'],
        ['locale' => '*',     'value' => 'somebody.else@domain.org']
    ];

?>