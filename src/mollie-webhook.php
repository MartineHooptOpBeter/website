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