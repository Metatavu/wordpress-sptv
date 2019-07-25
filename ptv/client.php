<?php
  namespace Metatavu\SPTV\Wordpress\PTV;
  
  require_once( __DIR__ . '/../vendor/autoload.php');

  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\SPTV\Wordpress\PTV\Client' ) ) {
    
    /**
     * PTV Client
     */
    class Client {

      private $serviceChannelCache = [];

      /**
       * Finds a service channel by id
       * 
       * @param id id
       * @returns found service or null if not found
       */
      public function findServiceChannel($id) {
        if (!$this->serviceChannelCache[$id]) {
          $this->serviceChannelCache[$id] = json_decode($this->doGetRequest("https://api.palvelutietovaranto.suomi.fi/api/v10/ServiceChannel/$id"), true);
        }

        return $this->serviceChannelCache[$id];
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