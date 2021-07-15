<?php
  require_once(__DIR__ . '/../common.php');
  $serviceChannel = $data->serviceChannel;
  $channelUrls = $serviceChannel["channelUrls"];

  if (!$channelUrls) {
    return;
  }

  $webPage = getLocalizedItem($channelUrls, $data->language);
  $name = getLocalizedValue($data->serviceChannel["serviceChannelNames"], $data->language, "Name");
  $description = nl2p(getLocalizedValue($data->serviceChannel["serviceChannelDescriptions"], $data->language, "Description"));
  
  if ($webPage) {
    $url = $webPage["value"];
    $text = $name ? $name : $url;
    echo "<p>";
    echo "<a target=\"_blank\" href=\"$url\">$text</a>";
    echo "</p>";
    echo "<p>";
    echo $description;
    echo "</p>";
  }
?>
