<?php
  require_once(__DIR__ . '/../common.php');
  echo "<h3>";
  echo getLocalizedValue($data->organization["organizationNames"], $data->language, "Name");
  echo "</h3>";
?>