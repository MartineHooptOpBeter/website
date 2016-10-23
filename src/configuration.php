<?php

    @@HEADER@@


    class Configuration
    {
        protected $_config = null;

        public function __construct() {
        	include 'config.php';
            $this->_config = $config;
        }

        public function getDonationsDatabaseDataSourceName() {
            return $this->_config['donate_dsn'];
        }

        public function getDonationsDatabaseUsername() {
            return $this->_config['donate_username'];
        }

        public function getDonationsDatabasePassword() {
            return $this->_config['donate_password'];
        }

        public function getDonationsGoalValue() {
            return $this->_config['donate_goal'];
        }

        public function getDonationMinimumAmount() {
            return $this->_config['donate_minamount'];
        }

        public function getDonationMaximumAmount() {
            return $this->_config['donate_maxamount'];
        }

        public function getDonationConfirmationFromEmailAddress() { 
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, 'emailaddress', $this->_config['donate_email_fromaddress']);
        }

        public function getDonationConfirmationFromName() { 
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, 'name', $this->_config['donate_email_fromname']);
        }

        public function getMollieApiKey() {
            return $this->_config['mollie_apikey'];
        }

        public function getMollieWebhookUrl() {
            return $this->_config['mollie_webhookurl'];
        }

        public function getContactSendMailTo() {
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, 'emailaddress', $this->_config['contact_sendmailto']);
        }

        public function getGoogleAnalyticsTrackingId() {
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, 'trackingid', $this->_config['googleanalytics_trackingid']);
        }

        public function getCurrentLocale() {

		    // Check if the Polylang plugin is installed
		    if (function_exists('pll_default_language')) {
                $current_locale = pll_current_language('locale');
            } else {
                $current_locale = get_locale();
            }

    		return $current_locale;
	    }

	    protected function _getPropertyForLocale($locale, $propertyname, $propertiesarray) {

            if (!$propertiesarray || !is_array($propertiesarray))
                return null;
            
            foreach($propertiesarray as $property) {

                if (!$property || !is_array($property))
                    continue;

                if (!isset($property['locale']) || !fnmatch($property['locale'], $locale))
                    continue;

                if (isset($property[$propertyname]))
                    return $property[$propertyname];
            }

        }

    }

?>