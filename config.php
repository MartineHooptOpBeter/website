<?php

    // ====================================================================
    // Voorbeeld configuratiebestand
    // Kopieer dit bestand naar de theme folder en pas de configuratie aan.
    // ====================================================================

    $config  = [];


    // ========
    // DONATIES
    // ========

    /* Database data source name */
    $config['donate_dsn'] = '';

    /* Gebruikersnaam om in te loggen op database server */
    $config['donate_username'] = '';

    /* Wachtwoord om in te loggen op database server */
    $config['donate_password'] = '';
	
	/* Het doelbedrag op te halen met donaties (in euro centen) */
	$config['donate_goal'] = 1000000;

	/* Het minimumbedrag van een donatie (in euro centen) */
	$config['donate_minamount'] = 500;
	
	/* Het maximumbedrag van een donatie (in euro centen) */
	$config['donate_maxamount'] = 212200;

    /* Het e-mailadres waarmee een bevestiging wordt gestuurd na ontvangst van de donatie */
    $config['donate_email_fromaddress'] = [
        ['locale' => 'en_US', 'emailaddress' => 'somebody@domain.com'],
        ['locale' => 'nl_NL', 'emailaddress' => 'iemand@domein.nl'],
        ['locale' => '*',     'emailaddress' => 'somebody.else@domain.org']
    ];

    /* De afzender van de e-mail waarmee een bevestiging wordt gestuurd na ontvangst van de donatie */
    $config['donate_email_fromname'] = [
        ['locale' => 'en_US', 'name' => 'Somebody'],
        ['locale' => 'nl_NL', 'name' => 'Iemand'],
        ['locale' => '*',     'name' => 'Somebody Else']
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
        ['locale' => 'en_US', 'trackingid' => 'track_en_US'],
        ['locale' => 'nl_NL', 'trackingid' => 'track_nl_NL'],
        ['locale' => '*',     'trackingid' => 'track_other']
    ];


    // =======
    // CONTACT
    // =======

    /* Het e-mailadres waar het contact formulier naar toe gestuurd moet worden */
    $config['contact_sendmailto'] = [
        ['locale' => 'en_US', 'emailaddress' => 'somebody@domain.com'],
        ['locale' => 'nl_NL', 'emailaddress' => 'iemand@domein.nl'],
        ['locale' => '*',     'emailaddress' => 'somebody.else@domain.org']
    ];

?>