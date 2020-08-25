<?php
  require_once(__DIR__ . '/../common.php');
  
  echo "<p>";
  echo getLocalizedValue($data->service["serviceDescriptions"], $data->language, "UserInstruction");
  echo "</p>";
?>