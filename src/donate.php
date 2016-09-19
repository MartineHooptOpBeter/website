<?php

    function show_donate_page($page = 1)
    {

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <h2><?php _e('Donate', 'martinehooptopbeter'); ?></h2>

                <form action="" method="post">
                    
                    <p><?php _e('Enter your details and the amount you want to donate below. Optionally you can make your donation anonymously or hide the amount of money you donate.', 'martinehooptopbeter'); ?>
                    <fieldset>
                        <p>
                            <label for="donate_name"><?php _e('Your name', 'martinehooptopbeter'); ?></label>
                            <input type="text" class="textinput" id="donate_name" name="donate_name" value="" />
                        </p>
                        <p>
                            <label for="donate_email"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?></label>
                            <input type="email" class="textinput" id="donate_email" name="donate_email" value="" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
                        <p><?php _e('Do you want to support Martine?  Leave her a message that will show up on the website.', 'martinehooptopbeter'); ?></p>
                        <p>
                            <label for="donate_name"><?php _e('Message', 'martinehooptopbeter'); ?></label>
                            <textarea id="donate_message" name="donate_message" rows="10"></textarea>
                        </p>
                    </fieldset>

                    <p><?php _e('Enter the amount you want to donate and choose your prefered payment method. You can donate immediately online.', 'martinehooptopbeter'); ?>
                    <fieldset>
                        <p>
                            <label for="donate_name"><?php _e('Amount to donate', 'martinehooptopbeter'); ?></label>
                            <span>&euro; </span><input type="text" class="textinput numberinput clearnone" id="donate_email" name="donate_email" value="" placeholder="<?php _e('00.00', 'martinehooptopbeter') ?>"/>
                        </p>
                        <p>
                            <label><?php _e('Payment method', 'martinehooptopbeter'); ?></label>
                            <ul>
                                <li><input type="radio" class="radio" id="donate_payment_method_ideal" name="donate_payment_method" value="ideal"><label for="donate_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?></label></li>
                                <li><input type="radio" class="radio" id="donate_payment_method_creditcard" name="donate_payment_method" value="creditcard"><label for="donate_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?></label></li>
                            </ul>
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_anonymous" value="" /><label for="donate_anonymous"><?php _e('I want to remain anonymous, do not show my name on the website.', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="checkbox" class="checkbox" id="donate_no_amount" value="" /><label for="donate_no_amount"><?php _e('Do not show the amount that I donate on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                    </fieldset>

                    <div class="buttons">
                        <button type="submit" class="btn"><?php _e('Donate', 'martinehooptopbeter'); ?></button>
                    </div>

                </form>

            </div>

        </div>
    </section>

<?php

    }

?>
<?php

    class Donation
    {
        public $id;
        public $amount;
        public $emailAddress;
        public $name;
        public $message;
        public $paymentVerification;
        public $showNoAmount;
        public $showAnonymous;

        function Donation($id, $amount = 0, $emailAddress = '', $name = '', $message = '', $paymentVerification = '', $showNoAmount = false, $showAnonymous = false)
        {
            $this->id = $id;
            $this->amount = $amount;
            $this->emailAddress = $emailAddress;
            $this->name = $name;
            $this->message = $message;
            $this->paymentVerifcation = $paymentVerification;
            $this->showNoAmount = $showNoAmount;
            $this->showAnonymous = $showAnonymous;
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
            $conn = $this->openConnection();

            try {
                $sql = 'INSERT INTO tbl_donations (amount, emailaddress, name, message, payment_verification, show_no_comment, show_anonymous) VALUES(:amount, :emailaddress, :name, :message, :payment_verification, :show_no_amount, :show_anonymous)';
                $query = $conn->prepare($sql);

                $query->bindParam(':amount', $donation->$amount);            
                $query->bindParam(':emailaddress', $donation->$emailaddress);            
                $query->bindParam(':name', $donation->$name);            
                $query->bindParam(':message', $donation->$message);            
                $query->bindParam(':payment_verification', $this->generatePaymentVerification($donation));            
                $query->bindParam(':show_no_comment', $donation->$showNoAmount);            
                $query->bindParam(':show_anonymous', $donation->$showAnonymous);
                $query->execute();

                $result = $donation;
                $result->id = $conn->lastInsertId();
            }
            catch(PDOException $ex)
            { }

            $conn = nothing;
            return $result;
        }

        function getDonation($id) {

            $result = null;

            try {
                $sql = 'SELECT id, amount, emailaddress, name, message, payment_verification, show_no_comment, show_anonymous FROM tbl_donations WHERE (id = :id)';
                $query = $conn->prepare($sql);

                $query->bindParam(':id', $id);            
                $query->execute();

                if ($query->rowCount() > 0) {

                    $result = new Donation($query[0], $query[1], $query[2], $query[3], $query[4], $query[5], $query[6], $query[7]);

                }
            }
            catch(PDOException $ex)
            { }

            return $result;
        }

        function getDonationsList($offset, $numberOfItems, $sortOrder = 'DESC') {

            $result = null;
            $orderBy = ($sortOrder == 'ASC') ? 'ASC' : 'DESC';

            try {
                $sql = 'SELECT id, amount, emailaddress, name, message, payment_verification, show_no_comment, show_anonymous FROM tbl_donations ORDER BY :sortOrder LIMIT :numberOfItems OFFSET :offset';
                $query = $conn->prepare($sql);

                $query->bindParam(':offset', $offset);            
                $query->bindParam(':numberOfItems', $numberOfItems);            
                $query->bindParam(':sortOrder', $sortOrder);            
                $rows = $query->execute();

                if ($query->rowCount() > 0) {

                    $result = [];

                    foreach($rows as $row) {
                        $result[] = new Donation($row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7]);
                    }
                }
            }
            catch(PDOException $ex)
            { }

            return $result;

        }

        public function generatePaymentVerification($donation) {
            $toHash = date('c') . $donation->name . $donation->emailAddress . $email->message;
            return password_hash($toHash, PASSWORD_BCRYPT, array());
        } 

        private function openConnection() {

            $conn = null;

            try {
                $conn = new PDO(
                    $this->dsn,
                    $this->username,
                    $this->password,
                    array(
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    )
                );
            }
            catch(PDOException $ex)
            { }

            return $conn;
        }

    }

?>