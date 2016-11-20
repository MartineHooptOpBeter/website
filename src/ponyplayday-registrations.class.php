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

        public static function getArrayWithEventsDateTimeOpenForRegistration($eventsdatetime)
        {
            if (!$eventsdatetime || !is_array($eventsdatetime) || (count($eventsdatetime) < 1))
                return false;

            $result = [];

            // If we get a non-multi-dimensional array, we make it multi-dimensional
            if (!is_array($eventsdatetime[0]))
                $eventsdatetime = array($eventsdatetime);


            foreach($eventsdatetime as $eventdatetime) {

                $startdatetime = isset($eventdatetime['startdatetime']) ? $eventdatetime['startdatetime'] : null;
                $enddatetime = isset($eventdatetime['enddatetime']) ? $eventdatetime['enddatetime'] : null;
                $closedays = isset($eventdatetime['closedays']) ? $eventdatetime['closedays'] : 0;
                
                if (self::isRegistrationOpenForDateTime($startdatetime, $closedays)) {

                    $result[] = array(
                        'eventdatetime' => self::getEventDateTimeString($startdatetime),
                        'eventdatetimestring' => self::getEventDateTimeSpan($startdatetime, $enddatetime),
                        'startdatetime' => $startdatetime,
                        'enddatetime' => $enddatetime,
                    ); 

                }
            }

            return $result;
        }

        public static function getEventDateTimeSpan($startdate, $enddate)
        {
            if (!$startdate)
                return false;

            if ($enddate) {
                if (date('Ymd', $startdate) != date('Ymd', $enddate)) {
                    return Utilities::formatShortDateTime($startdate, __('%1$s, %2$s', 'martinehooptopbeter'), get_locale()) . ' - ' . Utilities::formatShortDateTime($enddate, __('%1$s, %2$s', 'martinehooptopbeter'), get_locale());
                } else {
                    return Utilities::formatShortDateTime($startdate, __('%1$s, %2$s', 'martinehooptopbeter'), get_locale()) . ' - ' . Utilities::formatShortTime($enddate, get_locale());
                }  
            } else {
                return Utilities::formatShortDateTime($startdate, get_locale());
            }
        }

        public static function isValidEventDateTime($datetime, $eventsdatetime)
        {
            if (!$datetime || !is_string($datetime))
                return false;

            if (!$eventsdatetime || !is_array($eventsdatetime) || (count($eventsdatetime) < 1))
                return false;

            // If we get a non-multi-dimensional array, we make it multi-dimensional
            if (!is_array($eventsdatetime[0]))
                $eventsdatetime = array($eventsdatetime);

            foreach($eventsdatetime as $eventdatetime) {

                $startdatetime = isset($eventdatetime['startdatetime']) ? $eventdatetime['startdatetime'] : null;
                $closedays = isset($eventdatetime['closedays']) ? $eventdatetime['closedays'] : 0;
                
                if (self::isRegistrationOpenForDateTime($startdatetime, $closedays)) {

                    if ($datetime == self::getEventDateTimeString($startdatetime)) {
                        return true;
                    }

                }
            }

            return false;
        }

        public static function isRegistrationOpenForDateTime($startdatetime, $closedays) {
            if ($startdatetime) {
                if (time() < ($startdatetime - ($closedays * 24 * 60 * 60))) {
                    return true;
                }
            }
            return false;
        }

        public static function getEventDateTimeString($datetime) {
            return date('Ymd-His', $datetime);
        }

        public static function parseEventDateTimeString($datetimestring)
        {
            if (preg_match('/(\d{8})-(\d{6})/', $datetimestring, $matches)) {
                return strtotime(substr($matches[1], 0, 4) . '/' . substr($matches[1], 4, 2) . '/' . substr($matches[1], 6, 2) . ' ' . substr($matches[2], 0, 2) . ':' . substr($matches[2], 2, 2) . ':' . substr($matches[2], 4, 2));
            }

            return false;
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

            if (!$payments = $this->getPaymentsList(PaymentTypes::PonyPlayDayRegistration, 'paid', $page, $itemsPerPage, $sortOrder))
                return null;

            foreach($payments as $payment) {
                if ($registration = PonyPlayDayRegistration::withPayment($payment))
                    $registrations[] = $registration;
            }

            return $registrations;
        }
		
		public function getPonyPlayDayRegistrationsListCount()
        {
            return $this->getPaymentsListCount(PaymentTypes::PonyPlayDayRegistration, 'paid');
		}
		
		public function getTotalPonyPlayDayRegistrationsAmount()
        {
            return $this->getTotalPaymentsAmount(PaymentTypes::PonyPlayDayRegistration, 'paid');
        }

    }
