<?php
  require_once(__DIR__ . '/../common.php');
  $serviceChannel = $data->serviceChannel;
  $webPages = $serviceChannel["webPages"];

  if (!$webPages) {
    return;
  }

  $webPage = getLocalizedUrl($webPages, $data->language);
  $name = getLocalizedValue($data->serviceChannel["serviceChannelNames"], $data->language, "Name");
  $description = nl2p(getLocalizedValue($data->serviceChannel["serviceChannelDescriptions"], $data->language, "Description"));

  if ($webPage) {
    $url = $webPage["url"];
    $text = $name ? $name : $url;
    echo "<p>";
    echo "<a target=\"_blank\" href=\"$url\">$text</a>";
    echo "</p>";
    echo "<p>";
    echo $description;
    echo "</p>";
  }
?>