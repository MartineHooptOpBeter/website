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
		public $donate_amount_decimal = 0.0;
		public $donate_payment_method = '';
		public $donate_payment_status = '';
		public $donate_anonymous = false;
		public $donate_no_amount = false;
		
		function DonationPage() {
		}
		
		function processRequest($donateUrl, $server, $post, $get) {
			
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
				$noSubmit = isset($post['donate_nosubmit']);

				if (empty($this->donate_name)) {
					$this->missingfields['donate_name'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->donate_email)) {
					$this->missingfields['donate_email'] = __('Required field', 'martinehooptopbeter');
				}
				elseif (!Donation::validEMailAddress($this->donate_email)) {
					$this->missingfields['donate_email'] = __('Invalid e-mail address', 'martinehooptopbeter');
				}

				$this->donate_amount_decimal = Donation::parseAmount($this->donate_amount);
				if ($this->donate_amount_decimal <= 0) {
					$this->missingfields['donate_amount'] = __('Invalid amount', 'martinehooptopbeter');
				}
				elseif ($this->donate_amount_decimal < $config['donate_minamount']) {
					$this->missingfields['donate_amount'] = vsprintf(__('Minimum required amount is %1$s', 'martinehooptopbeter'), Donation::formatEuroPrice($config['donate_minamount']));
				}
				elseif ($this->donate_amount_decimal > $config['donate_maxamount']) {
					$this->missingfields['donate_amount'] = vsprintf(__('Maximum amount is %1$s', 'martinehooptopbeter'), Donation::formatEuroPrice($config['donate_maxamount']));
				}

				if (($this->donate_payment_method != 'ideal') && ($this->donate_payment_method != 'creditcard')) {
					$this->missingfields['donate_payment_method'] = __('Required field', 'martinehooptopbeter');
				}

				if ((count($this->missingfields) == 0) && (!$noSubmit)) {

					$d = new Donation(0, $this->donate_amount_decimal, $this->donate_email, $this->donate_name, $this->donate_message, '', $this->donate_payment_method, null, null, $this->donate_no_amount, $this->donate_anonymous, null);

					$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
					if ($donation = $donations->addDonation($d)) {
						
						$mollie = new Mollie_API_Client;
						$mollie->setApiKey($config['mollie_apikey']);
						
						try
						{
							if ($payment = $mollie->payments->create(
									array(
										'amount'      => number_format($donation->amount / 100, 2, '.', ''),
										'description' => __('Donation', 'martinehooptopbeter'),
										'redirectUrl' => $donateUrl . '?donationid=' . $donation->id . '&verification=' . $donation->paymentVerification,
										'webhookUrl'  => $config['mollie_webhookurl'],
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
				
				$donationId = isset($get['donationid']) ? trim($get['donationid']) : 0;
				$paymentVerification = isset($get['verification']) ? trim($get['verification']) : 0;

				if ($donationId && $paymentVerification) {

					$donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
					if ($donation = $donations->getDonation($donationId, $paymentVerification)) {

						$this->donate_id = $donation->id;
						$this->donate_name = $donation->name;
						$this->donate_email = $donation->emailAddress;
						$this->donate_message = $donation->message;
						$this->donate_amount_decimal = $donation->amount;
						$this->donate_amount = Donation::formatPrice($this->donate_amount_decimal);
						$this->donate_payment_id = $donation->paymentId;
						$this->donate_payment_method = $donation->paymentMethod;
						$this->donate_payment_status = $donation->paymentStatus;
						$this->donate_payment_verification = $donation->paymentVerification;
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
			global $config;

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
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
                            <label for="donate_name"><?php _e('Your name', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_name'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_name'])); ?>)</em><?php endif; ?></label>
                            <input type="text" class="textinput" id="donate_name" name="donate_name" value="<?php echo esc_attr($this->donate_name); ?>" />
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_anonymous" name="donate_anonymous"<?php if ($this->donate_anonymous) { echo ' checked="checked"'; } ?> /><label for="donate_anonymous"><?php _e('I want to remain anonymous, do not show my name on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p class="<?php if (isset($this->missingfields['donate_email'])) { echo 'error'; } ?>">
                            <label for="donate_email"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_email'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_email'])); ?>)</em><?php endif; ?></label>
                            <input type="email" class="textinput" id="donate_email" name="donate_email" value="<?php echo esc_attr($this->donate_email); ?>" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
						<p><?php _e('Your e-mail address will never be shown on the website, if you do not donate anonymously only your name will be shown.', 'martinehooptopbeter'); ?></p>
						<p><?php _e('Enter the amount you want to donate and choose your prefered payment method. You can donate immediately online.', 'martinehooptopbeter'); ?>
                        <p class="<?php if (isset($this->missingfields['donate_amount'])) { echo 'error'; } ?>">
                            <label for="donate_amount"><?php _e('Amount to donate', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_amount'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_amount'])); ?>)</em><?php endif; ?></label>
                            <span>&euro; </span><input type="text" class="textinput numberinput clearnone" id="donate_amount" name="donate_amount" value="<?php echo esc_attr($this->donate_amount); ?>" placeholder="<?php _e('00.00', 'martinehooptopbeter') ?>"/>
                        </p>
						<?php if (isset($this->missingfields['donate_amount']) && ($this->donate_amount_decimal > $config['donate_maxamount'])) : ?>
							<p class="error"><?php echo vsprintf(esc_attr(__('Unfortunately we can\'t accept donations higher than %1$s due to tax regulations. If you want to donate more, please %2$s for possibilities.', 'martinehooptopbeter')), array(esc_attr(Donation::formatEuroPrice($config['donate_maxamount'])), '<a href="/contact/">' . esc_attr(__('contact us', 'martinehooptopbeter')) . '</a>')); ?></p>
						<?php endif; ?>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_no_amount" name="donate_no_amount"<?php if ($this->donate_no_amount) { echo ' checked="checked"'; } ?> /><label for="donate_no_amount"><?php _e('Do not show the amount that I donate on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p class="<?php if (isset($this->missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <label><?php _e('Payment method', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_payment_method'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_payment_method'])); ?>)</em><?php endif; ?></label>
                        </p>
                        <ul class="<?php if (isset($this->missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="donate_payment_method_ideal" name="donate_payment_method" value="ideal"<?php if ($this->donate_payment_method == 'ideal') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="radio" class="radio" id="donate_payment_method_creditcard" name="donate_payment_method" value="creditcard"<?php if ($this->donate_payment_method == 'creditcard') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p><?php _e('Do you want to support Martine?  Leave her a message that will show up on the website.', 'martinehooptopbeter'); ?></p>
                        <p class="<?php if (isset($this->missingfields['donate_message'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Message', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_message'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_message'])); ?>)</em><?php endif; ?></label>
                            <textarea id="donate_message" name="donate_message" rows="10"><?php echo esc_attr($this->donate_message); ?></textarea>
                        </p>
                    </fieldset>

                    <div class="buttons">
                        <button type="submit" class="btn left"><?php _e('Donate', 'martinehooptopbeter'); ?></button>
                    </div>

                </form>

            </div>

        </div>
    </section>

<?php

		}
		
		function showDonationConfirmation() {
			
			global $config;

			$showRefresh = false;
			$showDonateAgain = false;
			
?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
			
				<?php if ($this->errorMessage) : ?>
					<p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
				<?php else : ?>
			
					<?php if (($this->donate_payment_status == 'paid') || ($this->donate_payment_status == 'paidout')) :  ?>
					
						<h2><?php _e('Thank You', 'martinehooptopbeter'); ?></h2>
						
						<p><?php echo esc_attr(vsprintf(__('Your donation of %1$s has been received. We thank you for supporting Martine!', 'martinehooptopbeter'), Donation::formatEuroPrice($this->donate_amount_decimal))); ?></p>
					
						<div class="buttons">
							<a href="/donaties/" class="btn"><?php _e('Continue', 'martinehooptopbeter'); ?></a>
						</div>
						
						<?php if (isset($config['googleanalytics_trackingid'])) : ?>
						<script>

							ga('require', 'ecommerce');
							ga('ecommerce:addTransaction', {
								'id': '<?php echo $this->donate_id; ?>',
								'revenue': '<?php echo number_format((float)$this->donate_amount_decimal / 100, 2, '.', ''); ?>',
							});
							ga('ecommerce:send');

						</script>
						<?php endif; ?>
						
					<?php elseif (($this->donate_payment_status == 'cancelled') || ($this->donate_payment_status == 'expired') || ($this->donate_payment_status == 'failed')) :  ?>
						<?php $showDonateAgain = true; ?>

						<h2><?php _e('Sorry', 'martinehooptopbeter'); ?></h2>
					
						<p><?php echo esc_attr(vsprintf(__('The payment for you donation has been cancelled, expired or has failed. Unfortunately we did not receive your donation of %1$s. Please press the \'Donate Again\' button to start a new donation.', 'martinehooptopbeter'), Donation::formatEuroPrice($this->donate_amount_decimal))); ?></p>
					
					<?php elseif (($this->donate_payment_status == 'refunded') || ($this->donate_payment_status == 'charged_back')) :  ?>
						<?php $showDonateAgain = true; ?>
						
						<h2><?php _e('Sorry', 'martinehooptopbeter'); ?></h2>
					
						<p><?php echo esc_attr(vsprintf(__('The payment for you donation has been refunded or charged back. Unfortunately we did not receive your donation of %1$s. Please press the \'Donate Again\' button to start a new donation.', 'martinehooptopbeter'), Donation::formatEuroPrice($this->donate_amount_decimal))); ?></p>
					
					<?php else : ?>
						<?php $showRefresh = true; ?>
						<?php $showDonateAgain = true; ?>
						
						<p><?php echo esc_attr(vsprintf(__('We have not received confirmation of your donation of %1$s yet.', 'martinehooptopbeter'), Donation::formatEuroPrice($this->donate_amount_decimal))); ?>
						
						<?php if ($this->donate_payment_method == 'ideal') { _e('Normally an iDEAL payment is processed immediately so maybe something went wrong?', 'martinehooptopbeter'); } ?>
						<?php if ($this->donate_payment_method == 'creditcard') { _e('Because you paid with a creditcard it could take a little bit longer before we receive confirmation of your donation.', 'martinehooptopbeter'); } ?></p>
						
						<p><?php _e('You can press the \'Refresh\' button to reload the page to show the latest status of your donation. In case something went wrong, you can press the \'Donate Again\' button to start a new donation.', 'martinehooptopbeter'); ?></p>
						
					<?php endif; ?>
					
					<?php if ($showRefresh || $showDonateAgain) : ?>
				
						<form action="<?php echo esc_attr(get_permalink()); ?>" method="post">
							<div class="buttons">
							
								<?php if ($showRefresh) : ?>
									<a href="<?php echo esc_attr(get_permalink() . '?donationid=' . $this->donate_id . '&verification=' . $this->donate_payment_verification); ?>" class="btn left"><?php _e('Refresh', 'martinehooptopbeter'); ?></a>
								<?php endif; ?>
								
								<?php if ($showDonateAgain) : ?>
									<button type="submit" class="btn right"><?php _e('Donate Again', 'martinehooptopbeter'); ?></button>
								<?php endif; ?>
								
							</div>
							<?php if ($showDonateAgain) : ?>
								<input type="hidden" name="donate_name" value="<?php echo esc_attr($this->donate_name); ?>" />
								<input type="hidden" name="donate_email" value="<?php echo esc_attr($this->donate_email); ?>" />
								<input type="hidden" name="donate_message" value="<?php echo esc_attr($this->donate_message); ?>" />
								<input type="hidden" name="donate_amount" value="<?php echo esc_attr($this->donate_amount); ?>" />
								<input type="hidden" name="donate_payment_method" value="<?php echo esc_attr($this->donate_payment_method); ?>" />
								<?php if ($this->donate_anonymous) : ?>
									<input type="hidden" name="donate_anonymous" value="ON" />
								<?php endif; ?>
								<?php if ($this->donate_no_amount) : ?>
									<input type="hidden" name="donate_no_amount" value="ON" />
								<?php endif; ?>
								<input type="hidden" name="donate_nosubmit" value="ON" />
							<?php endif; ?>
						</form>

					<?php endif; ?>

				<?php endif; ?>

			</div>

        </div>
    </section>

<?php
			
		}

	}

?>