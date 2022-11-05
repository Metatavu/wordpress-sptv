<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;
  
  $serviceChannelsWithLinks = array_filter($serviceChannels, function ($serviceChannel) use ($data) {
    $serviceChannelId = $serviceChannel["id"];
    return isset($data->relatedServiceChannelLinks[$serviceChannelId]);
  });

  if ($serviceChannelsWithLinks && count($serviceChannelsWithLinks) > 0) {
    echo "<h3>Toimipisteet</h3>";

    foreach ($serviceChannelsWithLinks as $serviceChannel) {
      $serviceChannelId = $serviceChannel["id"];      
      $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
      $link = $data->relatedServiceChannelLinks[$serviceChannelId];
      echo "<a href='$link'><p>$name</p></a>";
    }
  }
?>