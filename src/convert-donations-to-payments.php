<?php

    @@HEADER@@

    if (php_sapi_name() !== 'cli') {
        echo 'Dit script kan alleen vanaf de commandline worden uitgevoerd.';
        exit();
    }

	mb_internal_encoding('UTF-8');

    require_once 'configuration.php';
    require_once 'donations.class.php';
    require_once 'payments.class.php';

    class DonationsToPayments
    {
        private $dsn;
        private $username;
        private $password;

        function __construct($dsn, $username, $password = '')
        {
            $this->dsn = $dsn;
            $this->username = $username;
            $this->password = $password;
        }

        public function convert()
        {
            if (!$allDonations = $this->getAllDonations())
                return;

            foreach($allDonations as $donation) {
                if (!$this->addDonation($donation))
                {
                    echo sprintf('Donatie met ID %1$s kan niet worden geconverteerd!' . "\n", $donation->id);
                } else {
                    echo sprintf('Donatie met ID %1$s is geconverteerd' . "\n", $donation->id);
                }
            }

        }

        private function getAllDonations()
        {
            $result = null;

            if ($conn = $this->openConnection('latin1')) {

                try {
                    $sql = 'SELECT id, amount, emailaddress, name, message, payment_verification, payment_method, payment_id, payment_status, show_no_amount, show_anonymous, locale, timestamp FROM tbl_donations ORDER BY id ASC';
                    $query = $conn->prepare($sql);
					
                    if ($query->execute()) {
						if ($query->rowCount() > 0) {

							$result = [];
							
							while ($row = $query->fetch(PDO::FETCH_NUM)) {
								$result[] = new Donation($row[0], $row[1], $row[2], mb_convert_encoding($row[3], 'UTF-8', 'CP1252'), mb_convert_encoding($row[4], 'UTF-8', 'CP1252'), $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], strtotime($row[12]));
							}

						}
                    }
                }
                catch(PDOException $ex)
                { 
                    echo $ex;
                }

            }

            $conn = null;
            return $result;
        }

        private function addDonation($donation)
        {
            $donation->serializeData();
            return $this->addPayment($donation);
        }

        private function addPayment($payment)
        {
            $result = null;
            if ($conn = $this->openConnection()) {

                try {
                    $sql = 'INSERT INTO tbl_payments (id, type, amount, userid, payment_verification, payment_method, payment_id, payment_status, locale, data, timestamp) VALUES (:id, :type, :amount, :userid, :payment_verification, :payment_method, :payment_id, :payment_status, :locale, :data, :timestamp)';
                    $query = $conn->prepare($sql);
                    if ($query != null) {

                        $query->bindValue(':id', $payment->id);
                        $query->bindValue(':type', $payment->getType());
                        $query->bindValue(':amount', $payment->amount);
                        $query->bindValue(':userid', $payment->getUserId());
                        $query->bindValue(':payment_verification', $payment->paymentVerification);
                        $query->bindValue(':payment_method', $payment->paymentMethod);
                        $query->bindValue(':payment_id', $payment->paymentId);
                        $query->bindValue(':payment_status', $payment->paymentStatus);
                        $query->bindValue(':data', $payment->getData());
						$query->bindValue(':locale', $payment->locale);
						$query->bindValue(':timestamp', date('Y-m-d H:i:s', $payment->timestamp));
                        if ($query->execute()) {
                            $result = true;
                        }

                    }
                }
                catch(PDOException $ex)
                { 
                    echo $ex;
                }

                unset($conn);
            }

            return $result;
        }
        private function openConnection($charset = 'utf8') {

            $conn = null;

            try {
                $options = array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $charset
                );
                $conn = new PDO($this->dsn, $this->username, $this->password, $options);
                $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            catch(PDOException $ex)
            {
                    echo $ex;
            }

            return $conn;
        }

    }

	$configuration = new Configuration();

    $dtp = new DonationsToPayments($configuration->getDonationsDatabaseDataSourceName(), $configuration->getDonationsDatabaseUsername(), $configuration->getDonationsDatabasePassword());
    $dtp->convert();
    unset($dtp);
