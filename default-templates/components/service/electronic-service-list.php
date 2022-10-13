<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Verkkoasiointi</h3>";

    foreach ($serviceChannels as $serviceChannel) {
      if (count($serviceChannel["serviceChannelNames"]) > 0) {
        $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
        $link = getLocalizedValue($serviceChannel["webPages"], $data->language);
        echo "<a href='$link'>$name</a>";
      }

      if (count($serviceChannel["serviceChannelDescriptions"]) > 0) {
        echo "<p>";
        echo getLocalizedValue($serviceChannel["serviceChannelDescriptions"], $data->language, "Summary");
        echo "</p>";
      }
    }
  }
?>