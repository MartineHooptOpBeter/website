<?php

    @@HEADER@@

    require_once 'payments.class.php';

    class Donation extends Payment
    {
        public $emailAddress;
        public $name;
        public $message;
        public $showNoAmount;
        public $showAnonymous;

        function __construct($id, $amount = 0, $emailAddress = '', $name = '', $message = '', $paymentVerification = '', $paymentMethod = '', $paymentId = null, $paymentStatus = null, $showNoAmount = false, $showAnonymous = false, $locale = '', $timestamp = null)
        {
            parent::__construct($id, PaymentTypes::Donation, $amount, $emailAddress, $paymentVerification, $paymentMethod, $paymentId, $paymentStatus, $locale, '', $timestamp);

            $this->name = $name;
            $this->message = $message;
            $this->showNoAmount = $showNoAmount;
            $this->showAnonymous = $showAnonymous;
        }

        public static function withPayment($payment)
        {
            $instance = new self($payment->id, $payment->amount, $payment->getUserId(), '', '', $payment->paymentVerification, $payment->paymentMethod, $payment->paymentId, $payment->paymentStatus, false, false, $payment->locale, $payment->timestamp);
            $instance->setData($payment->getData());
            $instance->deserializeData();
            return $instance;
        }

        public function getType()
        {
            return PaymentTypes::Donation;
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
                'name' => utf8_encode($this->name),
                'message' => utf8_encode($this->message),
                'showNoAmount' => $this->showNoAmount,
                'showAnonymous' => $this->showAnonymous
            ));
        }

        public function deserializeData()
        {
            if (!$this->_data || !is_string($this->_data))
                return;

            $array = json_decode($this->_data, true);

            if (!$array || !is_array($array))
                return;

            if (isset($array['name'])) { $this->name = utf8_decode($array['name']); }
            if (isset($array['message'])) { $this->message = utf8_decode($array['message']); }
            if (isset($array['showNoAmount'])) { $this->showNoAmount = $array['showNoAmount']; }
            if (isset($array['showAnonymous'])) { $this->showAnonymous = $array['showAnonymous']; }
        }
		
		public static function validEMailAddress($emailaddress)
        {
			return preg_match('/^([0-9a-zA-Z_]([-.\w\+]*[0-9a-zA-Z_])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,})$/', $emailaddress);
		}
    }

    class Donations extends Payments
    {
        public function __construct($dsn, $username, $password = '')
        {
            parent::__construct($dsn, $username, $password);
        }

        public function addDonation($donation)
        {
            $donation->serializeData();
            return $this->addPayment($donation);
        }

        public function getDonation($id, $verification)
        {
            if (!$payment = $this->getPayment($id, $verification))
                return null;

            if (!$donation = Donation::withPayment($payment))
                return null; 

            return $donation;
        }

        public function getDonationsList($page = 1, $itemsPerPage = 10, $sortOrder = 'DESC')
        {
            $donations = [];

            if (!$payments = $this->getPaymentsList(PaymentTypes::Donation, 'paid', $page, $itemsPerPage, $sortOrder))
                return null;

            foreach($payments as $payment) {
                if ($donation = Donation::withPayment($payment))
                    $donations[] = $donation;
            }

            return $donations;
        }
		
		public function getDonationsListCount()
        {
            return $this->getPaymentsListCount(PaymentTypes::Donation, 'paid');
		}
		
		public function getTotalDonationsAmount()
        {
            return $this->getTotalPaymentsAmount(PaymentTypes::Donation, 'paid');
        }

		public function getPercentageOfGoal($current, $goal, $max)
        {
			$goalPercentage = 0;
			
			if ($goal > 0) {
				
				$goalPercentage = ((float)$current / (float)$goal) * 100;
					
				if ($goalPercentage > $max)
					$goalPercentage = $max;
			}

			return $goalPercentage;
		}

    }
