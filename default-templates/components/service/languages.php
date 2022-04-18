<?php
  require_once(__DIR__ . '/../common.php');
  $languages = $data->service["languages"];
  echo "<h3>Kielet, joilla palvelu on saatavilla</h3>";
  foreach ($languages as $language) {
    $localizedLanguage = getLocalizedLanguageName($language);
    echo "<span>$localizedLanguage</span><br/>";
  }
?>