<?php
  namespace Metatavu\SPTV;
  
  if (!defined('ABSPATH')) { 
    exit;
  }

  require_once(constant("SPTV_PLUGIN_DIR") . '/dependencies/classes/gamajo/template-loader/class-gamajo-template-loader.php');
  
  if (!class_exists( 'Metatavu\SPTV\TemplateLoader' ) ) {
    
    /**
     * Template loader for SPTV
     */
    class TemplateLoader extends \SPTV_Gamajo_Template_Loader {

      /**
       * Constructor
       */
      public function __construct() {
        $this->filter_prefix = 'sptv';
        $this->theme_template_directory = 'sptv';
        $this->plugin_directory = SPTV_PLUGIN_DIR;
        $this->plugin_template_directory = 'default-templates';
      }
    }
  }
  
?>
