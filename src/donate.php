<?php 

    @@HEADER@@
	
	require_once 'vendor/autoload.php';

	require_once 'donations.class.php';
	require_once 'donations-service.class.php';

	require_once 'xsrf.php';
	
	class DonationPage {
	
		protected $_configuration = null;
		protected $_xsrf = null;

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
		public $donate_payment_method_ideal = '';
		public $donate_payment_status = '';
		public $donate_anonymous = false;
		public $donate_no_amount = false;
		public $donate_locale = '';
		
		function DonationPage($configuration) {
			$this->_configuration = $configuration;
			$this->_xsrf = new XSRF();
		}

		function processRequest($donateUrl, $server, $post, $get) {
			
			// We show the form by default, unless we decide otherwise
			$this->doShowDonationForm = true;
			
			if ($server['REQUEST_METHOD'] == "POST") {
				
				$post = stripslashes_deep($post);

				$this->donate_name = isset($post['donate_name']) ? trim($post['donate_name']) : $this->donate_name;
				$this->donate_email = isset($post['donate_email']) ? trim($post['donate_email']) : $this->donate_email;
				$this->donate_message = isset($post['donate_message']) ? trim($post['donate_message']) : $this->donate_message;
				$this->donate_amount = isset($post['donate_amount']) ? trim($post['donate_amount']) : $this->donate_amount;
				$this->donate_payment_method = isset($post['donate_payment_method']) ? trim($post['donate_payment_method']) : $this->donate_payment_method;
				$this->donate_payment_method_ideal = isset($post['donate_payment_method_ideal']) ? trim($post['donate_payment_method_ideal']) : $this->donate_payment_method_ideal;
				$this->donate_anonymous = isset($post['donate_anonymous']);
				$this->donate_no_amount = isset($post['donate_no_amount']);
				$noSubmit = isset($post['donate_nosubmit']);

				if ((empty($this->donate_name)) && !$this->donate_anonymous) {
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
				elseif ($this->donate_amount_decimal < $this->_configuration->getDonationMinimumAmount()) {
					$this->missingfields['donate_amount'] = vsprintf(__('Minimum required amount is %1$s', 'martinehooptopbeter'), Donation::formatEuroPrice($this->_configuration->getDonationMinimumAmount()));
				}
				elseif ($this->donate_amount_decimal > $this->_configuration->getDonationMaximumAmount()) {
					$this->missingfields['donate_amount'] = vsprintf(__('Maximum amount is %1$s', 'martinehooptopbeter'), Donation::formatEuroPrice($this->_configuration->getDonationMaximumAmount()));
				}

				if (($this->donate_payment_method != 'ideal') && ($this->donate_payment_method != 'creditcard')) {
					$this->missingfields['donate_payment_method'] = __('Required field', 'martinehooptopbeter');
				} elseif (($this->donate_payment_method == 'ideal') && empty($this->donate_payment_method_ideal)) {
					$this->missingfields['donate_payment_method_ideal'] = __('Required field', 'martinehooptopbeter');
				}

				$token = isset($post[$this->_xsrf->getSessionKey()]) ? trim($post[$this->_xsrf->getSessionKey()]) : '';
				if (!$this->_xsrf->verifyToken($token)) {
					$this->missingfields[$this->_xsrf->getSessionKey()] = __('The posted data could not be validated. Please try again.', 'martinehooptopbeter'); 
				}

				if ((count($this->missingfields) == 0) && (!$noSubmit)) {

					$donationsService = new DonationsService($this->_configuration);

					$donation = new Donation(0, $this->donate_amount_decimal, $this->donate_email, $this->donate_name, $this->donate_message, '', $this->donate_payment_method, null, null, $this->donate_no_amount, $this->donate_anonymous, $this->_configuration->getCurrentLocale(), null);
					$returnurl = $donateUrl . '?donationid=%1$s&verification=%2$s';

					if ($redirecturl = $donationsService->createMolliePaymentForDonation($donation, $this->donate_payment_method_ideal, $returnurl)) {

                        header("Location: " . $redirecturl);
                        exit;

					} else {
						$this->errorMessage = $donationsService->lastErrorMessage;
					}

				}

			} else {
				
				$donationId = isset($get['donationid']) ? trim($get['donationid']) : 0;
				$paymentVerification = isset($get['verification']) ? trim($get['verification']) : 0;

				if ($donationId && $paymentVerification) {

					$donations = new Donations($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());
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
						$this->donate_locale = $donation->locale;
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
			$donationsService = new DonationsService($this->_configuration);
			$idealissuers = $donationsService->getIdealIssuersWithStatus();

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <form action="" method="post">
                    
                    <p><?php _e('Enter your details and the amount you want to donate below. Optionally you can make your donation anonymously or hide the amount you donate.', 'martinehooptopbeter'); ?>

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
						<p><?php _e('Your e-mail address will never be shown on the website.', 'martinehooptopbeter'); ?></p>
						<p><?php _e('Enter the amount you want to donate and choose your prefered payment method. You can donate immediately online.', 'martinehooptopbeter'); ?>
                        <p class="<?php if (isset($this->missingfields['donate_amount'])) { echo 'error'; } ?>">
                            <label for="donate_amount"><?php _e('Amount to donate', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_amount'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_amount'])); ?>)</em><?php endif; ?></label>
                            <span>&euro; </span><input type="text" class="textinput numberinput clearnone" id="donate_amount" name="donate_amount" value="<?php echo esc_attr($this->donate_amount); ?>" placeholder="<?php _e('00.00', 'martinehooptopbeter') ?>"/>
                        </p>
						<?php if (isset($this->missingfields['donate_amount']) && ($this->donate_amount_decimal > $this->_configuration->getDonationMaximumAmount())) : ?>
							<p class="error"><?php echo vsprintf(esc_attr(__('Unfortunately we can\'t accept donations higher than %1$s due to tax regulations. If you want to donate more, please %2$s for possibilities.', 'martinehooptopbeter')), array(esc_attr(Donation::formatEuroPrice($this->_configuration->getDonationMaximumAmount())), '<a href="/contact/">' . esc_attr(__('contact us', 'martinehooptopbeter')) . '</a>')); ?></p>
						<?php endif; ?>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_no_amount" name="donate_no_amount"<?php if ($this->donate_no_amount) { echo ' checked="checked"'; } ?> /><label for="donate_no_amount"><?php _e('Do not show the amount that I donate on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p class="<?php if (isset($this->missingfields['donate_payment_method']) || isset($this->missingfields['donate_payment_method_ideal'])) { echo 'error'; } ?>">
							<label><?php _e('Payment method', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_payment_method'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_payment_method'])); ?>)</em><?php elseif (isset($this->missingfields['donate_payment_method_ideal'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_payment_method_ideal'])); ?>)</em><?php endif; ?></label>
                        </p>
                        <ul class="paymentmethods <?php if (isset($this->missingfields['donate_payment_method']) || isset($this->missingfields['donate_payment_method_ideal'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="donate_payment_method_ideal" name="donate_payment_method" value="ideal"<?php if ($this->donate_payment_method == 'ideal') { echo ' checked="checked"'; } ?> onchange="onPaymentMethodChanged(this)"><label for="donate_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?><img src="<?php echo esc_attr(get_bloginfo('template_url') . '/img/ideal.svg'); ?>" alt="<?php _e('iDEAL', 'martinehooptopbeter'); ?>" /></label>
							<?php if ($idealissuers) : ?>
							<div id="payment_method_ideal_options" style="display: <?php echo ($this->donate_payment_method == 'ideal') ? "block" : "none"; ?>">
								<label for="donate_payment_method_ideal_options_bank"><?php _e('Choose your bank', 'martinehooptopbeter'); ?></label>
								<select name="donate_payment_method_ideal" onchange="onIdealIssuerChanged(this)">
									<option value=""> - </option>
									<?php foreach($idealissuers as $idealissuer) : ?>
										<?php $isSelected = ($this->donate_payment_method_ideal == $idealissuer['id']); ?>
										<?php $isSelectedShowWarning = $isSelected && $idealissuer['showwarning']; ?>
										<option <?php if ($isSelected) { echo ' selected="selected"'; } ?>value="<?php echo esc_attr($idealissuer['id']); ?>" data-show-warning='<?php echo $idealissuer['showwarning'] ? '1' : '0'; ?>'><?php echo esc_attr($idealissuer['name']); ?></option>
									<?php endforeach; ?>
								</select>
								<div id="payment_method_ideal_warning" style="display: <?php echo ($isSelectedShowWarning) ? "block" : "none"; ?>">
									<p><?php _e('There have been known problems with iDEAL transaction with this bank during the last hour. If you experience any problem please try again later.', 'martinehooptopbeter'); ?></p>									
								</div>
							</div><?php endif; ?>
							</li>
                            <li><input type="radio" class="radio" id="donate_payment_method_creditcard" name="donate_payment_method" value="creditcard"<?php if ($this->donate_payment_method == 'creditcard') { echo ' checked="checked"'; } ?> onchange="onPaymentMethodChanged(this)"><label for="donate_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?><img src="<?php echo esc_attr(get_bloginfo('template_url') . '/img/creditcards.svg'); ?>" alt="<?php _e('Credit Card', 'martinehooptopbeter'); ?>" /></label></li>
                        </ul>
                        <p><?php _e('Do you want to support Martine?  Leave her a message that will show up on the website.', 'martinehooptopbeter'); ?></p>
                        <p class="<?php if (isset($this->missingfields['donate_message'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Message', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['donate_message'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['donate_message'])); ?>)</em><?php endif; ?></label>
                            <textarea id="donate_message" name="donate_message" rows="10"><?php echo esc_attr($this->donate_message); ?></textarea>
                        </p>
                    </fieldset>

					<input type="hidden" name="<?php echo esc_attr($this->_xsrf->getSessionKey()); ?>" value="<?php echo esc_attr($this->_xsrf->generateToken()); ?>" />
                    <?php if (isset($this->missingfields[$this->_xsrf->getSessionKey()])) : ?>
						<p class="error"><?php echo esc_attr($this->missingfields[$this->_xsrf->getSessionKey()]); ?></p>
					<?php endif; ?>

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
			
			$showRefresh = false;
			$showDonateAgain = false;
			
?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
			
				<?php if ($this->errorMessage) : ?>
					<p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
				<?php else : ?>
			
					<?php if (($this->donate_payment_status == PaymentStatus::Paid) || ($this->donate_payment_status == PaymentStatus::PaidOut)) :  ?>
					
						<h2><?php _e('Thank You', 'martinehooptopbeter'); ?></h2>
						
						<p><?php echo esc_attr(vsprintf(__('Your donation of %1$s has been received. We thank you for supporting Martine!', 'martinehooptopbeter'), Donation::formatEuroPrice($this->donate_amount_decimal))); ?></p>
					
						<div class="buttons">
							<a href="<?php _e('/donations/', 'martinehooptopbeter'); ?>" class="btn"><?php _e('Continue', 'martinehooptopbeter'); ?></a>
						</div>
						
						<?php if ($this->_configuration->getGoogleAnalyticsTrackingId() !== null) : ?>
						<script>

							ga('require', 'ecommerce');
							ga('ecommerce:addTransaction', {
								'id': '<?php echo $this->donate_id; ?>',
								'revenue': '<?php echo Donation::formatDecimal($this->donate_amount_decimal); ?>',
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
								<input type="hidden" name="<?php echo esc_attr($this->_xsrf->getSessionKey()); ?>" value="<?php echo esc_attr($this->_xsrf->generateToken()); ?>" />
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