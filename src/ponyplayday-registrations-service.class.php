<?php

    @@HEADER@@

	require_once 'payments-service.class.php';

    class PonyPlayDayRegistrationsService extends PaymentsService {

        public function __construct($configuration)
        {
            parent::__construct($configuration);
        }

        public function createMolliePaymentForPonyPlayDayRegistration($registration, $returnurl)
        {
            if (!$registration || !($registration instanceof PonyPlayDayRegistration))
                throw new InvalidArgumentException('No or invalid pony play day registration!');
            
            $registrations = new PonyPlayDayRegistrations($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());
            if ($registration = $registrations->addPonyPlayDayRegistration($registration)) {
            
                $description = __('Pony Play Day', 'martinehooptopbeter');
                $returnurl = sprintf($returnurl, $registration->id, $registration->paymentVerification);

                return $this->createMolliePayment($registration, $description, $returnurl);

            } else {
                $this->lastErrorMessage = __('An error has occured while saving your registration.', 'martinehooptopbeter');
            }
            
            return false;
        }

    }
