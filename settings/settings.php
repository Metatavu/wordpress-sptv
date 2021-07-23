<?php
  namespace Metatavu\SPTV\Wordpress\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  require_once('settings-ui.php');  
  
  define("SPTV_SETTINGS_OPTION", 'sptv');
  
  if (!class_exists( '\Metatavu\SPTV\Wordpress\Settings\Settings' ) ) {

    class Settings {

      /**
       * Returns setting value
       * 
       * @param string $name setting name
       * @return string setting value
       */
      public static function getValue($name) {
        $options = get_option(SPTV_SETTINGS_OPTION);
        if ($options) {
          return $options[$name];
        }

        return null;
      }
      

      /**
       * Returns organization IDs
       */
      public static function getOrganizationIds() {
        $options = get_option(SPTV_SETTINGS_OPTION);
        if ($options) {
          $searchValue = 'ptv';
          $allowed=array_filter(
            array_keys($options), function($key) use ($searchValue ) {
              return stristr($key, $searchValue ) ;
            });
  
          return array_intersect_key($options,array_flip($allowed));
        }
        return [];
      }
      
      /**
       * Sets a value for settings
       * 
       * @param string $name setting name
       * @param string $value setting value
       */
      public static function setValue($name, $value) {
        $options = get_option(SPTV_SETTINGS_OPTION);
        if (!$options) {
          $options = [];
        } 
        
        $options[$name] = $value;
        
        update_option(SPTV_SETTINGS_OPTION, $options);
      }
      
    }

  }
  

?>