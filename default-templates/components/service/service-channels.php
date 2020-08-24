<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $service["serviceChannels"];

  if (!$serviceChannels) {
    return;
  }

  foreach ($serviceChannels as $serviceChannelChild) {
    
    $serviceChannel = $serviceChannelChild["serviceChannel"];
    $serviceChannelName = $serviceChannel["name"];

    echo "<p>";
    echo $serviceChannelName;
    echo "</p>";
  }

?>