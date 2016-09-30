<?php require_once 'donations-class.php' ?><?php require_once 'Mollie/API/Autoloader.php'; ?><?php

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

							$subject = __('Confirmation of your donation to Stichting Martine Hoopt Op Beter', 'martinehooptopbeter');
							if ($from_emailname) {
								$headers = 'From: ' . $from_emailname . ' <' . $from_emailaddress . '>';
							} else {
								$headers = 'From: ' . $from_emailaddress;
							}

							$message = vsprintf(__("Hello %1$s\n\nThank you for your donation of %2$s to the Stichting Martine Hoopt Op Beter. We have received your donation.\n\nPlease visit the website regularly for updates about the treatment of Martine: <https://www.martinehoooptopbeter.nl/>", 'martinehooptopbeter'), array($donation->name, formatEuroPrice($donation->amount)));
							
							mail($donation->emailaddress, $subject, $message, $headers);
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

	function formatPrice($amount) {
		return number_format((float)$amount / 100, 2, ',', '.');
	}
	
	function formatEuroPrice($amount) {
		return '€ ' . $this->formatPrice($amount);
	}

?>