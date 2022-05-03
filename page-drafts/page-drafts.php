<?php
  namespace Metatavu\SPTV\Wordpress\PageDrafts;
  use Metatavu\SPTV\Wordpress\Settings\Settings;

  if (!defined('ABSPATH')) { 
    exit;
  }


  if (!class_exists( '\Metatavu\SPTV\Wordpress\PageDrafts\PageDrafts' )) {
    class PageDrafts {
      public function __construct() {
        add_action('init', function () {
          $this->scheduleDraftHook('draft_hook', 'service-template');
          $this->scheduleDraftHook('location_draft_hook', 'service-location-template');
        });
      
        add_filter('cron_schedules', function ($schedules) {
          $schedules['template_interval'] = [
              'interval' => 900
          ];
          return $schedules;
        });
      
        add_action('draft_hook', function () {
          $lastSyncTime = get_option('sptv-last-template-sync-time');
          $newSyncTime = gmdate("Y-m-d\TH:i:s\Z");
          $newIndexItems = $this->getNewIndexItems('v11-service', 'organizationIds', $lastSyncTime);
          if (is_array($newIndexItems)) {
            $this->createDrafts($newIndexItems, 'service-template', $newSyncTime);
          }
      
        });
      
        add_action('location_draft_hook', function () {
          $lastSyncTime = get_option('sptv-last-location-template-sync-time');
          $newSyncTime = gmdate("Y-m-d\TH:i:s\Z");
          $newIndexItems = $this->getNewIndexItems('v11-servicelocation-service-channel', 'organizationId', $lastSyncTime);
          if (is_array($newIndexItems)) {
            $this->createDrafts($newIndexItems, 'service-location-template', $newSyncTime);
          }
        });
      }

      /**
       * Schedules a draft hook
       * @param hook hook
       * @param templateType template type 
       */
      function scheduleDraftHook($hook, $templateType) {
        $template = Settings::getValue($templateType);
      
        $hookScheduled = wp_next_scheduled($hook);

        if (empty($template)) {
          wp_clear_scheduled_hook($hook);
        } else if (!$draftHookScheduled) {
          wp_schedule_event(time(), 'template_interval', $hook);
        }
      }

      /**
       * Returns new items from the Elastic Search index
       * 
       * @param indexName index name
       * @param organizationFieldName organization field name
       * @param lastSyncTime last sync time
       */
      function getNewIndexItems($indexName, $organizationFieldName, $lastSyncTime) {
        $address = Settings::getValue('elastic-url') . '/' . $indexName . '/_search';
        $username = Settings::getValue('elastic-username');
        $password = Settings::getValue('elastic-password');

        $login = $username . ':'. $password;
        $base64Login = base64_encode($login);
        $requestBody = [
          'size' => 10000,
          'query' => [
            'bool' => [
              'must' => [
                [
                  'term' => [
                    $organizationFieldName => Settings::getOrganizationIds()[0]
                  ]
                ], 
                [
                  'range' => [
                    'creationDate'=> [
                      'gte' => empty($lastSyncTime) ? null : $lastSyncTime
                    ]
                  ]
                ]
              ]
            ]     
          ]
        ];

        $result = wp_remote_post($address, [
          'headers' => [
            'Authorization' => 'Basic ' . $base64Login,
            'Content-Type' => 'application/json'
          ],
          'body' => json_encode($requestBody)
        ]);

        return json_decode(wp_remote_retrieve_body($result))->hits->hits;
      }

      /**
       * Resolves a PTV-type from a template type
       * @param templateType template type
       */
      function resolvePtvType($templateType) {
        if ($templateType == 'service-template') {
          return 'service';
        } else if ($templateType == 'service-location-template') {
          return 'service_location';
        } else {
          return null;
        }
      }

      /**
       * Resolves the post title field name from a template type
       * 
       * @param templateType template type resolve from
       */
      function resolvePostTitleField ($templateType) {
        if ($templateType == 'service-template') {
          return 'serviceNames_fi';
        } else if ($templateType == 'service-location-template') {
          return 'serviceChannelNames_fi';
        } else {
          return null;
        }
      }

      /**
       * Creates a draft from a template
       * 
       * @param template_html template html
       * @param item_id item id
       * @param ptv_type ptv type
       * @param post_title post title
       */
      function createDraft($template_html, $item_id, $ptv_type, $post_title) {
        $posts = get_posts([
          'post_type'=> 'page',
          'meta_key'=> 'ptv_id',
          'meta_value'=> $item_id,
          'post_status' => 'any'
        ]);

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
            add_post_meta($result, 'ptv_type', $ptv_type);
          }
        }
      }

      /**
       * Creates new drafts from new index items
       * 
       * @param new_index_items new index items to use
       * @param template_type template type to use
       * @param new_sync_time new sync time
       */
      function createDrafts($new_index_items, $template_type, $new_sync_time) {
        $template = Settings::getValue($template_type);
        if (!empty($template)) {
          $post = get_post($template);
          if ($post) {
            $post_html = $post->post_content;

            foreach ($new_index_items as $item) {
              $ptv_type = $this->resolvePtvType($template_type);
              $name_field = $this->resolvePostTitleField($template_type);

              if ($name_field && $ptv_type) {
                $this->createDraft($post_html, $item->_id, $ptv_type, $item->_source->{$name_field});
              }
            }

            if ($template_type == 'service-template') {
              update_option('sptv-last-template-sync-time', $new_sync_time);
            } 
            
            if ($template_type == 'service-location-template') {
              update_option('sptv-last-location-template-sync-time', $new_sync_time);
            }
          }
        }
      }
    }
  }

  new PageDrafts();
?>