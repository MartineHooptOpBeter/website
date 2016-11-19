<?php

    @@HEADER@@

    class PaymentsService {

        public $lastErrorMessage = ''; 

        public function __construct($configuration)
        {
            $this->_configuration = $configuration;
        }

        public function createMolliePayment($payment, $description, $returnurl)
        {

            $mollie = new Mollie_API_Client;
            $mollie->setApiKey($this->_configuration->getMollieApiKey());

            try
            {
                if ($mollie_payment = $mollie->payments->create(
                        array(
                            'amount'      => Donation::formatDecimal($payment->amount),
                            'description' => $description,
                            'redirectUrl' => $returnurl,
                            'webhookUrl'  => $this->_configuration->getMollieWebhookUrl(),
                            'locale'      => $payment->locale,
                            'method'      => $payment->paymentMethod,
                            'metadata'    => array(
                                'payment_id' => $payment->id,
                                'payment_verification' => $payment->paymentVerification
                            )
                        )
                    ))
                {
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

    }
