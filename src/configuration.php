<?php

    @@HEADER@@


    class Configuration
    {
        protected $_config = null;
		
		protected $_overrideLocale = null;

        public function __construct() {
        	include 'config.php';
            $this->_config = $config;
			$this->_setPhpLocale();
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
            return $this->_getPropertyForLocale($locale, $this->_config['donate_email_fromaddress']);
        }

        public function getDonationConfirmationFromName() { 
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, $this->_config['donate_email_fromname']);
        }

        public function getMollieApiKey() {
            return $this->_config['mollie_apikey'];
        }

        public function getMollieWebhookUrl() {
            return $this->_config['mollie_webhookurl'];
        }

        public function getContactSendMailTo() {
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, $this->_config['contact_sendmailto']);
        }

        public function getGoogleAnalyticsTrackingId() {
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, $this->_config['googleanalytics_trackingid']);
        }

        public function getGoogleSearchConsoleSiteVerification() {
            $locale = $this->getCurrentLocale();
            return $this->_getPropertyForLocale($locale, $this->_config['googlesearch_siteverification']);
        }

		public function overrideLocale($locale) {
			if (!$locale || !is_string($locale))
				return;
			
			$this->_overrideLocale = $locale;
			$this->_setPhpLocale();
		}
		
        public function getCurrentLocale() {

			if (isset($this->_overrideLocale))
				return $this->_overrideLocale;

			$current_locale = null;

		    // Check if the Polylang plugin is installed
		    if (function_exists('pll_default_language')) {
                $current_locale = pll_current_language('locale');

			// Otherwise use WordPress
            } elseif (function_exists('get_locale')) {
                $current_locale = get_locale();

			}

    		return $current_locale;
	    }
		
		protected function _setPhpLocale() {
			if ($locale = $this->_getLocaleForPhp())
				setlocale(LC_ALL, $locale);
		}

		protected function _getLocaleForPhp() {
			
			if ($this->_isUnixOS()) {
				return $this->getCurrentLocale();
				
			} elseif ($this->_isMacOS()) {
				return $this->getCurrentLocale() . '.UTF-8';
				
			} elseif ($this->_isWindowsOS()) {
				return $this->_getWindowsLocale();
				
			}
			
			return null;
		}
		
		protected function _isUnixOS() {
			return (stripos(PHP_OS, 'win') === false);
		}

		protected function _isMacOS() {
			return (stripos(PHP_OS, 'darwin') === 0);
		}

		protected function _isWindowsOS() {
			return (stripos(PHP_OS, 'win') === 0) || (stripos(PHP_OS, 'cygwin') === 0);
		}

		protected function _getWindowsLocale() {
			if (!$locale = $this->getCurrentLocale())
				return null;
			
			switch ($locale) {
				case 'nl_NL' : return 'nld'; break;
				case 'en_US' : return 'usa'; break;
				default:
					return null;
			}
		}

	    protected function _getPropertyForLocale($locale, $propertiesarray) {

            if (!$propertiesarray)
				return null;
			
			if (!is_array($propertiesarray)) {
				
				if (is_string($propertiesarray))
					return $propertiesarray;
				
                return null;
			}
            
            foreach($propertiesarray as $property) {

                if (!$property || !is_array($property))
                    continue;

                if (!isset($property['locale']) || !fnmatch($property['locale'], $locale))
                    continue;

                if (isset($property['value']))
                    return $property['value'];
            }

        }

    }

?>