<?php

    @@HEADER@@

	require_once 'configuration.php';
	require_once 'donations.class.php';
	require_once 'Mollie/API/Autoloader.php';

	mb_internal_encoding('UTF-8');
	
	$configuration = new Configuration();
	
	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($configuration->getMollieApiKey());

		try {
			$transaction_id = isset($_POST["id"]) ? $_POST["id"] : '';
			
			if ($payment = $mollie->payments->get($transaction_id)) {

				$donation_id = $payment->metadata->donation_id;
				$payment_id = $payment->id;
				$payment_verification = $payment->metadata->payment_verification;
				
				$donations = new Donations($configuration->getDonationsDatabaseDataSourceName(), $configuration->getDonationsDatabaseUsername(), $configuration->getDonationsDatabasePassword());
				if (!$donations->updatePaymentStatus($donation_id, $payment_verification, $payment_id, $payment->status)) {
					header('HTTP/1.1 403 Forbidden');
					exit;
				}

				if ($payment->isPaid()) {
					if ($donation = $donations->getDonation($donation_id, $payment_verification)) {

						$configuration->overrideLocale($donation->locale);
					
						// This comment is here to force this page to be saved in UTF-8 because the next line will need to display a € sign correctly.
						switch ($donation->locale) {

							case 'nl_NL':
								$message = vsprintf('Hallo %1$s,' . "\r\n\r\n" . 'Bedankt voor je donatie van %2$s aan de Stichting Martine Hoopt Op Beter. We hebben de donatie ontvangen.' . "\r\n\r\n" . 'We zouden het leuk vinden als je de website regelmatig blijft bezoeken voor updates over de behandeling van Martine: <https://www.martinehooptopbeter.nl/>' . "\r\n\r\n" . 'Bedankt voor je hulp, ook namens Martine!' . "\r\n\r\n", array($donation->name, Donation::formatEuroPrice($donation->amount)));
								$subject = "Bevestiging van jouw donatie aan de Stichting Martine Hoopt Op Beter";
								break;

							default:
								$message = vsprintf('Hello %1$s,' . "\r\n\r\n" . 'Thank you for your donation of %2$s to the Martine Hoping For Better Foundation. We received the donation.' . "\r\n\r\n". 'We\'d love it if you continue to visit the website regularly for updates on the treatment of Martine: <https://www.martinehopingforbetter.com/>' . "\r\n\r\n" . 'Thanks for your help, also on behalf of Martine!' . "\r\n\r\n", array($donation->name, Donation::formatEuroPrice($donation->amount)));
								$subject = "Confirmation of your donation to the Martine Hoping For Better Foundation";
								break;
						}

						$from_emailaddress = $configuration->getDonationConfirmationFromEmailAddress();
						$from_emailname = $configuration->getDonationConfirmationFromName();

						if ($from_emailaddress) {

							if ($from_emailname) {
								$headers[] = 'From: ' . mb_encode_mimeheader($from_emailname) . ' <' . $from_emailaddress . '>';
							} else {
								$headers[] = 'From: ' . mb_encode_mimeheader($from_emailaddress);
							}

							mb_send_mail($donation->emailAddress, $subject, $message, implode("\r\n", $headers));
						}
					
					}
				}

				header('HTTP/1.1 200 OK');
				exit;
			}
			
			header('HTTP/1.1 404 Not Found');
			exit;
		}
		catch (Mollie_API_Exception $e)
		{
			header('HTTP/1.1 500 Internal Server Error');
			exit;
		}
	
	}

?>