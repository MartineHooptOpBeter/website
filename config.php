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

	
    // ======
    // MOLLIE
    // ======

    /* De API key voor Mollie */
    $config['mollie_apikey'] = '';

	/* De webhookr URL voor Mollie */
	$config['mollie_webhookurl'] = '';

?>