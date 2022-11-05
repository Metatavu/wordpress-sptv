<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Verkkoasiointi</h3>";
    foreach ($serviceChannels as $serviceChannel) {
      if (count($serviceChannel["serviceChannelNames"]) > 0 && $serviceChannel["webPages"] > 0) {
        $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
        $webPage = getLocalizedItem($serviceChannel["webPages"], $data->language);

        if (isset($webPage)) {
          $url = $webPage["url"];
          if (isset($url)) {
            echo "<a href='$url'>$name</a>";
          }
        }
      }

      if (count($serviceChannel["serviceChannelDescriptions"]) > 0) {
        echo "<p>";
        echo getLocalizedValue($serviceChannel["serviceChannelDescriptions"], $data->language, "Summary");
        echo "</p>";
      }
    }
  }
?>