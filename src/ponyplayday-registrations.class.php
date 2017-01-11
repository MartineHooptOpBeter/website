<?php

    @@HEADER@@

    require_once 'payments.class.php';
    require_once 'utilities.class.php';

    class PonyPlayDayRegistration extends Payment
    {
        public $emailAddress;
        public $nameChild;
        public $age;
        public $experienceLevel;
        public $eventDateTime;

        function __construct($id, $amount = 0, $emailAddress = '', $nameChild = '', $age = 0, $experienceLevel = '', $eventDateTime = '', $paymentVerification = '', $paymentMethod = '', $paymentId = null, $paymentStatus = null, $locale = '', $timestamp = null)
        {
            parent::__construct($id, PaymentTypes::PonyPlayDayRegistration, $amount, $emailAddress, $paymentVerification, $paymentMethod, $paymentId, $paymentStatus, $locale, '', $timestamp);

            $this->nameChild = $nameChild;
            $this->age = $age;
            $this->experienceLevel = $experienceLevel;
            $this->eventDateTime = $eventDateTime;
        }

        public static function withPayment($payment)
        {
            $instance = new self($payment->id, $payment->amount, $payment->getUserId(), '', 0, '', '', $payment->paymentVerification, $payment->paymentMethod, $payment->paymentId, $payment->paymentStatus, $payment->locale, $payment->timestamp);
            $instance->setData($payment->getData());
            $instance->deserializeData();
            return $instance;
        }

        public function getType()
        {
            return PaymentTypes::PonyPlayDayRegistration;
        }

        public function getUserId()
        {
            return $this->emailAddress;
        }

        protected function setData($data)
        {
            $this->_data = $data;
        }

        public function setUserId($value)
        {
            $this->emailAddress = $value;
            $this->userid = $value; 
        }

        public function serializeData()
        {
            $this->_data = json_encode(array(
                'namechild' => utf8_encode($this->nameChild),
                'age' => $this->age,
                'experiencelevel' => $this->experienceLevel,
                'eventdatetime' => $this->eventDateTime
            ));
        }

        public function deserializeData()
        {
            if (!$this->_data || !is_string($this->_data))
                return;

            $array = json_decode($this->_data, true);

            if (!$array || !is_array($array))
                return;

            if (isset($array['namechild'])) { $this->nameChild = utf8_decode($array['namechild']); }
            if (isset($array['age'])) { $this->age = $array['age']; }
            if (isset($array['experiencelevel'])) { $this->experienceLevel = $array['experiencelevel']; }
            if (isset($array['eventdatetime'])) { $this->eventDateTime = $array['eventdatetime']; }
        }
		
		public static function validEMailAddress($emailaddress)
        {
			return preg_match('/^([0-9a-zA-Z_]([-.\w\+]*[0-9a-zA-Z_])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,})$/', $emailaddress);
		}

    }

    class PonyPlayDayRegistrations extends Payments
    {
        public function __construct($dsn, $username, $password = '')
        {
            parent::__construct($dsn, $username, $password);
        }

        public function addPonyPlayDayRegistration($registration)
        {
            $registration->serializeData();
            return $this->addPayment($registration);
        }

        public function getPonyPlayDayRegistration($id, $verification)
        {
            if (!$payment = $this->getPayment($id, $verification))
                return null;

            if (!$registration = PonyPlayDayRegistration::withPayment($payment))
                return null; 

            return $registration;
        }

        public function getPonyPlayDayRegistrationsList($page = 1, $itemsPerPage = 10, $sortOrder = 'DESC')
        {
            $registrations = [];

            if (!$payments = $this->getPaymentsList(PaymentTypes::PonyPlayDayRegistration, PaymentStatus::Paid_Or_PaidOut, $page, $itemsPerPage, $sortOrder))
                return null;

            foreach($payments as $payment) {
                if ($registration = PonyPlayDayRegistration::withPayment($payment))
                    $registrations[] = $registration;
            }

            return $registrations;
        }
		
		public function getPonyPlayDayRegistrationsListCount()
        {
            return $this->getPaymentsListCount(PaymentTypes::PonyPlayDayRegistration, PaymentStatus::Paid_Or_PaidOut);
		}
		
		public function getTotalPonyPlayDayRegistrationsAmount()
        {
            return $this->getTotalPaymentsAmount(PaymentTypes::PonyPlayDayRegistration, PaymentStatus::Paid_Or_PaidOut);
        }

    }
