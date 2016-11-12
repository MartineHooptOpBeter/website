<?php

    @@HEADER@@

	require_once 'xsrf.php';

	class ContactPage {
	
		protected $_configuration = null;
		protected $_xsrf = null;

        public $doShowContactForm = false;
        public $doShowContactConfirmation = false;
		
		public $errorMessage = null;
		public $missingfields = [];
		
		public $contact_name = '';
		public $contact_email = '';
		public $comtact_message = '';
		
		function ContactPage($configuration) {
			$this->_configuration = $configuration;
			$this->_xsrf = new XSRF();
		}
		
		function processRequest($contactUrl, $server, $post, $get) {
			
			// Check if the contact form is enabled
			if (!$this->isContactFormEnabled())
				return;

			// We show the form by default, unless we decide otherwise
			$this->doShowContactForm = true;

			if ($server['REQUEST_METHOD'] == "POST") {

				$post = stripslashes_deep($post);

				$this->contact_name = isset($post['contact_name']) ? trim($post['contact_name']) : $this->contact_name;
				$this->contact_email = isset($post['contact_email']) ? trim($post['contact_email']) : $this->contact_email;
				$this->contact_message = isset($post['contact_message']) ? trim($post['contact_message']) : $this->contact_message;

				if (empty($this->contact_name)) {
					$this->missingfields['contact_name'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->contact_email)) {
					$this->missingfields['contact_email'] = __('Required field', 'martinehooptopbeter');
				}
				elseif (!$this->validEMailAddress($this->contact_email)) {
					$this->missingfields['contact_email'] = __('Invalid e-mail address', 'martinehooptopbeter');
				}

				if (empty($this->contact_message)) {
					$this->missingfields['contact_message'] = __('Required field', 'martinehooptopbeter');
				}

				$token = isset($post[$this->_xsrf->getSessionKey()]) ? trim($post[$this->_xsrf->getSessionKey()]) : '';
				if (!$this->_xsrf->verifyToken($token)) {
					$this->missingfields[$this->_xsrf->getSessionKey()] = __('The posted data could not be validated. Please try again.', 'martinehooptopbeter'); 
				}

				if (count($this->missingfields) == 0) {

					if ($this->sendContactEMail()) {
						
                        header("Location: " . $contactUrl . '?confirm=yes');
                        exit;

					} else {
						$this->errorMessage = __('An error has occured while sending your e-mail. Please try again...', 'martinehooptopbeter');
					}

				}

			} else {
				
				$this->doShowContactConfirmation = isset($get['confirm']);
                $this->doShowContactForm = !$this->doShowContactConfirmation;

			}
			
		}

		function isContactFormEnabled() {
			return $this->validEmailAddress($this->_configuration->getContactSendMailTo());
		}

		public static function validEMailAddress($emailaddress) {
			return preg_match('/^([0-9a-zA-Z_]([-.\w\+]*[0-9a-zA-Z_])*@([0-9a-zA-Z][-\w]*[0-9a-zA-Z]\.)+[a-zA-Z]{2,})$/', $emailaddress);
		}

        function sendContactEMail() {

			$to      = $this->_configuration->getContactSendMailTo();
			$subject = __('Contact through website Martine Hoopt Op Beter', 'martinehooptopbeter');
			$headers[] = 'From: ' . mb_encode_mimeheader($this->contact_name) . ' <' . $this->contact_email . '>';

			return mb_send_mail($to, $subject, $this->contact_message, implode("\r\n", $headers));
        }
		
		function showContactForm()
		{

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <form action="" method="post">
                    
                    <p><?php _e('Enter your details below and the message or question you would like to send.', 'martinehooptopbeter'); ?>

                    <?php if (count($this->missingfields) > 0) : ?>
                        <p class="error"><?php _e('One or more fields are not filled in or incorrect. Please check and correct the entered data.', 'martinehooptopbeter'); ?>
                    <?php endif; ?>
                    <?php if ($this->errorMessage) : ?>
                        <p class="error"><?php echo esc_attr($this->errorMessage); ?></p>
                    <?php endif; ?>

                    <fieldset>
                        <p class="<?php if (isset($this->missingfields['contact_name'])) { echo 'error'; } ?>">
                            <label for="contact_name"><?php _e('Your name', 'martinehooptopbeter'); ?></label>
                            <input type="text" class="textinput" id="contact_name" name="contact_name" value="<?php echo esc_attr($this->contact_name); ?>" />
                        </p>
                        <p class="<?php if (isset($this->missingfields['contact_email'])) { echo 'error'; } ?>">
                            <label for="contact_email"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?></label>
                            <input type="email" class="textinput" id="contact_email" name="contact_email" value="<?php echo esc_attr($this->contact_email); ?>" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
                        <p class="<?php if (isset($this->missingfields['contact_message'])) { echo 'error'; } ?>">
                            <label for="contact_name"><?php _e('Message', 'martinehooptopbeter'); ?></label>
                            <textarea id="contact_message" name="contact_message" rows="10"><?php echo esc_attr($this->contact_message); ?></textarea>
                        </p>
                    </fieldset>

					<input type="hidden" name="<?php echo esc_attr($this->_xsrf->getSessionKey()); ?>" value="<?php echo esc_attr($this->_xsrf->generateToken()); ?>" />
                    <?php if (isset($this->missingfields[$this->_xsrf->getSessionKey()])) : ?>
						<p class="error"><?php echo esc_attr($this->missingfields[$this->_xsrf->getSessionKey()]); ?></p>
					<?php endif; ?>

                    <div class="buttons">
                        <button type="submit" class="btn left"><?php _e('Send', 'martinehooptopbeter'); ?></button>
                    </div>

                </form>

            </div>

        </div>
    </section>

<?php

		}
		
		function showContactConfirmation() {
			
?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
			
                <h2><?php _e('Thank You', 'martinehooptopbeter'); ?></h2>
                    
                <p><?php _e('Your message has been sent. We try to respond within 24 hours.', 'martinehooptopbeter'); ?></p>
                
                <div class="buttons">
                    <a href="<?php echo get_permalink(); ?>" class="btn"><?php _e('Continue', 'martinehooptopbeter'); ?></a>
                </div>

			</div>

        </div>
    </section>

<?php
			
		}

	}

?>