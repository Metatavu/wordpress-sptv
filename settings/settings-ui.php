<?php
  namespace Metatavu\SPTV\Wordpress\Settings;
  
  if (!defined('ABSPATH')) { 
    exit;
  }
  
  if (!class_exists( 'Metatavu\SPTV\Wordpress\Settings\SettingsUI' ) ) {
    class SettingsUI {
      
      public function __construct() {
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'adminMenu'));
      }

      public function adminMenu() {
        add_options_page (__( "SPTV Settings", 'sptv' ), __( "SPTV", 'sptv' ), 'manage_options', SPTV_SETTINGS_OPTION, [$this, 'settingsPage']);
      }

      public function adminInit() {
        $options = get_option(SPTV_SETTINGS_OPTION);
  
        if($options) {
          /**
           * parse organization ids from options
           */
          $searchValue = 'ptv';
          $allowed=array_filter(
            array_keys($options), function($key) use ($searchValue ) {
              return stristr($key, $searchValue ) ;
            });

          $organizationIds = array_intersect_key($options,array_flip($allowed));
        } else {
            $id = uniqid();
            $organizationIds["ptv-organization-id:$id"] = "";
          }


        if(array_key_exists('button2', $_POST)) {
          $id=$_POST["button2"];
          unset($organizationIds[$id]);
        }

        if(array_key_exists('button1', $_POST)) {
          $id = uniqid();
          $organizationIds["ptv-organization-id:$id"] = "";
        }

        register_setting(SPTV_SETTINGS_GROUP, SPTV_SETTINGS_PAGE);
        add_settings_section('elastic', __( "Elasticsearch Settings", 'sptv' ), null, SPTV_SETTINGS_PAGE);
        $this->addOption('elastic', 'url', 'elastic-url', __( "URL", 'sptv'));
        $this->addOption('elastic', 'text', 'elastic-username', __( "Username", 'sptv' ));
        $this->addOption('elastic', 'text', 'elastic-password', __( "Password", 'sptv' ));
        $this->addVersionDropDown();
        add_settings_section('ptv', __( "PTV Settings", 'sptv' ), null, SPTV_SETTINGS_PAGE);
        foreach($organizationIds as $key => $value) {
          $this->addOrganizationOption('ptv', 'text', $key, __( "Organization Id", 'sptv' ), $key);
        }

        $args = [
          'post_type' => 'wp_block'
        ];

        $page_templates = get_posts($args);
        $this->addTemplateDropdown('service-template', __( "Service template", 'sptv' ), $page_templates);
        $this->addTemplateDropdown('service-location-template', __( "Service location template", 'sptv' ), $page_templates);
      }

      private function addOption($group, $type, $name, $title) {
        add_settings_field($name, $title, [$this, 'createFieldUI'], SPTV_SETTINGS_PAGE, $group, [
          'name' => $name, 
          'type' => $type
        ]);
      }

      /**
       * Adds a version dropdown
       */
      private function addVersionDropdown() {
        add_settings_field('version', __( "PTV-versio", 'sptv' ), [$this, 'createVersionDropdownUI'], SPTV_SETTINGS_PAGE, 'elastic', [
          'name' => 'version', 
          'type' => 'text'
        ]);
      }

      /**
       * Adds a template dropdown
       * 
       * @param name setting name
       * @param title setting title
       * @param templates template options to render
       */
      private function addTemplateDropdown($name, $title, $templates) {
        add_settings_field($name, $title, function ($opts) use($templates) {
          $name = $opts['name'];
          $type = $opts['type'];
          $value = Settings::getValue($name);
          $noTemplate = __( "No template", 'sptv' );

          echo "<select id='$name' name='" . SPTV_SETTINGS_PAGE . "[$name]' type='$type' value='$value' >";
          echo '<option value="" '.((empty($value))?'selected="selected"':"").'> ' . $noTemplate . '</option>';

          foreach ($templates as $template) {
            $template_id = $template->ID;
            $template_name = $template->post_name;
            echo '<option value="' . $template_id . '" '.(($value==$template_id)?'selected="selected"':"").'> ' . $template_name . '</option>';
          }

          echo "</select >";
        }, SPTV_SETTINGS_PAGE, 'ptv', [
          'name' => $name, 
          'type' => 'text'
        ]);
      }

      /**
       * Creates a dropdown for version
       * 
       * @param opts dropdown parameters
       */
      public function createVersionDropDownUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);
        echo "<select id='$name' name='" . SPTV_SETTINGS_PAGE . "[$name]' type='$type' value='$value' >";
        echo '<option value="v11" '.(($value=='v11')?'selected="selected"':"").'>v11</option>';
        echo "</select >";
      }

      public function createFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);
        echo "<input id='$name' name='" . SPTV_SETTINGS_PAGE . "[$name]' size='42' type='$type' value='$value' />";
      }

      private function addOrganizationOption($group, $type, $name, $title, $key) {
        add_settings_field($name, $title, [$this, 'createOrganizationFieldUI'], SPTV_SETTINGS_PAGE, $group, [
          'name' => $name, 
          'type' => $type,
          'key' => $key
        ]);
      }

      public function createOrganizationFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $key = $opts['key'];
        $value = Settings::getValue($name);
        echo '<div style="display:flex">';
        echo "<input id='$name' name='" . SPTV_SETTINGS_PAGE . "[$name]' size='42' type='$type' value='$value' />";
        echo '<form method="POST">';
        echo "<button type='submit' name='button2'class='button' value=$key>Delete</button>";
        echo "</form>";
        echo "</div>";
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
        echo '<form method="POST">';
        echo "<input type='submit', name='button1', class='button' value='Add new organization' />";
        echo "</form>";
        echo "</div>";
      }
    }
  }
  
  if (is_admin()) {
    $settingsUI = new SettingsUI();
  }

?>