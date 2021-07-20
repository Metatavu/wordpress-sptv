<?php
  require_once(__DIR__ . '/../common.php');
  echo "<p>";
  echo nl2p(getLocalizedValue($data->organization["organizationDescriptions"], $data->language, "Description"));
  echo "</p>";
?>