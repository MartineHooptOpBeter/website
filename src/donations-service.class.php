<?php

    @@HEADER@@

	require_once 'payments-service.class.php';

    class DonationsService extends PaymentsService {

        public function __construct($configuration)
        {
            parent::__construct($configuration);
        }

        public function createMolliePaymentForDonation($donation, $returnurl)
        {
            if (!$donation || !($donation instanceof Donation))
                throw new InvalidArgumentException('No or invalid donation!');
            
            $donations = new Donations($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());
            if ($donation = $donations->addDonation($donation)) {
            
                $description = __('Donation', 'martinehooptopbeter');
                $returnurl = sprintf($returnurl, $donation->id, $donation->paymentVerification);

                return $this->createMolliePayment($donation, $description, $returnurl);

            } else {
                $this->lastErrorMessage = __('An error has occured while saving your donation.', 'martinehooptopbeter');
            }
            
            return false;
        }

    }
