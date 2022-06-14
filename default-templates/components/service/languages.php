<?php
  require_once(__DIR__ . '/../common.php');
  $languages = $data->service["languages"];
  $languageText = __("AvailableLanguages", "sptv");
  echo "<h2>$languageText</h2>";
  foreach ($languages as $language) {
    $localizedLanguage = getLocalizedLanguageName($language);
    echo "<span>$localizedLanguage</span><br/>";
  }
?>