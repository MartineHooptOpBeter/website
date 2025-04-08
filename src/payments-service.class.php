<?php

    @@HEADER@@

	require_once 'vendor/autoload.php';

    require_once 'idealstatus.class.php';

    class PaymentsService {

        public $lastErrorMessage = ''; 

        public function __construct($configuration)
        {
            $this->_configuration = $configuration;
        }

        public function createMolliePayment($payment, $idealissuer, $description, $returnurl)
        {

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->_configuration->getMollieApiKey());

            try
            {
                $options = array(
                    'amount'      => array(
                        'value' => Payment::formatDecimal($payment->amount),
                        'currency' => 'EUR'
                    ),
                    'description' => $description,
                    'redirectUrl' => $returnurl,
                    'webhookUrl'  => $this->_configuration->getMollieWebhookUrl(),
                    'locale'      => $payment->locale,
                    'method'      => $payment->paymentMethod,
                    'metadata'    => array(
                        'payment_id' => $payment->id,
                        'payment_verification' => $payment->paymentVerification
                    )
                );

                if ($payment->paymentMethod == 'ideal') {
                    $options['issuer'] = $idealissuer;
                }

                if ($mollie_payment = $mollie->payments->create($options)) {
                    $payments = new Payments($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());

                    if ($payments->updatePaymentId($payment->id, $payment->paymentVerification, $mollie_payment->id)) {
                        return $mollie_payment->_links->checkout->href;
                    }
                }

                $this->lastErrorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
            }
            catch (\Mollie\Api\Exceptions\ApiException $e)
            {
                $this->lastErrorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
            }

            return false;
        }

        public function getIdealIssuersWithStatus()
        {
            $ideal = [];

            $idealstatus = new IdealStatus();

            $mollie = new \Mollie\Api\MollieApiClient();
            $mollie->setApiKey($this->_configuration->getMollieApiKey());

            $idealPaymentMethod = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL, ["include" => "issuers"]);

            foreach ($idealPaymentMethod->issuers as $issuer)
            {
                $ideal[] = array('id' => $issuer->id, 'name' => $issuer->name, 'showwarning' => !$idealstatus->statusForIssuer($issuer->id));
            }

            return $ideal;
        }

    }
