<?php
  namespace Metatavu\SPTV\Wordpress\PageDrafts;
  use Metatavu\SPTV\Wordpress\Settings\Settings;

  if (!defined('ABSPATH')) { 
    exit;
  }

  add_action('init', function () {
    var_dump(getNewIndexItems('v11-service'));
    die();
    scheduleDraftHook('draft_hook', 'service-template');
  });

  add_filter('cron_schedules', function ($schedules) {
    $schedules['template_interval'] = [
        'interval' => 5
    ];
    return $schedules;
  });

  add_action('draft_hook', function () {
    $new_index_items = getNewIndexItems('v11-service');
  });

  add_action('location_draft_hook', function () {});

  function scheduleDraftHook($hook, $template_type) {
    $template = Settings::getValue($template_type);
  
    $hook_scheduled = wp_next_scheduled($hook);

    if ($template == '') {
      wp_clear_scheduled_hook($hook);
    } else if (!$draft_hook_scheduled) {
      wp_schedule_event(time(), 'template_interval', $hook);
    }
  }

  function getOrganizationIds() {
    $options = get_option(SPTV_SETTINGS_OPTION);
  
    if ($options) {
      $searchValue = 'ptv';
      $allowed=array_filter(
        array_keys($options), function($key) use ($searchValue ) {
          return stristr($key, $searchValue ) ;
        });
  
      $ids_object = array_intersect_key($options,array_flip($allowed));

      $organization_ids = [];
      foreach($ids_object as $key => $value) {
        array_push($organization_ids, $value);
      }

      return $organization_ids;
    } else {
      return [];
    }
  }

  function getNewIndexItems($index_name) {
    $address = Settings::getValue('elastic-url') . '/' . $index_name . '/_search';
    $username = Settings::getValue('elastic-username');
    $password = Settings::getValue('elastic-password');

    $login = $username . ':'. $password;
    $base64_login = base64_encode($login);
    $request_body = [
      'size' => 10000,
      '_source' => false
    ];

    $result = wp_remote_get($address, [
      'headers' => [
        'Authorization' => 'Basic ' . $base64_login,
        'Content-Type' => 'application/json'
      ],
      'body' => $request_body

    ]);

    return json_decode(wp_remote_retrieve_body($result))->hits->hits;
  }
?>