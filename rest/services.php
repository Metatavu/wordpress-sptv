<?php
  namespace Metatavu\SPTV\Wordpress\Rest;
  
  require_once( __DIR__ . '/../vendor/autoload.php');
  require_once( __DIR__ . '/../rest/rest.php');

  use Metatavu\SPTV\Wordpress\Settings\Settings;
  use Elasticsearch\ClientBuilder;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\SPTV\Wordpress\Rest\Services' ) ) {
    
    /**
     * REST services class
     */
    class Services {

      private static $ALLOWED_TYPES = ["echannel", "webpage", "printableform", "phone", "servicelocation"];

      /**
       * Constructor
       */
      public function __construct() {
        register_rest_route('sptv', '/search-service-channels', [
          [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'searchServiceChannels')
          ]
        ]);
        register_rest_route('sptv', '/search-services', [
          [
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array($this, 'searchServices')
          ]
        ]);
      }
      
      /**
       * REST endpoint for /sptv/search-service-channels
       * 
       * @param \WP_REST_Request $data
       * @return WP_REST_Response | string[] response  
       */
      public function searchServiceChannels($data) {
        $ptvVersion = Settings::getValue("version");
        $query = $data->get_query_params()["q"];
        $type = $data->get_query_params()["type"];
        $lang = $data->get_query_params()["lang"];
        $options = get_option(SPTV_SETTINGS_OPTION);

        if (empty($query)) {
          return new \WP_REST_Response("Missing query", 400);
        }

        if (empty($lang)) {
          return new \WP_REST_Response("Missing lang", 400);
        }

        if (empty($ptvVersion)) {
          return new \WP_REST_Response("Missing PTV version", 400);
        }

        if (!in_array($type, self::$ALLOWED_TYPES)) {
          return new \WP_REST_Response("Invalid type", 400);
        }

        $nameQuery = [ 'match' => [ "serviceChannelNames_$lang" => [ "query" => $query ] ] ];

        /**
         * parse organization ids from options
         */
        $searchValue = 'ptv';
        $allowed=array_filter(
          array_keys($options), function($key) use ($searchValue ) {
            return stristr($key, $searchValue ) ;
          });

        $organizationIdArray = array_intersect_key($options,array_flip($allowed));
        $organizationIds = array_values($organizationIdArray);
        $organizationQuery = [ 'terms' => [ "organizationId" =>  $organizationIds ] ];

        $query = [
          'bool' => [
            'must' => [ $nameQuery, $organizationQuery ]
            ]
          ];        

        $searchResult = $this->getClient()->search([
          'index' => "$ptvVersion-$type-service-channel",
          'body' => [ 
            "_source" => false,
            'query' => $query
          ]
        ]);

        return $this->getResultIds($searchResult);
      }

      /**
       * REST endpoint for /sptv/search-services
       * 
       * @param \WP_REST_Request $data
       * @return WP_REST_Response | string[] response  
       */
      public function searchServices($data) {
        $ptvVersion = Settings::getValue("version");
        $query = $data->get_query_params()["q"];
        $lang = $data->get_query_params()["lang"];
        $options = get_option(SPTV_SETTINGS_OPTION);
        
        if (empty($query)) {
          return new \WP_REST_Response("Missing query", 400);
        }

        if (empty($lang)) {
          return new \WP_REST_Response("Missing lang", 400);
        }

        if (empty($ptvVersion)) {
          return new \WP_REST_Response("Missing PTV version", 400);
        }

        $organizationId = Settings::getValue("ptv-organization-id");
        $nameQuery = [ 'match' => [ "serviceNames_$lang" => [ "query" => $query ] ] ];

        /**
         * parse organization ids from options
         */
        $searchValue = 'ptv';
        $allowed=array_filter(
          array_keys($options), function($key) use ($searchValue ) {
            return stristr($key, $searchValue ) ;
          });

        $organizationIdArray = array_intersect_key($options,array_flip($allowed));
        $organizationIds = array_values($organizationIdArray);
        $organizationQuery = [ 'terms' => [ "organizationIds" =>  $organizationIds ] ];


        $query = [
          'bool' => [
            'must' => [ $nameQuery, $organizationQuery ]
            ]
          ];     

        $searchResult = $this->getClient()->search([
          'index' => "$ptvVersion-service",
          'body' => [ 
            "_source" => false,
            'query' => $query
          ]
        ]);

        return $this->getResultIds($searchResult);
      }

      /**
       * Returns ids from search result
       * 
       * @param object $searchResult search result
       * @return string[] result ids
       */
      private function getResultIds($searchResult) {
        $hits = [];
        
        if ($searchResult && $searchResult["hits"] && $searchResult["hits"]["hits"]) {
          $hits = $searchResult["hits"]["hits"];
        }

        $resultIds = $hits ? array_map(function ($hit) {
          return $hit["_id"];
        }, $hits) : [];

        return $resultIds;
      }

      /**
       * Returns Elasticsearch client
       * 
       * @return \Elasticsearch\Client
       */
      private function getClient() {
        $host = parse_url(Settings::getValue("elastic-url"));
        $host['user'] = Settings::getValue("elastic-username");
        $host['pass'] = Settings::getValue("elastic-password");
        $builder = ClientBuilder::create();
        $builder->setHosts([$host]);
        return $builder->build();
      }
      
    }
  
  }
  
  add_action('rest_api_init', function () {
    new Services();
  });
  
?>