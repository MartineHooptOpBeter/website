<?php 

    @@HEADER@@
	
	require_once 'ponyplayday-registrations.class.php';
	require_once 'ponyplayday-registrations-service.class.php';
	require_once 'Mollie/API/Autoloader.php';

	require_once 'xsrf.php';
	
	class PonyPlayDayPage {
	
		protected $_configuration = null;
		protected $_xsrf = null;

        public $doShowPonyPlayDayForm = false;
        public $doShowPonyPlayDayConfirmation = false;
		
		public $errorMessage = null;
		public $missingfields = [];
		
		public $registration_emailparent = '';
		public $registration_namechild = '';
		public $registration_age = '';
		public $registration_experiencelevel = '';
		public $registration_eventdatetime = '';
		public $registration_locale = '';
		
		function PonyPlayDayPage($configuration) {
			$this->_configuration = $configuration;
			$this->_xsrf = new XSRF();
		}

		function processRequest($registrationUrl, $server, $post, $get) {
			
			// We show the form by default, unless we decide otherwise
			$this->doShowPonyPlayDayForm = true;
			
			if ($server['REQUEST_METHOD'] == "POST") {
				
				$post = stripslashes_deep($post);

				$this->registration_emailparent = isset($post['registration_emailparent']) ? trim($post['registration_emailparent']) : $this->registration_emailparent;
				$this->registration_namechild = isset($post['registration_namechild']) ? trim($post['registration_namechild']) : $this->registration_namechild;
				$this->registration_age = isset($post['registration_age']) ? trim($post['registration_age']) : $this->registration_age;
				$this->registration_experiencelevel = isset($post['registration_experiencelevel']) ? trim($post['registration_experiencelevel']) : $this->registration_experiencelevel;
				$this->registration_eventdatetime = isset($post['registration_eventdatetime']) ? trim($post['registration_eventdatetime']) : $this->registration_eventdatetime;
				$this->registration_payment_method = isset($post['registration_payment_method']) ? trim($post['registration_payment_method']) : $this->registration_paymentmethod;
				$noSubmit = isset($post['registration_nosubmit']);

				if (empty($this->registration_emailparent)) {
					$this->missingfields['registration_emailparent'] = __('Required field', 'martinehooptopbeter');
				}
				elseif (!PonyPlayDayRegistration::validEMailAddress($this->registration_emailparent)) {
					$this->missingfields['registration_emailparent'] = __('Invalid e-mail address', 'martinehooptopbeter');
				}

				if (empty($this->registration_namechild)) {
					$this->missingfields['registration_namechild'] = __('Required field', 'martinehooptopbeter');
				}

				$this->registration_age_decimal = PonyPlayDayRegistration::parseInteger($this->registration_age);
				if ($this->registration_age_decimal <= 0) {
					$this->missingfields['registration_age'] = __('Invalid age', 'martinehooptopbeter');
				}
				elseif ($this->_configuration->getPonyPlayDayMinimumAge() && ($this->registration_age_decimal < $this->_configuration->getPonyPlayDayMinimumAge())) {
					$this->missingfields['registration_age'] = vsprintf(__('Minimum age is %1$s', 'martinehooptopbeter'), $this->_configuration->getPonyPlayDayMinimumAge());
				}
				elseif ($this->_configuration->getPonyPlayDayMaximumAge() && ($this->registration_age_decimal > $this->_configuration->getPonyPlayDayMaximumAge())) {
					$this->missingfields['registration_age'] = vsprintf(__('Maximum age is %1$s', 'martinehooptopbeter'), $this->_configuration->getPonyPlayDayMaximumAge());
				}

				if (($this->registration_experiencelevel != 'none') && ($this->registration_experiencelevel != 'some') && ($this->registration_experiencelevel != 'high')) {
					$this->missingfields['registration_experiencelevel'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->registration_eventdatetime)) {
					$this->missingfields['registration_eventdatetime'] = __('Required field', 'martinehooptopbeter');
				} else {
					$eventsdatetime = $this->_configuration->getPonyPlayDayEvents();
					if (is_string($eventsdatetime)) {
						if ($eventdatetime != $this->registration_eventdatetime) {
							$this->missingfields['registration_eventdatetime'] = __('Invalid value', 'martinehooptopbeter');
						}
					} elseif (!PonyPlayDayRegistration::isValidEventDateTime($this->registration_eventdatetime, $eventsdatetime)) {
						$this->missingfields['registration_eventdatetime'] = __('Invalid date / time', 'martinehooptopbeter');
					}
				}

				if (($this->registration_payment_method != 'ideal') && ($this->registration_payment_method != 'creditcard')) {
					$this->missingfields['registration_payment_method'] = __('Required field', 'martinehooptopbeter');
				}

				$token = isset($post[$this->_xsrf->getSessionKey()]) ? trim($post[$this->_xsrf->getSessionKey()]) : '';
				if (!$this->_xsrf->verifyToken($token)) {
					$this->missingfields[$this->_xsrf->getSessionKey()] = __('The posted data could not be validated. Please try again.', 'martinehooptopbeter'); 
				}

				if ((count($this->missingfields) == 0) && (!$noSubmit)) {

					$registrationsService = new PonyPlayDayRegistrationsService($this->_configuration);

					$registration = new PonyPlayDayRegistration(0, $this->_configuration->getPonyPlayDayPrice(), $this->registration_emailparent, $this->registration_namechild, $this->registration_age, $this->registration_experiencelevel, $this->registration_eventdatetime, '', $this->registration_payment_method, null, null, $this->_configuration->getCurrentLocale(), null);
					$returnurl = $registrationUrl . '?registrationid=%1$s&verification=%2$s';

					if ($redirecturl = $registrationsService->createMolliePaymentForPonyPlayDayRegistration($registration, $returnurl)) {

                        header("Location: " . $redirecturl);
                        exit;

					} else {
						$this->errorMessage = $paymentService->lastErrorMessage;
					}

				}

			} else {
				
				$registrationId = isset($get['registrationid']) ? trim($get['registrationid']) : 0;
				$paymentVerification = isset($get['verification']) ? trim($get['verification']) : 0;

				if ($registrationId && $paymentVerification) {

					$registrations = new PonyPlayDayRegistrations($this->_configuration->getPaymentsDatabaseDataSourceName(), $this->_configuration->getPaymentsDatabaseUsername(), $this->_configuration->getPaymentsDatabasePassword());
					if ($registration = $registrations->getPonyPlayDayRegistration($registrationId, $paymentVerification)) {

						$this->registration_id = $registration->id;
                        $this->registration_amount = '' . $registration->amount;
                        $this->registration_amount_decimal = $registration->amount; 
						$this->registration_emailparent = $registration->emailAddress;
						$this->registration_namechild = $registration->nameChild;
						$this->registration_age = '' . $registration->age;
						$this->registration_age_decimal = $registration->age;
                        $this->registration_experiencelevel = $registration->experienceLevel;
                        $this->registration_eventdatetime = $registration->eventDateTime;
						$this->registration_payment_id = $registration->paymentId;
						$this->registration_payment_method = $registration->paymentMethod;
						$this->registration_payment_status = $registration->paymentStatus;
						$this->registration_payment_verification = $registration->paymentVerification;
						$this->registration_locale = $registration->locale;

					} else {
						$this->errorMessage = __('The specified registration could not be found.', 'martinehooptopbeter');
					}
				
					$this->doShowPonyPlayDayForm = false;
					$this->doShowPonyPlayDayConfirmation = true;
					
				}

			}
			
		}
		
		function showPonyPlayDayForm()
		{
?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <form action="" method="post">
                    
                    <p><?php _e('Enter your and your child\'s details below. If you want to register more than one child please complete this form for each child separately.', 'martinehooptopbeter'); ?>

                    <?php if (count($this->missingfields) > 0) : ?>
                        <p class="error"><?php _e('One or more fields are not filled in or incorrect. Please check and correct the entered data.', 'martinehooptopbeter'); ?>
                    <?php endif; ?>
                    <?php if ($this->errorMessage) : ?>
                        <p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
                    <?php endif; ?>

                    <fieldset>
                        <p class="<?php if (isset($this->missingfields['registration_emailparent'])) { echo 'error'; } ?>">
                            <label for="registration_emailparent"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_emailparent'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_emailparent'])); ?>)</em><?php endif; ?></label>
                            <input type="email" class="textinput" id="registration_emailparent" name="registration_emailparent" value="<?php echo esc_attr($this->registration_emailparent); ?>" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
                        <p class="<?php if (isset($this->missingfields['registration_namechild'])) { echo 'error'; } ?>">
                            <label for="registration_namechild"><?php _e('Name child', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_namechild'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_namechild'])); ?>)</em><?php endif; ?></label>
                            <input type="text" class="textinput" id="registration_namechild" name="registration_namechild" value="<?php echo esc_attr($this->registration_namechild); ?>" />
                        </p>
                        <p class="<?php if (isset($this->missingfields['registration_age'])) { echo 'error'; } ?>">
                            <label for="registration_age"><?php _e('Age child', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_age'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_age'])); ?>)</em><?php endif; ?></label>
                            <input type="text" class="textinput numberinput" id="registration_age" name="registration_age" value="<?php echo esc_attr($this->registration_age); ?>" placeholder="<?php echo esc_attr(sprintf(__('%1$s till %2$s', 'martinehooptopbeter'), $this->_configuration->getPonyPlayDayMinimumAge(), $this->_configuration->getPonyPlayDayMaximumAge())); ?>"/>
                        </p>
						<p><?php _e('Please specify the level of experience your child has with ponies / horses.', 'martinehooptopbeter'); ?>
                        <p class="<?php if (isset($this->missingfields['registration_experiencelevel'])) { echo 'error'; } ?>">
                            <label><?php _e('Experience level', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_experiencelevel'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_experiencelevel'])); ?>)</em><?php endif; ?></label>
                        </p>
                        <ul class="<?php if (isset($this->missingfields['registration_experiencelevel'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="registration_experiencelevel_none" name="registration_experiencelevel" value="none"<?php if ($this->registration_experiencelevel == 'none') { echo ' checked="checked"'; } ?>><label for="registration_experiencelevel_none"><?php _e('No experience (or very little)', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="radio" class="radio" id="registration_experiencelevel_some" name="registration_experiencelevel" value="some"<?php if ($this->registration_experiencelevel == 'some') { echo ' checked="checked"'; } ?>><label for="registration_experiencelevel_some"><?php _e('Some experience in caring for and riding ponnies', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="radio" class="radio" id="registration_experiencelevel_high" name="registration_experiencelevel" value="high"<?php if ($this->registration_experiencelevel == 'high') { echo ' checked="checked"'; } ?>><label for="registration_experiencelevel_high"><?php _e('A lot of experience, has taken riding lessons', 'martinehooptopbeter'); ?></label></li>
                        </ul>
						<?php

							$eventsdatetime = $this->_configuration->getPonyPlayDayEvents();
							if (is_string($eventsdatetime)) :
						
						?><input type="hidden" name="registration_eventdatetime" value="<?php echo esc_attr($eventsdatetime); ?>" />
						<?php

							else:

								if ($events = PonyPlayDayRegistration::getArrayWithEventsDateTimeOpenForRegistration($eventsdatetime)) :
									if (count($events) == 1) :

						?><p class="<?php if (isset($this->missingfields['registration_eventdatetime'])) { echo 'error'; } ?>"><?php echo esc_attr(sprintf(__('You are registering for the Pony Play Day at %1$s.', 'martinehooptopbeter'), $events[0]['eventdatetimestring'])); ?></p>
						<input type="hidden" name="registration_eventdatetime" value="<?php echo esc_attr($events[0]['eventdatetime']); ?>" />
						<?php
									else:

						?><p class="<?php if (isset($this->missingfields['registration_eventdatetime'])) { echo 'error'; } ?>">
                            <label><?php _e('Choose date / time', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_eventdatetime'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_eventdatetime'])); ?>)</em><?php endif; ?></label>
                        </p>
                        <ul class="<?php if (isset($this->missingfields['registration_eventdatetime'])) { echo 'error'; } ?>">
						<?php foreach($events as $event) : ?>
                            <li><input type="radio" class="radio" id="registration_eventdatetime_<?php echo esc_attr($event['eventdatetime']); ?>" name="registration_eventdatetime" value="<?php echo esc_attr($event['eventdatetime']); ?>"<?php if ($this->registration_eventdatetime == $event['eventdatetime']) { echo ' checked="checked"'; } ?>><label for="registration_eventdatetime_<?php echo esc_attr($event['eventdatetime']); ?>"><?php echo esc_attr($event['eventdatetimestring']); ?></label></li>
						<?php endforeach; ?>
                        </ul>
								<?php endif; ?>
							<?php endif; ?>
						<?php endif; ?>
                        <p><?php echo esc_attr(sprintf(__('The cost per child is %1$s. You can pay immediately online.', 'martinehooptopbeter'), PonyPlayDayRegistration::formatEuroPrice($this->_configuration->getPonyPlayDayPrice()))); ?>
                        <p class="<?php if (isset($this->missingfields['registration_payment_method'])) { echo 'error'; } ?>">
                            <label><?php _e('Payment method', 'martinehooptopbeter'); ?><?php if (isset($this->missingfields['registration_payment_method'])) : ?> <em>(<?php echo esc_attr(strtolower($this->missingfields['registration_payment_method'])); ?>)</em><?php endif; ?></label>
                        </p>
                        <ul class="paymentmethods <?php if (isset($this->missingfields['registration_payment_method'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="registration_payment_method_ideal" name="registration_payment_method" value="ideal"<?php if ($this->registration_payment_method == 'ideal') { echo ' checked="checked"'; } ?>><label for="registration_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?><img src="<?php echo esc_attr(get_bloginfo('template_url') . '/img/ideal.svg'); ?>" alt="<?php _e('iDEAL', 'martinehooptopbeter'); ?>" /></label></li>
                            <li><input type="radio" class="radio" id="registration_payment_method_creditcard" name="registration_payment_method" value="creditcard"<?php if ($this->registration_payment_method == 'creditcard') { echo ' checked="checked"'; } ?>><label for="registration_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?><img src="<?php echo esc_attr(get_bloginfo('template_url') . '/img/creditcards.svg'); ?>" alt="<?php _e('Credit Card', 'martinehooptopbeter'); ?>" /></label></li>
                        </ul>
                    </fieldset>

					<input type="hidden" name="<?php echo esc_attr($this->_xsrf->getSessionKey()); ?>" value="<?php echo esc_attr($this->_xsrf->generateToken()); ?>" />
                    <?php if (isset($this->missingfields[$this->_xsrf->getSessionKey()])) : ?>
						<p class="error"><?php echo esc_attr($this->missingfields[$this->_xsrf->getSessionKey()]); ?></p>
					<?php endif; ?>

                    <div class="buttons">
                        <button type="submit" class="btn left"><?php _e('Register and Pay Online', 'martinehooptopbeter'); ?></button>
                    </div>

                </form>

            </div>

        </div>
    </section>

<?php

		}
		
		function showPonyPlayDayConfirmation() {
			
			$showRefresh = false;
			$showRegisterAgain = false;
			
?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
			
				<?php if ($this->errorMessage) : ?>
					<p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
				<?php else : ?>
			
					<?php if (($this->registration_payment_status == 'paid') || ($this->registration_payment_status == 'paidout')) :  ?>
					
						<h2><?php _e('Thank You', 'martinehooptopbeter'); ?></h2>
						
						<p><?php echo esc_attr(sprintf(__('Your registration of %1$s for the Pony Play Day has been received. We also received your payment of %2$s.', 'martinehooptopbeter'), $this->registration_namechild, PonyPlayDayRegistration::formatEuroPrice($this->registration_amount_decimal))); ?></p>
					
						<div class="buttons">
							<a href="<?php _e('/ponyspeeldag/', 'martinehooptopbeter'); ?>" class="btn"><?php _e('Continue', 'martinehooptopbeter'); ?></a>
						</div>
						
						<?php if ($this->_configuration->getGoogleAnalyticsTrackingId() !== null) : ?>
						<script>

							ga('require', 'ecommerce');
							ga('ecommerce:addTransaction', {
								'id': '<?php echo $this->registration_id; ?>',
								'revenue': '<?php echo PonyPlayDayRegistration::formatDecimal($this->registration_amount_decimal); ?>',
							});
							ga('ecommerce:send');

						</script>
						<?php endif; ?>
						
					<?php elseif (($this->registration_payment_status == 'cancelled') || ($this->registration_payment_status == 'expired') || ($this->registration_payment_status == 'failed')) :  ?>
						<?php $showRegisterAgain = true; ?>

						<h2><?php _e('Sorry', 'martinehooptopbeter'); ?></h2>
					
						<p><?php _e('The payment of you registration for the Pony Play Day has been cancelled, expired or has failed. We did not receive your payment. Please press the \'Register Again\' button to retry your registration.', 'martinehooptopbeter'); ?></p>
					
					<?php elseif (($this->registration_payment_status == 'refunded') || ($this->registration_payment_status == 'charged_back')) :  ?>
						<?php $showRegisterAgain = true; ?>
						
						<h2><?php _e('Sorry', 'martinehooptopbeter'); ?></h2>
					
						<p><?php _e('The payment of you registration for the Pony Play Day has been refunded or charged back. We did not receive your payment. Please press the \'Register Again\' button to retry your registration.', 'martinehooptopbeter'); ?></p>
					
					<?php else : ?>
						<?php $showRefresh = true; ?>
						<?php $showRegisterAgain = true; ?>
						
						<p><?php echo esc_attr(vsprintf(__('Your registration of %1$s for the Pony Play Day has been received. However we have not received confirmation of your payment.', 'martinehooptopbeter'), $this->registration_namechild)); ?>
						
						<?php if ($this->registration_payment_method == 'ideal') { _e('Normally an iDEAL payment is processed immediately so maybe something went wrong?', 'martinehooptopbeter'); } ?>
						<?php if ($this->registration_payment_method == 'creditcard') { _e('Because you paid with a creditcard it could take a little bit longer before we receive confirmation of your payment.', 'martinehooptopbeter'); } ?></p>
						
						<p><?php _e('You can press the \'Refresh\' button to reload the page to show the latest status of your payment. In case something went wrong, you can press the \'Register Again\' button to retry your registration.', 'martinehooptopbeter'); ?></p>
						
					<?php endif; ?>
					
					<?php if ($showRefresh || $showRegisterAgain) : ?>
				
						<form action="<?php echo esc_attr(get_permalink()); ?>" method="post">
							<div class="buttons">
							
								<?php if ($showRefresh) : ?>
									<a href="<?php echo esc_attr(get_permalink() . '?registrationid=' . $this->registration_id . '&verification=' . $this->registration_payment_verification); ?>" class="btn left"><?php _e('Refresh', 'martinehooptopbeter'); ?></a>
								<?php endif; ?>
								
								<?php if ($showRegisterAgain) : ?>
									<button type="submit" class="btn right"><?php _e('Register Again', 'martinehooptopbeter'); ?></button>
								<?php endif; ?>
								
							</div>
							<?php if ($showRegisterAgain) : ?>
								<input type="hidden" name="registration_emailparent" value="<?php echo esc_attr($this->registration_emailparent); ?>" />
								<input type="hidden" name="registration_namechild" value="<?php echo esc_attr($this->registration_namechild); ?>" />
								<input type="hidden" name="registration_age" value="<?php echo esc_attr($this->registration_age); ?>" />
								<input type="hidden" name="registration_experiencelevel" value="<?php echo esc_attr($this->registration_experiencelevel); ?>" />
								<input type="hidden" name="registration_eventdatetime" value="<?php echo esc_attr($this->registration_eventdatetime); ?>" />
								<input type="hidden" name="registration_payment_method" value="<?php echo esc_attr($this->registration_payment_method); ?>" />
								<input type="hidden" name="registration_nosubmit" value="ON" />
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