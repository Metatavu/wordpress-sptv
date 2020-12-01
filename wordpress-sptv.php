<?php
/*
 * Created on Jul 19, 2019
 * Plugin Name: SPTV
 * Description: Wordpress plugin to searchable PTV service
 * Version: 1.0.7.4
 * Author: Metatavu Oy
 */

  defined ( 'ABSPATH' ) || die ( 'No script kiddies please!' );

  if (!defined('SPTV_PLUGIN_DIR')) {
    define('SPTV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
  }

  require_once( __DIR__ . '/settings/settings.php');
  require_once( __DIR__ . '/gutenberg/gutenberg.php');
  require_once( __DIR__ . '/rest/rest.php');
  
  add_action('plugins_loaded', function() {
    load_plugin_textdomain('sptv', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
  });
?>
