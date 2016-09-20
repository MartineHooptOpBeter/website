<?php

	class MollieSettingsPage
	{
		private $options;

		public function __construct()
		{
			add_action( 'admin_menu', array( $this, 'add_mollie_settings_page' ) );
			add_action( 'admin_init', array( $this, 'mollie_settings_page_init' ) );
		}

		public function add_mollie_settings_page()
		{
			// The page will be added under "Settings"
			add_options_page(
				__('Mollie settings', 'martinehooptopbeter' ),
				'Mollie', 
				'manage_options', 
				'mollie-settings', 
				array( $this, 'create_mollie_settings_page' )
			);
		}

		public function create_mollie_settings_page()
		{
			// Set class property
			$this->options = get_option( 'mollie_options' );
			?>
			<div class="wrap">
				<h1><?php _e('Mollie settings', 'martinehooptopbeter' ); ?></h1>
				<form method="post" action="options.php">
				<?php
					// This prints out all hidden setting fields
					settings_fields( 'mollie_options_group' );
					do_settings_sections( 'mollie-settings' );
					submit_button();
				?>
				</form>
			</div>
			<?php
		}

		public function mollie_settings_page_init()
		{        
			register_setting(
				'mollie_options_group', // Option group
				'mollie_options', // Option name
				array( $this, 'sanitize' ) // Sanitize
			);

			add_settings_section(
				'mollie_setting_api_section', // ID
				__('API', 'martinehooptopbeter' ), // Title
				array( $this, 'print_api_section_header' ), // Callback
				'mollie-settings' // Page
			);  

			add_settings_field(
				'apikey', 
				__('API Key', 'martinehooptopbeter'), 
				array( $this, 'apikey_callback' ), 
				'mollie-settings', 
				'mollie_setting_api_section'
			);      

			add_settings_field(
				'webhookurl', 
				__('Webhook URL', 'martinehooptopbeter'), 
				array( $this, 'webhookurl_callback' ), 
				'mollie-settings', 
				'mollie_setting_api_section'
			);      
		}

		public function sanitize( $input )
		{
			$new_input = array();
			if( isset( $input['apikey'] ) )
				$new_input['apikey'] = sanitize_text_field( $input['apikey'] );

			if( isset( $input['webhookurl'] ) )
				$new_input['webhookurl'] = sanitize_text_field( $input['webhookurl'] );

			return $new_input;
		}

		public function print_api_section_header()
		{
			print __('Enter your Mollie API settings below.', 'martinehooptopbeter');
		}

		public function apikey_callback()
		{
			printf(
				'<input type="text" id="apikey" name="mollie_options[apikey]" value="%s" class="regular-text" />',
				isset( $this->options['apikey'] ) ? esc_attr( $this->options['apikey']) : ''
			);
		}

		public function webhookurl_callback()
		{
			printf(
				'<input type="text" id="webhookurl" name="mollie_options[webhookurl]" value="%s" class="regular-text" /><div><small>%s</small></div>',
				isset( $this->options['webhookurl'] ) ? esc_attr( $this->options['webhookurl']) : '', __('This should point to mollie-webhook.php in the theme folder.', 'martinehooptopbeter')
			);
		}
	}

	if( is_admin() )
		$mollie_settings_page = new MollieSettingsPage();
	
?>