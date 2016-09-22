<?php require_once 'donations-class.php' ?><?php require_once 'Mollie/API/Autoloader.php'; ?><?php

	class DonationPage {
	
        public $doShowDonationForm = false;
        public $doShowDonationConfirmation = false;
		
		public $errorMessage = null;
		public $missingfields = [];
		
		public $donate_name = '';
		public $donate_email = '';
		public $donate_message = '';
		public $donate_amount = '';
		public $donate_payment_method = '';
		public $donate_anonymous = false;
		public $donate_no_amount = false;
		
		function DonationPage() {
		}
		
		function processRequest($server, $post, $get) {
			
			global $config;

			// We show the form by default, unless we decide otherwise
			$this->doShowDonationForm = true;
			
			if ($server['REQUEST_METHOD'] == "POST") {

				$this->donate_name = isset($post['donate_name']) ? trim($post['donate_name']) : $this->donate_name;
				$this->donate_email = isset($post['donate_email']) ? trim($post['donate_email']) : $this->donate_email;
				$this->donate_message = isset($post['donate_message']) ? trim($post['donate_message']) : $this->donate_message;
				$this->donate_amount = isset($post['donate_amount']) ? trim($post['donate_amount']) : $this->donate_amount;
				$this->donate_payment_method = isset($post['donate_payment_method']) ? trim($post['donate_payment_method']) : $this->donate_payment_method;
				$this->donate_anonymous = isset($post['donate_anonymous']);
				$this->donate_no_amount = isset($post['donate_no_amount']);

				if (empty($this->donate_name)) {
					$this->missingfields['donate_name'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->donate_email)) {
					$this->missingfields['donate_email'] = __('Required field', 'martinehooptopbeter');
				}

				$donate_amount_parsed = $this->parseAmount($this->donate_amount);
				if ($donate_amount_parsed <= 0) {
					$this->missingfields['donate_amount'] = __('Invalid amount', 'martinehooptopbeter');
				}
				if ($donate_amount_parsed < 500) {
					$this->missingfields['donate_amount'] = __('Minimum required amount is 5 Euro', 'martinehooptopbeter');
				}

				if (($this->donate_payment_method != 'ideal') && ($this->donate_payment_method != 'creditcard')) {
					$this->missingfields['donate_payment_method'] = __('Required field', 'martinehooptopbeter');
				}

				if (count($this->missingfields) == 0) {

					$d = new Donation(0, $donate_amount_parsed, $this->donate_email, $this->donate_name, $this->donate_message, '', $this->donate_payment_method, null, null, $this->donate_no_amount, $this->donate_anonymous, null);

					$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
					if ($donation = $donations->addDonation($d)) {
						
						$mollie_options = get_option('mollie_options');
						
						$mollie = new Mollie_API_Client;
						$mollie->setApiKey($mollie_options['apikey']);
						
						try
						{
							if ($payment = $mollie->payments->create(
									array(
										'amount'      => number_format($donation->amount / 100, 2, '.', ''),
										'description' => __('Donation', 'martinehooptopbeter'),
										'redirectUrl' => get_permalink() . '?donationid=' . $donation->id . '&verification=' . $donation->paymentVerification,
										'locale'      => 'nl',
										'method'      => $donation->paymentMethod,
										'metadata'    => array(
											'donation_id' => $donation->id,
											'payment_verification' => $donation->paymentVerification
										)
									)
								)) {
								
								if ($donations->updatePaymentId($donation->id, $payment->id)) {
									header("Location: " . $payment->getPaymentUrl());
									exit;
								}
							}
							
							$this->errorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
						}
						catch (Mollie_API_Exception $e)
						{
							var_dump($e);
							$this->errorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
						}

					} else {
						$this->errorMessage = __('An error has occured while saving your donation.', 'martinehooptopbeter');
					}

				}

			} else {
				
				$donationId = isset($get['donationid']) ? trim($get['donateid']) : 0;
				$paymentVerification = isset($get['verification']) ? trim($get['verification']) : 0;
				
				if ($donationId && $paymentVerification) {
					
					$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
					if ($donation = $donations->getDonation($donationID, $paymentVerification)) {
						
						$this->donate_name = $donation->name;
						$this->donate_email = $donation->emailAddress;
						$this->donate_message = $donation->message;
						$this->donate_amount = $donation->amount;
						$this->donate_payment_method = $donation->paymentMethod;
						$this->donate_status = $donation->paymentStatus;
						$this->donate_anonymous = $donation->showAnonymous;
						$this->donate_no_amount = $donation->showNoAmount;
						
					} else {
						$this->errorMessage = __('The specified donation could not be found.', 'martinehooptopbeter');
					}
				
					$this->doShowDonationForm = false;
					$this->doShowDonationConfirmation = true;
					
				}
				
			}
			
		}
		
		function showDonationForm()
		{

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <h2><?php _e('Donate', 'martinehooptopbeter'); ?></h2>

                <form action="" method="post">
                    
                    <p><?php _e('Enter your details and the amount you want to donate below. Optionally you can make your donation anonymously or hide the amount of money you donate.', 'martinehooptopbeter'); ?>

                    <?php if (count($this->missingfields) > 0) : ?>
                        <p class="error"><?php _e('One or more fields are not filled in or incorrect. Please check and correct the entered data.', 'martinehooptopbeter'); ?>
                    <?php endif; ?>
                    <?php if ($this->errorMessage) : ?>
                        <p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
                    <?php endif; ?>

                    <fieldset>
                        <p class="<?php if (isset($this->missingfields['donate_name'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Your name', 'martinehooptopbeter'); ?></label>
                            <input type="text" class="textinput" id="donate_name" name="donate_name" value="<?php echo esc_attr($this->donate_name); ?>" />
                        </p>
                        <p class="<?php if (isset($this->missingfields['donate_email'])) { echo 'error'; } ?>">
                            <label for="donate_email"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?></label>
                            <input type="email" class="textinput" id="donate_email" name="donate_email" value="<?php echo esc_attr($this->donate_email); ?>" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_anonymous" name="donate_anonymous"<?php if ($this->donate_anonymous) { echo ' checked="checked"'; } ?> /><label for="donate_anonymous"><?php _e('I want to remain anonymous, do not show my name on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p><?php _e('Do you want to support Martine?  Leave her a message that will show up on the website.', 'martinehooptopbeter'); ?></p>
                        <p class="<?php if (isset($this->missingfields['donate_message'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Message', 'martinehooptopbeter'); ?></label>
                            <textarea id="donate_message" name="donate_message" rows="10"><?php echo esc_attr($this->donate_message); ?></textarea>
                        </p>
                    </fieldset>

                    <p><?php _e('Enter the amount you want to donate and choose your prefered payment method. You can donate immediately online.', 'martinehooptopbeter'); ?>
                    <fieldset>
                        <p class="<?php if (isset($this->missingfields['donate_amount'])) { echo 'error'; } ?>">
                            <label for="donate_amount"><?php _e('Amount to donate', 'martinehooptopbeter'); ?></label>
                            <span>&euro; </span><input type="text" class="textinput numberinput clearnone" id="donate_amount" name="donate_amount" value="<?php echo esc_attr($this->donate_amount); ?>" placeholder="<?php _e('00.00', 'martinehooptopbeter') ?>"/>
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_no_amount" name="donate_no_amount"<?php if ($this->donate_no_amount) { echo ' checked="checked"'; } ?> /><label for="donate_no_amount"><?php _e('Do not show the amount that I donate on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p class="<?php if (isset($this->missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <label><?php _e('Payment method', 'martinehooptopbeter'); ?></label>
                        </p>
                        <ul class="<?php if (isset($this->missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="donate_payment_method_ideal" name="donate_payment_method" value="ideal"<?php if ($this->donate_payment_method == 'ideal') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="radio" class="radio" id="donate_payment_method_creditcard" name="donate_payment_method" value="creditcard"<?php if ($this->donate_payment_method == 'creditcard') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?></label></li>
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

		private function parseAmount($amount) {
			$amount = str_replace(',', '.', $amount);
			if (is_numeric($amount)) {
				$amound_parsed = floatval($amount);
				return (int)($amound_parsed * 100);
			}
			return 0;
		}
		
	}

?>