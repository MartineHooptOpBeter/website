<?php

    @@HEADER@@

	require_once 'vendor/autoload.php';

    require_once 'idealstatus.class.php';

    use phpFastCache\CacheManager;

    class PaymentsService {

        public $lastErrorMessage = ''; 

        public function __construct($configuration)
        {
            $this->_configuration = $configuration;
        }

        public function createMolliePayment($payment, $idealissuer, $description, $returnurl)
        {

            $mollie = new Mollie_API_Client;
            $mollie->setApiKey($this->_configuration->getMollieApiKey());

            try
            {
                $options = array(
                    'amount'      => Payment::formatDecimal($payment->amount),
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
                        return $mollie_payment->getPaymentUrl();
                    }
                }

                $this->lastErrorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
            }
            catch (Mollie_API_Exception $e)
            {
                $this->lastErrorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
            }

            return false;
        }

        public function getIdealIssuersWithStatus()
        {
            $cache = CacheManager::Apcu();

            $cachedItem = $cache->getItem("martinehooptopbeter_idealissuerswithstatus");

            if (!$cachedItem->isHit()) {

                $ideal = [];

                $idealstatus = new IdealStatus();

                $mollie = new Mollie_API_Client;
                $mollie->setApiKey($this->_configuration->getMollieApiKey());

                $issuers = $mollie->issuers->all();

                foreach ($issuers as $issuer)
                {
                    if ($issuer->method == Mollie_API_Object_Method::IDEAL)
                    {
                        $ideal[] = array('id' => $issuer->id, 'name' => $issuer->name, 'showwarning' => !$idealstatus->statusForIssuer($issuer->id));
                    }
                }

                $cachedItem->set($ideal);
                $cachedItem->expiresAfter(15 * 60);
                $cache->save($cachedItem);

            } else {
                $ideal = $cachedItem->get();
            }

            return $ideal;
        }

    }
