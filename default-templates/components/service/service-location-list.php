<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h2>Toimipisteet</h2>";

    foreach ($serviceChannels as $serviceChannel) {
      $name = count($serviceChannel["serviceChannelNames"]) > 0 ? $serviceChannel["serviceChannelNames"][0]["value"] : "";
      $link = count($serviceChannel["webPages"]) > 0 ? $serviceChannel["webPages"][0]["url"] : "";

      echo "<a href='$link'><p>$name</p></a>";
    }
  }
?>