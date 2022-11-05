<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Lomakkeet</h3>";

    foreach ($serviceChannels as $serviceChannel) {
      if (count($serviceChannel["serviceChannelNames"]) > 0 && $serviceChannel["channelUrls"] > 0) {
        $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
        $channelUrl = getLocalizedValue($serviceChannel["channelUrls"], $data->language);

        if (isset($channelUrl)) {
          echo "<a href='$channelUrl'>$name</a>";
        }
      }

      if (count($serviceChannel["serviceChannelDescriptions"]) > 0) {
        $summary = getLocalizedValue($serviceChannel["serviceChannelDescriptions"], $data->language);
        echo "<p>$summary</p>";
      }
    }
  }
?>