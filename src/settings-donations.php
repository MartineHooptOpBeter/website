<?php

    @@HEADER@@

	class DonationsSettingsPage
	{
		private $options;

		public function __construct()
		{
			add_action( 'admin_menu', array( $this, 'add_donations_settings_page' ) );
			add_action( 'admin_init', array( $this, 'donations_settings_page_init' ) );
		}

		public function add_donations_settings_page()
		{
			// The page will be added under "Settings"
			add_options_page(
				__('Donations', 'martinehooptopbeter' ),
				__('Donations', 'martinehooptopbeter' ),
				'manage_options',
				'donations-settings',
				array( $this, 'create_donations_settings_page' )
			);
		}

		public function create_donations_settings_page()
		{
			// Set class property
			$this->options = get_option( 'donations_options' );
			?>
			<div class="wrap">
				<h1><?php _e('Donations', 'martinehooptopbeter' ); ?></h1>
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'donations_options_group' );
					do_settings_sections( 'donations-settings' );
					submit_button();
				?>
				</form>
			</div>
			<?php
		}

		public function donations_settings_page_init()
		{        
			register_setting(
				'donations_options_group', // Option group
				'donations_options', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				'donations_offline_section', // ID
				__('Offline donations', 'martinehooptopbeter' ), // Title
				array( $this, 'print_offline_section_header' ), // Callback
				'donations-settings' // Page
			);  

			add_settings_field(
				'offline_number', 
				__('Count', 'martinehooptopbeter'), 
				array( $this, 'offline_number_callback' ), 
				'donations-settings', 
				'donations_offline_section'
			);      

			add_settings_field(
				'offline_amount', 
				__('Amount', 'martinehooptopbeter'), 
				array( $this, 'offline_amount_callback' ), 
				'donations-settings', 
				'donations_offline_section'
			);
		}

		public function sanitize( $input )
		{
			$new_input = array();
			if( isset( $input['offline_number'] ) ) {
				$number = 0;
				$tmp = sanitize_text_field( $input['offline_number'] );
				if (is_numeric($tmp)) {
					$new_input['offline_number'] = intval($tmp);
				}
				
				$new_input['apikey'] = sanitize_text_field( $input['apikey'] );
			}

			if( isset( $input['offline_amount'] ) ) {
				$amount = 0;
				$tmp = sanitize_text_field( $input['offline_amount'] );
				if (is_numeric($tmp)) {
					$new_input['offline_amount'] = intval($tmp);
				}
			}

			return $new_input;
		}

		public function print_offline_section_header()
		{
			print __('Enter the total amount and number of offline donations below. These will be added to the online donations.', 'martinehooptopbeter');
		}

		public function offline_number_callback()
		{
			printf(
				'<input type="text" id="offline_number" name="donations_options[offline_number]" value="%s" class="regular-text" />',
				isset( $this->options['offline_number'] ) ? esc_attr( $this->options['offline_number']) : ''
			);
		}

		public function offline_amount_callback()
		{
			printf(
				'<input type="text" id="offline_amount" name="donations_options[offline_amount]" value="%s" class="regular-text" /><div><small>%s</small></div>',
				isset( $this->options['offline_amount'] ) ? esc_attr( $this->options['offline_amount']) : '', __('Enter the amount in Euro cents.', 'martinehooptopbeter')
			);
		}
	}

	if( is_admin() )
		$donations_settings_page = new DonationsSettingsPage();
	
?>