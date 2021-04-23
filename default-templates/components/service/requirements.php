<?php
  require_once(__DIR__ . '/../common.php');
  
  echo "<p>";
  echo nl2p(getLocalizedValue($data->service["requirements"], $data->language));
  echo "</p>";
?>