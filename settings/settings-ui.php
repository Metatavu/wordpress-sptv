<?php
  namespace Metatavu\SPTV\Wordpress\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  define("SPTV_SETTINGS_OPTION", 'sptv');
  define("SPTV_SETTINGS_GROUP", 'sptv');
  define("SPTV_SETTINGS_PAGE", 'sptv');
  
  if (!class_exists( 'Metatavu\SPTV\Wordpress\SettingsUI' ) ) {

    class SettingsUI {

      public function __construct() {
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'adminMenu'));
      }

      public function adminMenu() {
        add_options_page (__( "SPTV Settings", 'sptv' ), __( "SPTV", 'sptv' ), 'manage_options', SPTV_SETTINGS_OPTION, [$this, 'settingsPage']);
      }

      public function adminInit() {
        register_setting(SPTV_SETTINGS_GROUP, SPTV_SETTINGS_PAGE);
        add_settings_section('elastic', __( "Elasticsearch Settings", 'sptv' ), null, SPTV_SETTINGS_PAGE);
        $this->addOption('elastic', 'url', 'elastic-url', __( "URL", 'sptv'));
        $this->addOption('elastic', 'text', 'elastic-username', __( "Username", 'sptv' ));
        $this->addOption('elastic', 'text', 'elastic-password', __( "Password", 'sptv' ));
        add_settings_section('ptv', __( "PTV Settings", 'sptv' ), null, SPTV_SETTINGS_PAGE);
        $this->addOption('ptv', 'text', 'ptv-organization-id', __( "Organization Id", 'sptv' ));
      }

      private function addOption($group, $type, $name, $title) {
        add_settings_field($name, $title, [$this, 'createFieldUI'], SPTV_SETTINGS_PAGE, $group, [
          'name' => $name, 
          'type' => $type
        ]);
      }

      public function createFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);
        echo "<input id='$name' name='" . SPTV_SETTINGS_PAGE . "[$name]' size='42' type='$type' value='$value' />";
      }

      public function settingsPage() {
        if (!current_user_can('manage_options')) {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        echo '<div class="wrap">';
        echo "<h2>" . __( "SPTV", 'sptv') . "</h2>";
        echo '<form action="options.php" method="POST">';
        settings_fields(SPTV_SETTINGS_GROUP);
        do_settings_sections(SPTV_SETTINGS_PAGE);
        submit_button();
        echo "</form>";
        echo "</div>";
      }
    }

  }
  
  if (is_admin()) {
    $settingsUI = new SettingsUI();
  }

?>