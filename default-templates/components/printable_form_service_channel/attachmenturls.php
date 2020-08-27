<?php
  require_once(__DIR__ . '/../common.php');
  $serviceChannel = $data->serviceChannel;
  $attachments = $serviceChannel["attachments"];

  if (!$attachments) {
    return;
  }

  $attachment = getLocalizedUrl($attachments, $data->language);
  if ($attachment) {
    $url = $attachment["url"];
    $text = $attachment["description"] ? $attachment["description"] : $url;
    echo "<p>";
    echo $description;
    echo "<a target=\"_blank\" href=\"$url\">$text</a>";
    echo "</p>";
  }
?>