<?php
  require_once(__DIR__ . '/../common.php');

  $serviceChannel = $data->serviceChannel;
  $emails = $serviceChannel ["emails"];

  if (!$emails) {
    return;
  }

  echo "<p>";
  echo getLocalizedValue($emails, $data->language);
  echo "</p>";

?>
