<?php
namespace Metatavu\SPTV\Wordpress\Gutenberg\Blocks;

use Metatavu\SPTV\Wordpress\Settings\Settings;
use GuzzleHttp\Client;

require_once(__DIR__ . '/../../templates/template-loader.php');
require_once(__DIR__ . '/../../ptv/ptv.php');
require_once(__DIR__ . '/../../settings/settings.php');

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

      $electronicServiceChannelComponents = apply_filters("sptv_electronic_service_channel_components", [
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
          "slug" => "webpages",
          "name" => __("Url", "sptv")
        ],
        [
          "slug" => "description-and-url",
          "name" => __("Description and url", "sptv")
        ]
      ]);

      $webpageServiceChannelComponents = apply_filters("sptv_webpage_service_channel_components", [
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
          "slug" => "webpages",
          "name" => __("Web pages", "sptv")
        ]
      ]);

      $printableFormServiceChannelComponents = apply_filters("sptv_printable_form_service_channel_components", [
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
          "slug" => "channelurls",
          "name" => __("Channel urls", "sptv")
        ],
        [
          "slug" => "attachmenturls",
          "name" => __("Attachment urls", "sptv")
        ],    
        [
          "slug" => "description-and-url",
          "name" => __("Description and url", "sptv")
        ]
      ]);

      $phoneServiceChannelComponents = apply_filters("sptv_phone_service_channel_components", [
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
          "slug" => "phone-numbers",
          "name" => __("Phone numbers", "sptv")
        ]
      ]);

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
        ],
        [
          "slug" => "accessibility",
          "name" => __("Accessibility information", "sptv")
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
        [
          "slug" => "electronic-service-list",
          "name" => __("Electronic service list", "sptv")
        ],
        [
          "slug" => "service-location-list",
          "name" => __("Service location list", "sptv")
        ],
        [
          "slug" => "phone-service-list",
          "name" => __("Phone service list", "sptv")
        ],
        [
          "slug" => "webpage-service-list",
          "name" => __("Webpage service list", "sptv")
        ],
        [
          "slug" => "printable-form-list",
          "name" => __("Printable form list", "sptv")
        ],
        [
          "slug" => "languages",
          "name" => __("Languages", "sptv")
        ]
      ]);

      $organizationComponents = apply_filters("sptv_organization_components", [
        [
          "slug" => "default-all",
          "name" => __("Default", "sptv")
        ],
        [
          "slug" => "name",
          "name" => __("Name", "sptv")
        ],
        [
          "slug" => "description",
          "name" => __("Description", "sptv")
        ]
      ]);

      wp_localize_script('sptv-blocks', 'sptv', [ 
        "serviceLocationServiceChannelBlock" => [
          "components" => $serviceChannelComponents
        ],
        "electronicServiceChannelBlock" => [
          "components" => $electronicServiceChannelComponents
        ],
        "webpageServiceChannelBlock" => [
          "components" => $webpageServiceChannelComponents
        ],
        "printableFormServiceChannelBlock" => [
          "components" => $printableFormServiceChannelComponents
        ],
        "phoneServiceChannelBlock" => [
          "components" => $phoneServiceChannelComponents
        ],
        "serviceBlock" => [
          "components" => $serviceComponents
        ],
        "organizationBlock" => [
          "components" => $organizationComponents,
          "organizationIds" => Settings::getOrganizationIds()
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

      register_block_type('sptv/electronic-service-channel-block', [
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
        'render_callback' => [ $this, "renderElectronicServiceChannelBlock" ]
      ]);

      register_block_type('sptv/webpage-service-channel-block', [
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
        'render_callback' => [ $this, "renderWebpageServiceChannelBlock" ]
      ]);

      register_block_type('sptv/printable-form-service-channel-block', [
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
        'render_callback' => [ $this, "renderPrintableFormServiceChannelBlock" ]
      ]);

      register_block_type('sptv/phone-service-channel-block', [
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
        'render_callback' => [ $this, "renderPhoneServiceChannelBlock" ]
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

      register_block_type('sptv/organization-block', [
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
        'render_callback' => [ $this, "renderOrganizationBlock" ]
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
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "serviceChannel" => $serviceChannel,
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/service_location_service_channel/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
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
    public function renderElectronicServiceChannelBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $serviceChannel = $this->ptv->findServiceChannel($id);
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "serviceChannel" => $serviceChannel,
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/electronic_service_channel/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
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
    public function renderWebpageServiceChannelBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $serviceChannel = $this->ptv->findServiceChannel($id);
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "serviceChannel" => $serviceChannel,
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/webpage_service_channel/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
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
    public function renderPrintableFormServiceChannelBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $serviceChannel = $this->ptv->findServiceChannel($id);
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "serviceChannel" => $serviceChannel,
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/printable_form_service_channel/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
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
    public function renderPhoneServiceChannelBlock($attributes) {
      $result = ''; 

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];

      $serviceChannel = $this->ptv->findServiceChannel($id);
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "serviceChannel" => $this->processPhoneServiceChannel($serviceChannel),
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/phone_service_channel/$component");
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
      $serviceChannels = [];
    
      switch ($component) {
        case "electronic-service-list":
          $serviceChannels = $this->getAttachedServiceChannels($service, "EChannel");
        break;
        case "service-location-list":
          $serviceChannels = $this->getAttachedServiceChannels($service, "ServiceLocation");
        break;
        case "phone-service-list":
          $serviceChannels = $this->getAttachedServiceChannels($service, "Phone");
        break;
        case "webpage-service-list":
          $serviceChannels = $this->getAttachedServiceChannels($service, "WebPage");
        break;
        case "printable-form-list":
          $serviceChannels = $this->getAttachedServiceChannels($service, "PrintableForm");
        break;
        default:
      }

      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "service" => $service,
        "language" => $language,
        "serviceChannels" => $serviceChannels,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/service/$component");
      $result = ob_get_contents();
      ob_end_clean();

      return $result; 
    }

    /**
     * Renders a organization component block
     *
     * Return a HTML representation of a organization component
     *
     * @property array $attributes {
     *   block attributes
     * 
     *   @type string $id organization block
     * }
     */
    public function renderOrganizationBlock($attributes) {
      $result = '';

      $id = $attributes["id"];
      $component = $attributes["component"];
      $language = $attributes["language"];
      $organization = $this->ptv->findOrganization($id);
      $templateLoader = new \Metatavu\SPTV\TemplateLoader();

      $templateData = [
        "organization" => $organization,
        "language" => $language,
        'templateLoader' => $templateLoader
      ];

      ob_start();
      $templateLoader->set_template_data($templateData)->get_template_part("components/organization/$component");
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

    /**
     * Gets attached service channels
     *
     * @param array $service service channel
     * @param string $type service channel type
     */
    private function getAttachedServiceChannels($service, $type) {
      $serviceChannels = array_map(function ($serviceChannel) {
        $channelId = $serviceChannel["serviceChannel"]["id"];
        $channel = $this->ptv->findServiceChannel($channelId);
        return $channel;
      }, $service["serviceChannels"]);

      $serviceChannels = array_filter(
        $serviceChannels,
        function ($channel) use ($type) {
          return $channel["serviceChannelType"] == $type;
        }
      );

      return $serviceChannels;
    }

    /**
     * Processes a phone service channel
     * 
     * @param array $phoneNumber phone number to be processed
     * @return array processed phone number 
     */
    private function processPhoneServiceChannel($serviceChannel) {
      if (isset($serviceChannel["phoneNumbers"]) && is_array($serviceChannel["phoneNumbers"])) {
        $serviceChannel["phoneNumbers"] = array_map([$this, "processPhoneNumber"], $serviceChannel["phoneNumbers"]);
      }

      return $serviceChannel;
    }

    /**
     * Processes a phone number
     * 
     * @param array $phoneNumber phone number to be processed
     * @return array processed phone number 
     */
    private function processPhoneNumber($phoneNumber) {
      if (isset($phoneNumber["number"]) && empty($phoneNumber["prefixNumber"]) && str_starts_with($phoneNumber["number"], "+358")) {
        $phoneNumber["number"] = substr($phoneNumber["number"], 4);
        $phoneNumber["prefixNumber"] = "+358";
      }
      
      return $phoneNumber;
    }

  }

}

new Blocks();

?>