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
                    $organizationFieldName => array_values(Settings::getOrganizationIds())[0]
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
       * @param templateHtml template html
       * @param itemId item id
       * @param ptvType ptv type
       * @param postTitle post title
       */
      function createDraft($templateHtml, $itemId, $ptvType, $postTitle) {
        $posts = get_posts([
          'post_type'=> 'page',
          'meta_key'=> 'ptv_id',
          'meta_value'=> $itemId,
          'post_status' => 'any'
        ]);

        $postCount = count($posts);

        if ($postCount == 0) {
          $regExp = '/"id":"[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/';
          $draftHtml = preg_replace($regExp, '"id":"' . $itemId, $templateHtml);
          $draftData = [
            'post_content' => $draftHtml,
            'post_type' => 'page',
            'post_title' => $postTitle
          ];

          $result = wp_insert_post($draftData);
          if ($result != 0) {
            add_post_meta($result, 'ptv_id', $itemId);
            add_post_meta($result, 'ptv_type', $ptvType);
          }
        }
      }

      /**
       * Creates new drafts from new index items
       * 
       * @param newIndexItems new index items to use
       * @param templateType template type to use
       * @param newSyncTime new sync time
       */
      function createDrafts($newIndexItems, $templateType, $newSyncTime) {
        $template = Settings::getValue($templateType);
        if (!empty($template)) {
          $post = get_post($template);
          if ($post) {
            $postHtml = $post->post_content;

            foreach ($newIndexItems as $item) {
              $ptvType = $this->resolvePtvType($templateType);
              $nameField = $this->resolvePostTitleField($templateType);

              if ($nameField && $ptvType) {
                $this->createDraft($postHtml, $item->_id, $ptvType, $item->_source->{$nameField});
              }
            }

            if ($templateType == 'service-template') {
              update_option('sptv-last-template-sync-time', $newSyncTime);
            } 
            
            if ($templateType == 'service-location-template') {
              update_option('sptv-last-location-template-sync-time', $newSyncTime);
            }
          }
        }
      }
    }
  }

  new PageDrafts();
?>