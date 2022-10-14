<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Toimipisteet</h3>";

    foreach ($serviceChannels as $serviceChannel) {
      $serviceChannelId = $serviceChannel["id"];
      
      if (isset($data->relatedServiceChannelLinks[$serviceChannelId])) {
        $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
        $link = $data->relatedServiceChannelLinks[$serviceChannelId];
        echo "<a href='$link'><p>$name</p></a>";
      }
    }
  }
?>