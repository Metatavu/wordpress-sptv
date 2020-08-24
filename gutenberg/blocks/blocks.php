<?php
namespace Metatavu\SPTV\Wordpress\Gutenberg\Blocks;

use GuzzleHttp\Client;

require_once(__DIR__ . '/../../templates/template-loader.php');
require_once(__DIR__ . '/../../ptv/ptv.php');

defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

if (!class_exists( 'Metatavu\SPTV\Wordpress\Gutenberg\Blocks\Blocks' ) ) {

  /**
   * Class for handling Gutenberg blocks
   */
  class Blocks {

    private $ptv;

    /**
     * Constructor
     */
    public function __construct() {
      $this->ptv = new \Metatavu\SPTV\Wordpress\PTV\Client();
      add_action('init', [$this, "onInit"]);
    }

    /**
     * Action executed on init
     */
    public function onInit() {
      wp_register_script('sptv-blocks', plugins_url( 'js/sptv-blocks.js', __FILE__ ), ['wp-blocks', 'wp-element', 'wp-i18n']);      
      wp_set_script_translations("sptv-blocks", "sptv", dirname(__FILE__) . '/lang/');
      add_filter("block_categories", [ $this, "blockCategoriesFilter"], 10, 2);

      $serviceChannelComponents = apply_filters("sptv_service_location_service_channel_components", [
        [
          "slug" => "default-all",
          "name" => __("Default template", "sptv")
        ],
        [
          "slug" => "name",
          "name" => __("Name", "sptv")
        ],
        [
          "slug" => "description",
          "name" => __("Description", "sptv")
        ],
        [
          "slug" => "addresses",
          "name" => __("Addresses", "sptv")
        ],
        [
          "slug" => "email",
          "name" => __("Email", "sptv")
        ],
        [
          "slug" => "webpage",
          "name" => __("Website", "sptv")
        ],
        [
          "slug" => "phone-numbers",
          "name" => __("Phone numbers", "sptv")
        ],
        [
          "slug" => "service-hours",
          "name" => __("Service Hours", "sptv")
        ]
      ]);

      $serviceComponents = apply_filters("sptv_service_components", [
        [
          "slug" => "default-all",
          "name" => __("Default (no service channels)", "sptv")
        ],
        [
          "slug" => "name",
          "name" => __("Name", "sptv")
        ],
        [
          "slug" => "summary",
          "name" => __("Summary", "sptv")
        ],
        [
          "slug" => "description",
          "name" => __("Description", "sptv")
        ],
        [
          "slug" => "user-instruction",
          "name" => __("User instruction", "sptv")
        ],
        [
          "slug" => "requirements",
          "name" => __("Requirements", "sptv")
        ],
        [
          "slug" => "service-channels",
          "name" => __("Service channels", "sptv")
        ],
      ]);

      wp_localize_script('sptv-blocks', 'sptv', [ 
        "serviceLocationServiceChannelBlock" => [
          "components" => $serviceChannelComponents
        ],
        "serviceBlock" => [
          "components" => $serviceComponents
        ]
      ]);

      register_block_type('sptv/service-location-service-channel-block', [
        'attributes' => [ 
          "id" => [
            'type' => 'string'
          ],
          "component" => [
            'type' => 'string'
          ],
          "language" => [
            'type' => 'string'
          ]
        ],
        'editor_script' => 'sptv-blocks',
        'render_callback' => [ $this, "renderServiceLocationServiceChannelBlock" ]
      ]);

      register_block_type('sptv/service-block', [
        'attributes' => [ 
          "id" => [
            'type' => 'string'
          ],
          "component" => [
            'type' => 'string'
          ],
          "language" => [
            'type' => 'string'
          ]
        ],
        'editor_script' => 'sptv-blocks',
        'render_callback' => [ $this, "renderServiceBlock" ]
      ]);
    }
    
    /**
     * Renders a list block
     *
     * Return a HTML representation of events
     *
     * @property array $attributes {
     *   block attributes
     * 
     *   @type string $id service location service channel block
     * }
     */
    public function renderServiceLocationServiceChannelBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $serviceChannel = $this->ptv->findServiceChannel($id);

      $templateData = [
        "serviceChannel" => $serviceChannel,
        "language" => $language
      ];

      ob_start();
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();
      $templateLoader->set_template_data($templateData)->get_template_part("components/service_location_service_channel/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
    }

    /**
     * Renders a service component block
     *
     * Return a HTML representation of a service component
     *
     * @property array $attributes {
     *   block attributes
     * 
     *   @type string $id service block
     * }
     */
    public function renderServiceBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $service = $this->ptv->findService($id);

      $templateData = [
        "service" => $service,
        "language" => $language
      ];

      ob_start();
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();
      $templateLoader->set_template_data($templateData)->get_template_part("components/service/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
    }
    
    /**
     * Filter method for block categories. Used to add custom category for SPTV
     * 
     * @param array $categories categories
     * @param \WP_Post post being loaded
     */
    public function blockCategoriesFilter($categories, $post) {
      $categories[] = [
        'slug' => 'sptv',
        'title' => __( 'Finnish Service Catalogue', 'sptv' ),
      ];
      
      return $categories;
    }
    
    /**
     * Resoles place name in current locale 
     */
    private function getCurrentLanguage() {
      $locale = get_locale();
      return substr($locale, 0, 2);
    }

  }

}

new Blocks();

?>