<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h2>Lomakkeet</h2>";

    foreach ($serviceChannels as $serviceChannel) {
      $name = count($serviceChannel["serviceChannelNames"]) > 0 ? $serviceChannel["serviceChannelNames"][0]["value"] : "";
      $link = count($serviceChannel["webPages"]) > 0 ? $serviceChannel["webPages"][0]["url"] : "";
      $summary = count($serviceChannel["serviceChannelDescriptions"]) > 0 ? $serviceChannel["serviceChannelDescriptions"][0]["value"] : "";

      echo "<a href='$link'>$name</a>";
      echo "<p>$summary</p>";
    }
  }
?>