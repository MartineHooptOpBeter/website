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
	
	/* Het doel bedrag op te halen met donaties (in euro centen) */
	$config['donate_goal'] = 1000000;

	/* Het minimum bedrag van een donatie (in euro centen) */
	$config['donate_minamount'] = 500;
	
	/* Het maximum bedrag van een donatie (in euro centen) */
	$config['donate_maxamount'] = 212200;


    // ======
    // MOLLIE
    // ======

    /* De API key voor Mollie */
    $config['mollie_apikey'] = '';

	/* De webhookr URL voor Mollie */
	$config['mollie_webhookurl'] = '';


    // ================
    // GOOGLE ANALYTICS
    // ================

	/* Google Analytics tracking ID */
	$config['googleanalytics_trackingid'] = '';


    // =======
    // CONTACT
    // =======

    /* Het e-mailadres waar het contact formulier naar toe gestuurd moet worden */
    $config['contact_sendmailto'] = '';

?>