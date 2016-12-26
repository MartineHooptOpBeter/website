<?php

    @@HEADER@@

	require_once 'payments-service.class.php';
    require_once 'ponyplayday-registrations.class.php';

    class PonyPlayDayRegistrationsService extends PaymentsService {

        public function __construct($configuration)
        {
            parent::__construct($configuration);
        }

        public function createMolliePaymentForPonyPlayDayRegistration($registration, $idealissuer, $returnurl)
        {
            if (!$registration || !($registration instanceof PonyPlayDayRegistration))
                throw new InvalidArgumentException('No or invalid pony play day registration!');
            
            $registrations = new PonyPlayDayRegistrations($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());
            if ($registration = $registrations->addPonyPlayDayRegistration($registration)) {
            
                $description = __('Pony Play Day', 'martinehooptopbeter');
                $returnurl = sprintf($returnurl, $registration->id, $registration->paymentVerification);

                return $this->createMolliePayment($registration, $idealissuer, $description, $returnurl);

            } else {
                $this->lastErrorMessage = __('An error has occured while saving your registration.', 'martinehooptopbeter');
            }
            
            return false;
        }

		public function isRegistrationPossible() {

			$price = PonyPlayDayRegistration::parseAmount($this->_configuration->getPonyPlayDayPrice());
			$eventsdatetime = $this->_configuration->getPonyPlayDayEvents();

			if (!$price || !$eventsdatetime)
				return false;

			if (is_string($eventsdatetime))
				return true;

			if (!is_array($eventsdatetime))
				return false;

            return true;
        }

        public function isRegistrationStillOpen() {

            if (!$this->isRegistrationPossible())
                return false;

			$price = PonyPlayDayRegistration::parseAmount($this->_configuration->getPonyPlayDayPrice());
			$eventsdatetime = $this->_configuration->getPonyPlayDayEvents();

            // If we get a non-multi-dimensional array, we make it multi-dimensional
            if (!is_array($eventsdatetime[0]))
                $eventsdatetime = array($eventsdatetime);

            foreach($eventsdatetime as $eventdatetime) {

                $startdatetime = isset($eventdatetime['startdatetime']) ? $eventdatetime['startdatetime'] : null;
                $closedays = isset($eventdatetime['closedays']) ? $eventdatetime['closedays'] : 0;

                if (self::isRegistrationOpenForDateTime($startdatetime, $closedays)) {
                	return true;
                }
            }

			return false;
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
