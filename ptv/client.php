<?php
  namespace Metatavu\SPTV\Wordpress\PTV;
  
  use Metatavu\SPTV\Wordpress\Settings\Settings;

  require_once(__DIR__ . '/../vendor/autoload.php');

  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\SPTV\Wordpress\PTV\Client' ) ) {
    
    /**
     * PTV Client
     */
    class Client {

      private $serviceChannelCache = [];
      private $serviceCache = [];
      private $organizationCache = [];

      /**
       * Finds a service channel by id
       * 
       * @param id id
       * @returns found service or null if not found
       */
      public function findServiceChannel($id) {
        $ptvVersion = Settings::getValue("version");
        if (!isset($this->serviceChannelCache[$id])) {
          $this->serviceChannelCache[$id] = json_decode($this->doGetRequest("https://api.palvelutietovaranto.suomi.fi/api/$ptvVersion/ServiceChannel/$id"), true);
        }

        return $this->serviceChannelCache[$id];
      }

      /**
       * Finds a service by id
       * 
       * @param id id
       * @returns found service or null if not found
       */
      public function findService($id) {
        $ptvVersion = Settings::getValue("version");
        if (!isset($this->serviceCache[$id])) {
          $this->serviceCache[$id] = json_decode($this->doGetRequest("https://api.palvelutietovaranto.suomi.fi/api/$ptvVersion/Service/$id"), true);
        }

        return $this->serviceCache[$id];
      }

      /**
       * Finds a organization by id
       * 
       * @param id id
       * @returns found organization or null if not found
       */
      public function findOrganization($id) {
        $ptvVersion = Settings::getValue("version");
        if (!$this->organizationCache[$id]) {
          $this->organizationCache[$id] = json_decode($this->doGetRequest("https://api.palvelutietovaranto.suomi.fi/api/$ptvVersion/Organization/$id"), true);
        }

        return $this->organizationCache[$id];
      }

      /**
       * Executes a GET request into given URL and returns response as string
       * 
       * @param string $url URL
       * @return string response
       */
      private function doGetRequest($url) {
        $curl = curl_init();
        try {
          curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url
          ]);
    
          return curl_exec($curl);  
        } finally {
          curl_close($curl);
        }
      }
    }  
  }
  
?>