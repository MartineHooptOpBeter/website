<?php

    @@HEADER@@

    class PaymentTypes
    {
        const NotDefined = 0;
        const Donation = 1;
        const PonyPlayDayRegistration = 2;
    }

    class Payment
    {
        public $id;
        public $amount;
        public $paymentVerification;
		public $paymentMethod;
		public $paymentId;
		public $paymentStatus;
		public $locale;
		public $timestamp;

        protected $_type = PaymentTypes::NotDefined;
        protected $_userId;
        protected $_data;

        public function __construct($id, $type, $amount = 0, $userId = '', $paymentVerification = '', $paymentMethod = '', $paymentId = null, $paymentStatus = null, $locale = '', $data = '', $timestamp = null)
        {
            $this->id = $id;
            $this->_type = $type;
            $this->amount = $amount;
            $this->setUserId($userId);
            $this->paymentVerification = $paymentVerification;
			$this->paymentMethod = $paymentMethod;
			$this->paymentId = $paymentId;
			$this->paymentStatus = $paymentStatus;
			$this->locale = $locale;
            $this->_data = $data;
			$this->timestamp = $timestamp;
        }

        public function getType()
        {
            return $this->_type;
        }

        public function getUserId()
        {
            return $this->_userid;
        }

        public function setUserId($value)
        {
            $this->_userid = $value;
        }

        public function getData()
        {
            return $this->_data;
        }

		public static function parseAmount($amount) {
			$amount = str_replace(',', '.', $amount);
			if (is_numeric($amount)) {
				$amount_parsed = floatval($amount);
				return (int)($amount_parsed * 100);
			}
			return 0;
		}

        public static function parseInteger($int) {
            if (is_numeric($int)) {
                return (int)$int;
            }
            return 0;
        }

		public static function formatDecimal($amount) {
			if (!is_numeric($amount))
				$amount = 0.0;
			return number_format((float)$amount / 100, 2, '.', '');
		}
		
		public static function formatPrice($amount) {
			$decimal_point = '.';
			$thousand_sep = ',';

			if (($li = localeconv()) && is_array($li)) {
				$decimal_point = $li['mon_decimal_point'];
				$thousand_sep = $li['mon_thousands_sep'];
			}
			return number_format((float)$amount / 100, 2, $decimal_point, $thousand_sep);
		}
		
		public static function formatEuroPrice($amount) {
			$currencySymbol = '€';
			$isNegative = ($amount < 0);
			$symbolInFront = true;
			$spaceBeforeSymbol = true;
			
			if (($li = localeconv()) && is_array($li)) {
				$symbolInFront = $isNegative ? isset($li['n_cs_precedes']) : isset($li['p_cs_precedes']);
				$spaceBeforeSymbol = $isNegative ? isset($li['n_sep_by_space']) : isset($li['p_sep_by_space']);
			}

			return ($symbolInFront ? $currencySymbol . ($spaceBeforeSymbol ? ' ' : '') : '') . Payment::formatPrice($amount) . (!$symbolInFront ? ($spaceBeforeSymbol ? ' ' : '') . $currencySymbol : '');
		}
    }

    class Payments
    {
        private $dsn;
        private $username;
        private $password;

        public function __construct($dsn, $username, $password = '')
        {
            $this->dsn = $dsn;
            $this->username = $username;
            $this->password = $password;
        }

        public function addPayment($payment)
        {
            $result = null;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'INSERT INTO tbl_payments (type, amount, userid, payment_verification, payment_method, payment_id, payment_status, locale, data, timestamp) VALUES (:type, :amount, :userid, :payment_verification, :payment_method, :payment_id, :payment_status, :locale, :data, NOW())';
                    $query = $conn->prepare($sql);
                    if ($query != null) {

                        $payment->paymentVerification = $this->generatePaymentVerification();

                        $query->bindValue(':type', $payment->getType());
                        $query->bindValue(':amount', $payment->amount);
                        $query->bindValue(':userid', $payment->getUserId());
                        $query->bindValue(':payment_verification', $payment->paymentVerification);
                        $query->bindValue(':payment_method', $payment->paymentMethod);
                        $query->bindValue(':payment_id', $payment->paymentId);
                        $query->bindValue(':payment_status', $payment->paymentStatus);
                        $query->bindValue(':data', $payment->getData());
						$query->bindValue(':locale', $payment->locale);
                        if ($query->execute()) {
							$newId = $conn->lastInsertId();
                            $result = $this->getPayment($newId, $payment->paymentVerification);
                        }

                    }
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
        }

        public function getPayment($id, $verification) {

            $result = null;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT id, type, amount, userid, payment_verification, payment_method, payment_id, payment_status, locale, data, timestamp FROM tbl_payments WHERE (id = :id) AND (payment_verification = :payment_verification)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':id', $id);            
                    $query->bindValue(':payment_verification', $verification);            
					
                    if ($query->execute()) {
							
						if ($row = $query->fetch(PDO::FETCH_NUM)) {

							$result = new Payment($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], strtotime($row[10]));
							
						}

					}
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
        }
        
        public function getPaymentsList($type, $paymentStatus = 'paid', $page = 1, $itemsPerPage = 10, $sortOrder = 'DESC') {

            $result = null;

            $type = $this->validatePaymentType($type);
            $paymentStatus = $this->validatePaymentStatus($paymentStatus);

            $page = $this->validatePageNumber($page, null);
            $itemsPerPage = $this->validatePageSize($itemsPerPage);            

            $offset = ($page - 1) * $itemsPerPage;

            $orderBy = ($sortOrder == 'ASC') ? 'ASC' : 'DESC';

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT id, type, amount, userid, payment_method, payment_status, locale, data, timestamp FROM tbl_payments WHERE (type = :type) AND (payment_status = :payment_status) ORDER BY timestamp ' . $orderBy . ' LIMIT :numberOfItems OFFSET :offset';
                    $query = $conn->prepare($sql);
					
                    $query->bindValue(':type', $type);            
                    $query->bindValue(':payment_status', $paymentStatus);            
                    $query->bindValue(':offset', $offset);            
                    $query->bindValue(':numberOfItems', $itemsPerPage);            
                    if ($query->execute()) {
						if ($query->rowCount() > 0) {

							$result = [];
							
							while ($row = $query->fetch(PDO::FETCH_NUM)) {
								$result[] = new Payment($row[0], $row[1], $row[2], $row[3], '***', $row[4], '***', $row[5],  $row[6], $row[7], strtotime($row[8]));
							}

						}
                    }
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
        }

		public function getPaymentsListCount($type, $paymentStatus = 'paid')
        {
            $result = null;

            $type = $this->validatePaymentType($type);
            $paymentStatus = $this->validatePaymentStatus($paymentStatus);            

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT COUNT(*) as total_payments FROM tbl_payments WHERE (type = :type) AND (payment_status = :payment_status)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':type', $type);            
                    $query->bindValue(':payment_status', $paymentStatus);            
                    if ($query->execute()) {
						if ($row = $query->fetch(PDO::FETCH_NUM)) {

							$result = intval($row[0]);

						}
                    }
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
		}

		public function getTotalPaymentsAmount($type, $paymentStatus = 'paid')
        {
            $result = null;

            $type = $this->validatePaymentType($type);
            $paymentStatus = $this->validatePaymentStatus($paymentStatus);            

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT SUM(amount) as total_amount FROM tbl_payments WHERE (type = :type) AND (payment_status = :payment_status)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':type', $type);            
                    $query->bindValue(':payment_status', $paymentStatus);            
                    if ($query->execute()) {
						if ($row = $query->fetch(PDO::FETCH_NUM)) {

							$result = intval($row[0]);

						}
                    }
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
		}

		public function updatePaymentId($id, $verification, $payment_id)
        {
            $result = false;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'UPDATE tbl_payments SET payment_id = :payment_id WHERE (id = :id) AND (payment_verification = :payment_verification)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':payment_id', $payment_id);
                    $query->bindValue(':id', $id);
					$query->bindValue(':payment_verification', $verification);            
                    $result = $query->execute();
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
		}

		public function updatePaymentStatus($id, $verification, $payment_id, $payment_status)
        {
            $result = false;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'UPDATE tbl_payments SET payment_status = :payment_status WHERE (id = :id) AND (payment_verification = :payment_verification) AND (payment_id = :payment_id)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':payment_status', $payment_status);            
					$query->bindValue(':id', $id);
                    $query->bindValue(':payment_id', $payment_id);            
					$query->bindValue(':payment_verification', $verification);            
                    $result = $query->execute();
                }
                catch(PDOException $ex)
                { }

                unset($conn);
            }

            return $result;
		}

        public function generatePaymentVerification() {
            return bin2hex(random_bytes(32));
        } 

        private function openConnection() {

            $conn = null;

            try {
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
                );
                $conn = new PDO($this->dsn, $this->username, $this->password, $options);
                $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $ex)
            { }

            return $conn;
        }

        private function validatePaymentType($type)
        {
            if (!type || !is_numeric($type))
                $type = PaymentTypes::NotDefined;

            return $type;
        }

        private function validatePaymentStatus($paymentStatus)
        {
            if (!$paymentStatus || !is_string($paymentStatus))
                $paymentStatus = 'paid';

            return $paymentStatus;
        }

        public function validatePageNumber($page, $maxPage)
        {
            $page = intval($page);

            if (!$page || !is_numeric($page) || ($page < 1))
                $page = 1;

            if (($maxPage != null) && ($maxPage > 0)) {
    		    return $page > $maxPage ? $maxPage : $page;
            } else {
                return $page;
            }
        }

        public function getMaximumPageNumber($itemCount, $pageSize)
        {
            $pageSize = $this->validatePageSize($pageSize);

            if (!$itemCount || !is_numeric($itemCount) || ($itemCount < 1))
                return null;
            
		    return intval(($itemCount - 1) / $pageSize) + 1;
        }

        public function validatePageSize($itemsPerPage)
        {
            if (!$itemsPerPage || !is_numeric($itemsPerPage) || ($itemsPerPage < 1))
                $itemsPerPage = 10;

            return $itemsPerPage;
        }

    }
