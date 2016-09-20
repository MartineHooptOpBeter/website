<?php require_once 'config.php' ?><?php

    class Donation
    {
        public $id;
        public $amount;
        public $emailAddress;
        public $name;
        public $message;
        public $paymentVerification;
		public $paymentMethod;
		public $paymentId;
		public $paymentStatus;
        public $showNoAmount;
        public $showAnonymous;
		public $timestamp;

        function Donation($id, $amount = 0, $emailAddress = '', $name = '', $message = '', $paymentVerification = '', $paymentMethod = '', $paymentId = null, $paymentStatus = null, $showNoAmount = false, $showAnonymous = false, $timestamp = null)
        {
            $this->id = $id;
            $this->amount = $amount;
            $this->emailAddress = $emailAddress;
            $this->name = $name;
            $this->message = $message;
            $this->paymentVerification = $paymentVerification;
			$this->paymentMethod = $paymentMethod;
			$this->paymentId = $paymentId;
			$this->paymentStatus = $paymentStatus;
            $this->showNoAmount = $showNoAmount;
            $this->showAnonymous = $showAnonymous;
			$this->timestamp = $timestamp;
        }
    }

    class Donations
    {
        private $dsn;
        private $username;
        private $password;

        function Donations($dsn, $username, $password = '')
        {
            $this->dsn = $dsn;
            $this->username = $username;
            $this->password = $password;
        }

        function addDonation($donation) {

            $result = null;
            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'INSERT INTO tbl_donations (amount, emailaddress, name, message, payment_verification, payment_method, payment_id, payment_status, show_no_amount, show_anonymous, timestamp) VALUES (:amount, :emailaddress, :name, :message, :payment_verification, :payment_method, :payment_id, :payment_status, :show_no_amount, :show_anonymous, NOW())';
                    $query = $conn->prepare($sql);
                    if ($query != null) {

                        $donation->paymentVerification = $this->generatePaymentVerification($donation);

                        $query->bindValue(':amount', $donation->amount);
                        $query->bindValue(':emailaddress', $donation->emailAddress);
                        $query->bindValue(':name', $donation->name);            
                        $query->bindValue(':message', $donation->message);            
                        $query->bindValue(':payment_verification', $donation->paymentVerification);
                        $query->bindValue(':payment_method', $donation->paymentMethod);
                        $query->bindValue(':payment_id', $donation->paymentId);
                        $query->bindValue(':payment_status', $donation->paymentStatus);
                        $query->bindValue(':show_no_amount', $donation->showNoAmount ? '1' : '0');
                        $query->bindValue(':show_anonymous', $donation->showAnonymous ? '1' : '0');
                        if ($query->execute()) {
							$newId = $conn->lastInsertId();
                            $result = $this->getDonation($newId);
                        }

                    }
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;
        }

        function getDonation($id) {

            $result = null;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT id, amount, emailaddress, name, message, payment_verification, payment_method, payment_id, payment_status, show_no_amount, show_anonymous, timestamp FROM tbl_donations WHERE (id = :id)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':id', $id);            
                    if ($query->execute()) {
							
						if ($row = $query->fetch(PDO::FETCH_NUM)) {

							$result = new Donation($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], strtotime($row[11]));
							
						}

					}
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;
        }

        function getDonationsList($offset, $numberOfItems, $sortOrder = 'DESC') {

            $result = null;
            $orderBy = ($sortOrder == 'ASC') ? 'ASC' : 'DESC';

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT id, amount, emailaddress, name, message, payment_verification, payment_method, payment_id, payment_status, show_no_amount, show_anonymous, timestamp FROM tbl_donations WHERE (payment_status = \'paid\') ORDER BY timestamp ' . $orderBy . ' LIMIT :numberOfItems OFFSET :offset';
                    $query = $conn->prepare($sql);
					
                    $query->bindValue(':offset', $offset);            
                    $query->bindValue(':numberOfItems', $numberOfItems);            
                    if ($query->execute()) {
						if ($query->rowCount() > 0) {

							$result = [];
							
							while ($row = $query->fetch(PDO::FETCH_NUM)) {
								$result[] = new Donation($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], strtotime($row[11]));
							}

						}
                    }
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;

        }
		
		function getDonationsListCount() {

            $result = null;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'SELECT COUNT(*) as total_donations FROM tbl_donations WHERE (payment_status = \'paid\')';
                    $query = $conn->prepare($sql);
                    if ($query->execute()) {
						if ($row = $query->fetch(PDO::FETCH_NUM)) {

							$result = intval($row[0]);

						}
                    }
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;
		
		}
		
		function updatePaymentId($id, $payment_id) {
			
            $result = false;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'UPDATE tbl_donations SET payment_id = :payment_id WHERE (id = :id)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':payment_id', $payment_id);            
                    $query->bindValue(':id', $id);            
                    $result = $query->execute();
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;
			
		}

		function updatePaymentStatus($id, $payment_id, $payment_verification, $payment_status) {
			
            $result = false;

            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'UPDATE tbl_donations SET payment_status = :payment_status WHERE (id = :id) AND (payment_id = :payment_id) AND (payment_verification = :payment_verification)';
                    $query = $conn->prepare($sql);

                    $query->bindValue(':payment_status', $payment_status);            
					$query->bindValue(':id', $id);
                    $query->bindValue(':payment_id', $payment_id);            
					$query->bindValue(':payment_verification', $payment_verification);            
                    $result = $query->execute();
                }
                catch(PDOException $ex)
                { }

            }

            $conn = null;
            return $result;
			
		}

        public function generatePaymentVerification($donation) {
            return bin2hex(random_bytes(32));
        } 

        private function openConnection() {

            $conn = null;

            try {
                $conn = new PDO($this->dsn, $this->username, $this->password);
                $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $ex)
            { }

            return $conn;
        }

    }

?>