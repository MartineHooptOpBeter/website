<?php require_once 'donations-class.php' ?><?php require_once 'Mollie/API/Autoloader.php'; ?><?php

    global $config;

	$errorMessage = null;
    $missingfields = [];

    $donate_name = '';
    $donate_email = '';
    $donate_message = '';
    $donate_amount = '';
    $donate_payment_method = '';
    $donate_anonymous = false;
    $donate_no_amount = false;

    if ($_SERVER['REQUEST_METHOD'] == "POST") {

        $donate_name = isset($_POST['donate_name']) ? trim($_POST['donate_name']) : $donate_name;
        $donate_email = isset($_POST['donate_email']) ? trim($_POST['donate_email']) : $donate_email;
        $donate_message = isset($_POST['donate_message']) ? trim($_POST['donate_message']) : $donate_message;
        $donate_amount = isset($_POST['donate_amount']) ? trim($_POST['donate_amount']) : $donate_amount;
        $donate_payment_method = isset($_POST['donate_payment_method']) ? trim($_POST['donate_payment_method']) : $donate_payment_method;
        $donate_anonymous = isset($_POST['donate_anonymous']);
        $donate_no_amount = isset($_POST['donate_no_amount']);

        if (empty($donate_name)) {
            $missingfields['donate_name'] = __('Required field', 'martinehooptopbeter');
        }

        if (empty($donate_email)) {
            $missingfields['donate_email'] = __('Required field', 'martinehooptopbeter');
        }

        $donate_amount_parsed = parseAmount($donate_amount);
        if ($donate_amount_parsed <= 0) {
            $missingfields['donate_amount'] = __('Invalid amount', 'martinehooptopbeter');
        }
        if ($donate_amount_parsed < 500) {
            $missingfields['donate_amount'] = __('Minimum required amount is 5 Euro', 'martinehooptopbeter');
        }

        if (($donate_payment_method != 'ideal') && ($donate_payment_method != 'creditcard')) {
            $missingfields['donate_payment_method'] = __('Required field', 'martinehooptopbeter');
        }

        if (count($missingfields) == 0) {

            $d = new Donation(0, $donate_amount_parsed, $donate_email, $donate_name, $donate_message, '', $donate_payment_method, null, null, $donate_no_amount, $donate_anonymous, null);

            $donations = new Donations($config['donate_dsn'], $config['donate_username'], $config['donate_password']);
            if ($donation = $donations->addDonation($d)) {
				
				var_dump($donation);
				
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
					
					$errorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
				}
				catch (Mollie_API_Exception $e)
				{
					var_dump($e);
					$errorMessage = __('An error has occured while starting your payment.', 'martinehooptopbeter');
				}

            } else {
                $errorMessage = __('An error has occured while saving your donation.', 'martinehooptopbeter');
            }

        }

    }

    function show_donate_page()
    {
		global $errorMessage;
		global $missingfields;
		
        global $donate_name;
        global $donate_name;
        global $donate_email;
        global $donate_message;
        global $donate_amount;
        global $donate_payment_method;
        global $donate_anonymous;
        global $donate_no_amount;

?>	<section class="content">
		<div class="sitewidth clearfix">

            <div class="text">
                <h2><?php _e('Donate', 'martinehooptopbeter'); ?></h2>

                <form action="" method="post">
                    
                    <p><?php _e('Enter your details and the amount you want to donate below. Optionally you can make your donation anonymously or hide the amount of money you donate.', 'martinehooptopbeter'); ?>

                    <?php if (count($missingfields) > 0) : ?>
                        <p class="error"><?php _e('One or more fields are not filled in or incorrect. Please check and correct the entered data.', 'martinehooptopbeter'); ?>
                    <?php endif; ?>
                    <?php if ($errorMessage) : ?>
                        <p class="error"><?php echo esc_attr($errorMessage); ?></p>
                    <?php endif; ?>

                    <fieldset>
                        <p class="<?php if (isset($missingfields['donate_name'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Your name', 'martinehooptopbeter'); ?></label>
                            <input type="text" class="textinput" id="donate_name" name="donate_name" value="<?php echo esc_attr($donate_name); ?>" />
                        </p>
                        <p class="<?php if (isset($missingfields['donate_email'])) { echo 'error'; } ?>">
                            <label for="donate_email"><?php _e('Your E-mail address', 'martinehooptopbeter'); ?></label>
                            <input type="email" class="textinput" id="donate_email" name="donate_email" value="<?php echo esc_attr($donate_email); ?>" placeholder="<?php _e('youremail@domain.com', 'martinehooptopbeter') ?>" />
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_anonymous" name="donate_anonymous"<?php if ($donate_anonymous) { echo ' checked="checked"'; } ?> /><label for="donate_anonymous"><?php _e('I want to remain anonymous, do not show my name on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p><?php _e('Do you want to support Martine?  Leave her a message that will show up on the website.', 'martinehooptopbeter'); ?></p>
                        <p class="<?php if (isset($missingfields['donate_message'])) { echo 'error'; } ?>">
                            <label for="donate_name"><?php _e('Message', 'martinehooptopbeter'); ?></label>
                            <textarea id="donate_message" name="donate_message" rows="10"><?php echo esc_attr($donate_message); ?></textarea>
                        </p>
                    </fieldset>

                    <p><?php _e('Enter the amount you want to donate and choose your prefered payment method. You can donate immediately online.', 'martinehooptopbeter'); ?>
                    <fieldset>
                        <p class="<?php if (isset($missingfields['donate_amount'])) { echo 'error'; } ?>">
                            <label for="donate_amount"><?php _e('Amount to donate', 'martinehooptopbeter'); ?></label>
                            <span>&euro; </span><input type="text" class="textinput numberinput clearnone" id="donate_amount" name="donate_amount" value="<?php echo esc_attr($donate_amount); ?>" placeholder="<?php _e('00.00', 'martinehooptopbeter') ?>"/>
                        </p>
                        <ul>
                            <li><input type="checkbox" class="checkbox" id="donate_no_amount" name="donate_no_amount"<?php if ($donate_no_amount) { echo ' checked="checked"'; } ?> /><label for="donate_no_amount"><?php _e('Do not show the amount that I donate on the website.', 'martinehooptopbeter'); ?></label></li>
                        </ul>
                        <p class="<?php if (isset($missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <label><?php _e('Payment method', 'martinehooptopbeter'); ?></label>
                        </p>
                        <ul class="<?php if (isset($missingfields['donate_payment_method'])) { echo 'error'; } ?>">
                            <li><input type="radio" class="radio" id="donate_payment_method_ideal" name="donate_payment_method" value="ideal"<?php if ($donate_payment_method == 'ideal') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_ideal"><?php _e('iDEAL', 'martinehooptopbeter'); ?></label></li>
                            <li><input type="radio" class="radio" id="donate_payment_method_creditcard" name="donate_payment_method" value="creditcard"<?php if ($donate_payment_method == 'creditcard') { echo ' checked="checked"'; } ?>><label for="donate_payment_method_creditcard"><?php _e('Credit Card', 'martinehooptopbeter'); ?></label></li>
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

    function parseAmount($amount) {
        $amount = str_replace(',', '.', $amount);
        if (is_numeric($amount)) {
            $amound_parsed = floatval($amount);
            return (int)($amound_parsed * 100);
        }
        return 0;
    }

?>