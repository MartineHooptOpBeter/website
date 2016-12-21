<?php

    @@HEADER@@

    class IdealStatus {

        private $issuersstatus = [];

        public function __construct()
        {
            $this->issuersstatus = $this->_loadIssuersStatus();
        }

        public function statusForIssuer($issuer)
        {
            if (!$this->issuersstatus)
                return false;
            
            foreach($this->issuersstatus as $issuerstatus)
            {
                if ($issuer == ('ideal_' . $issuerstatus['issuer_bic']))
                    return (strtoupper($issuerstatus['status']) === 'OK');
            }
        }

        protected function _loadIssuersStatus()
        {
            if ($idealstatus = $this->_loadIssuersStatusFromCache())
                return $idealstatus;

            if ($idealstatus = $this->_getIdealStatusConsumerNotificationAdvise())
            {
                $this->_saveIssuerStatusInCache($idealstatus);
            }
        
            return $idealstatus;
        }

        protected function _loadIssuersStatusFromCache()
        {
            return null;
        }

        protected function _saveIssuerStatusInCache()
        {
            return true;
        }

        protected function _getStaticIdealStatusUrl($file)
        {
            return 'https://www.ideal-status.nl/static/' . $file;
        }

        protected function _getIdealStatusConsumerNotificationAdvise()
        {
            return $this->_sendRequest($this->_getStaticIdealStatusUrl('consumer_notification_advice.json'));
        }

		protected function _sendRequest($url, $method = 'GET') {
			
			// Initialize cURL
			$curl = curl_init();

			// Set options
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_TIMEOUT, 3);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Accept: application/json'));
			
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 2);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
				
			// Execute request
			$apiresult = curl_exec($curl);
			$headerinfo = curl_getinfo($curl);

			// Deinitialize cURL
			curl_close($curl);

			// Check HTTP status
			if (!in_array($headerinfo['http_code'], array('200', '201', '204')))
				return null;
			
			// We assume we got JSON back, so let's decode it to an associative array and return it
			return json_decode($apiresult, true);
		}
        
    }