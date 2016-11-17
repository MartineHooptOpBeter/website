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
			
			if ($mollie_payment = $mollie->payments->get($transaction_id)) {

				$mollie_payment_id = $mollie_payment->id;

				$payment_id = isset($mollie_payment->metadata->payment_id) ? $mollie_payment->metadata->payment_id : null;
				if ($payment_id == null) {
					// We used donation_id previously, so for backwards compatibility we keep this
					$payment_id = isset($mollie_payment->metadata->donation_id) ? $mollie_payment->metadata->donation_id : null;
				}

				$payment_verification = isset($mollie_payment->metadata->payment_verification) ? $mollie_payment->metadata->payment_verification : null;
				
				if (($payment_id != null) && ($payment_verification != null)) {

					$payments = new Payments($configuration->getDonationsDatabaseDataSourceName(), $configuration->getDonationsDatabaseUsername(), $configuration->getDonationsDatabasePassword());
					if (!$payments->updatePaymentStatus($payment_id, $payment_verification, $mollie_payment_id, $mollie_payment->status)) {
						header('HTTP/1.1 403 Forbidden');
						exit;
					}

					if ($mollie_payment->isPaid()) {
						if ($payment = $payments->getPayment($payment_id, $payment_verification)) {

							$configuration->overrideLocale($payment->locale);

							switch($payment->getType())
							{
								case PaymentTypes::Donation:
									sendDonationConformationEMail($payment, $configuration);
									break;
							}
						
						}
					}

					header('HTTP/1.1 200 OK');
					exit;
				}
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

	function sendDonationConformationEMail($payment, $configuration)
	{
		if ($payment == null)
			return false;

		if ($configuration == null)
			return null;

		if (!$donation = Donation::withPayment($payment))
			return null;

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

			return true;
		}
	}
