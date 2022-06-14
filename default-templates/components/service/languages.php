<?php
  require_once(__DIR__ . '/../common.php');
  $languages = $data->service["languages"];
  $languageText = __("AvailableLanguages", "sptv");
  echo "<h3>$languageText</h3>";
  foreach ($languages as $language) {
    $localizedLanguage = getLocalizedLanguageName($language);
    echo "<span>$localizedLanguage</span><br/>";
  }
?>