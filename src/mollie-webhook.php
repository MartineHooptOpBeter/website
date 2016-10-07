<?php require_once 'donations-class.php' ?><?php require_once 'Mollie/API/Autoloader.php'; ?><?php

	mb_internal_encoding('UTF-8');
	
    global $config;

	if ($_SERVER['REQUEST_METHOD'] == "POST") {
		
		$mollie = new Mollie_API_Client;
		$mollie->setApiKey($config['mollie_apikey']);

		try {
			$transaction_id = isset($_POST["id"]) ? $_POST["id"] : '';
			
			if ($payment = $mollie->payments->get($transaction_id)) {

				$donation_id = $payment->metadata->donation_id;
				$payment_id = $payment->id;
				$payment_verification = $payment->metadata->payment_verification;
				
				$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
				if (!$donations->updatePaymentStatus($donation_id, $payment_id, $payment_verification, $payment->status)) {
					header('HTTP/1.1 403 Forbidden');
					exit;
				}

				if ($payment->isPaid()) {
					if ($donation = $donations->getDonation($donation_id, $payment_verification)) {

						$from_emailaddress = $config['donate_email_fromaddress'];
						$from_emailname = $config['donate_email_fromname'];

						if ($from_emailaddress) {

							$subject = "Bevestiging van jouw donatie aan de Stichting Martine Hoopt Op Beter";
							if ($from_emailname) {
								$headers[] = 'From: ' . mb_encode_mimeheader($from_emailname) . ' <' . $from_emailaddress . '>';
							} else {
								$headers[] = 'From: ' . mb_encode_mimeheader($from_emailaddress);
							}

							// This comment is here to force this page to be saved in UTF-8 because the next line will need to display a € sign correctly.
							$message = vsprintf('Hallo %1$s,' . "\n\n" . 'Bedankt voor je donatie van %2$s aan de Stichting Martine Hoopt Op Beter. We hebben de donatie ontvangen.' . "\n\n" . 'We zouden het leuk vinden als je de website regelmatig blijft bezoeken voor updates over de behandeling van Martine: <https://www.martinehooptopbeter.nl/>' . "\n\n" . 'Bedankt voor je hulp, ook namens Martine!' . "\n\n", array($donation->name, Donation::formatEuroPrice($donation->amount)));

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