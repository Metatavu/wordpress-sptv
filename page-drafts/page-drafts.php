<?php
  namespace Metatavu\SPTV\Wordpress\PageDrafts;
  use Metatavu\SPTV\Wordpress\Settings\Settings;

  if (!defined('ABSPATH')) { 
    exit;
  }

  add_action('init', function () {
    scheduleDraftHook('draft_hook', 'service-template');
    scheduleDraftHook('draft_hook', 'service-location-template');
  });

  add_filter('cron_schedules', function ($schedules) {
    $schedules['template_interval'] = [
        'interval' => 5
    ];
    return $schedules;
  });

  add_action('draft_hook', function () {
    $new_index_items = getNewIndexItems('v11-service', 'organizationIds');
    syncDrafts($new_index_items, 'service-template');
  });

  add_action('location_draft_hook', function () {
    $new_index_items = getNewIndexItems('v11-servicelocation-service-channel', 'organizationId');
    syncDrafts($new_index_items, 'service-location-template');
  });

  function scheduleDraftHook($hook, $template_type) {
    $template = Settings::getValue($template_type);
  
    $hook_scheduled = wp_next_scheduled($hook);

    if (empty($template)) {
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

  function getNewIndexItems($index_name, $organization_field_name) {
    $address = Settings::getValue('elastic-url') . '/' . $index_name . '/_search';
    $username = Settings::getValue('elastic-username');
    $password = Settings::getValue('elastic-password');

    $login = $username . ':'. $password;
    $base64_login = base64_encode($login);
    $request_body = [
      'size' => 10000,
      'query' => [
        'terms' => [
          $organization_field_name => getOrganizationIds()
        ]
      ]
    ];

    $result = wp_remote_post($address, [
      'headers' => [
        'Authorization' => 'Basic ' . $base64_login,
        'Content-Type' => 'application/json'
      ],
      'body' => json_encode($request_body)
    ]);

    return json_decode(wp_remote_retrieve_body($result))->hits->hits;
  }

  function syncDrafts($new_index_items, $template_type) {
    $template = Settings::getValue($template_type);
    if (!empty($template)) {
      $post = get_post($template);
      if ($post) {
        $post_html = $post->post_content;
        foreach ($new_index_items as $item) {
          $meta_type = resolve_meta_type($template_type);
          $name_field = resolve_name_field($template_type);

          if ($name_field && $meta_type) {
            syncDraft($post_html, $item->_id, $meta_type, $item->_source->{$name_field});
          }
        }
      }
    }
  }

  function resolve_meta_type($template_type) {
    if ($template_type == 'service-template') {
      return 'service';
    } else if ($template_type == 'service-location-template') {
      return 'service_location';
    } else {
      return null;
    }
  }

  function resolve_name_field ($template_type) {
    if ($template_type == 'service-template') {
      return 'serviceNames_fi';
    } else if ($template_type == 'service-location-template') {
      return 'serviceChannelNames_fi';
    } else {
      return null;
    }
  }

  function syncDraft($template_html, $item_id, $meta_type, $post_title) {
    $args = [
      'post_type'=> 'page',
      'meta_key'=> 'ptv_id',
      'meta_value'=> $item_id,
      'post_status' => 'any'
    ];
    $posts = get_posts($args);
    $post_count = count($posts);

    if ($post_count == 0) {
      $reg_exp = '/"id":"[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/';
      $draft_html = preg_replace($reg_exp, '"id":"' . $item_id, $template_html);
      $draft_data = [
        'post_content' => $draft_html,
        'post_type' => 'page',
        'post_title' => $post_title
      ];

      $result = wp_insert_post($draft_data);
      if ($result != 0) {
        add_post_meta($result, 'ptv_id', $item_id);
        add_post_meta($result, 'ptv_type', $meta_type);
      }
    }
   
  }
?>