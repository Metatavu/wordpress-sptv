<?php
  namespace Metatavu\SPTV\Wordpress\PageDrafts;
  use Metatavu\SPTV\Wordpress\Settings\Settings;

  if (!defined('ABSPATH')) { 
    exit;
  }

  add_action('init', function () {
    $serviceTemplate = Settings::getValue('service-template');
    $serviceLocationTemplate = Settings::getValue('service-location-template');
  
    $serviceTemplateHookScheduled = wp_next_scheduled('service_template_hook');
    $serviceLocationTemplateHookScheduled = wp_next_scheduled('service_location_template_hook');
  
    if (!empty($serviceTemplate) && !$serviceTemplateHookScheduled) {
      wp_schedule_event(time(), 'template_interval', 'service_template_hook');
    }

    if (!empty($serviceLocationTemplate) && !$serviceLocationTemplateHookScheduled) {
      wp_schedule_event(time(), 'template_interval', 'service_location_template_hook');
    }
  });

  add_filter('cron_schedules', function ($schedules) {
    $schedules['template_interval'] = [
        'interval' => 1
    ];
    return $schedules;
  });

  add_action('service_template_hook', function () {
    error_log(Settings::getValue('service-template'));
  });

  add_action('service_location_template_hook', function () {
    error_log(Settings::getValue('service-location-template'));
  });
?>