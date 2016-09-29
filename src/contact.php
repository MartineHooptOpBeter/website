<?php require_once 'config.php' ?><?php

	class ContactPage {
	
        public $doShowContactForm = false;
        public $doShowContactConfirmation = false;
		
		public $errorMessage = null;
		public $missingfields = [];
		
		public $contact_name = '';
		public $contact_email = '';
		public $comtact_message = '';
		
		function ContactPage() {
		}
		
		function processRequest($contactUrl, $server, $post, $get) {
			
			global $config;

			// We show the form by default, unless we decide otherwise
			$this->doShowContactForm = true;
			
			if ($server['REQUEST_METHOD'] == "POST") {

				$this->contact_name = isset($post['contact_name']) ? trim($post['contact_name']) : $this->contact_name;
				$this->contact_email = isset($post['contact_email']) ? trim($post['contact_email']) : $this->contact_email;
				$this->contact_message = isset($post['contact_message']) ? trim($post['contact_message']) : $this->contact_message;

				if (empty($this->contact_name)) {
					$this->missingfields['contact_name'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->contact_email)) {
					$this->missingfields['contact_email'] = __('Required field', 'martinehooptopbeter');
				}

				if (empty($this->contact_message)) {
					$this->missingfields['contact_message'] = __('Required field', 'martinehooptopbeter');
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

        function sendContactEMail() {

			global $config;

			$to      = $config['contact_sendmailto'];
			$subject = __('Contact through website Martine Hoopt Op Beter', 'martinehooptopbeter');
			$headers = 'From: ' . $this->contact_name . ' <' . $this->contact_email . '>';

			return mail($to, $subject, $this->contact_message, $headers);
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