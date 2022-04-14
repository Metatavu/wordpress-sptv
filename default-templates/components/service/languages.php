<?php
  require_once(__DIR__ . '/../common.php');
  $languages = $data->service["languages"];
  echo "<h3>Kielet, joilla palvelu on saatavilla</h3>";
  foreach ($languages as $language) {
    echo "<span>$language</span><br/>";
  }
?>