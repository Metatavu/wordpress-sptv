<?php
  require_once(__DIR__ . '/../common.php');
  
  echo "<p>";
  echo getLocalizedValue($data->service["requirements"], $data->language);
  echo "<p>";
?>