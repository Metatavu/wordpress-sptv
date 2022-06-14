<?php
  require_once(__DIR__ . '/../common.php');

  $serviceChannel = $data->serviceChannel;
  $addresses = $data->serviceChannel["addresses"];
  if (!$addresses) {
    return;
  }

  $services = $serviceChannel["services"];
  $serviceId = !empty($services) ? $services[0]["service"]["id"] : null;
  $serviceChannelId = $serviceChannel["id"];
  $blockHeader = __("Accessibility information", "sptv");

  foreach ($addresses as $address) {
    if ($address["type"] == "Location") {

      /**
       * PTV accessibility information
       */
      if ($address['entrances']) {
        echo "<div class='accessibility-sentences'>";
        echo "<h2>$blockHeader</h2>";
        foreach ($address['entrances'] as $entrance) {
          foreach ($entrance['accessibilitySentences'] as $accessibilitySentence) {
            $sentenceGroups = $accessibilitySentence['sentenceGroup'];
            $key = array_search('fi', array_column($sentenceGroups, 'language'));
            echo "<div class='accessibility-sentence'>";
            echo "<button type='button' class='button-text closed'>" . $sentenceGroups[$key]['value'] . "</button>";
            echo "<div class='content'>";

            foreach ($accessibilitySentence['sentences'] as $sentences) {
              $sentence = $sentences['sentence'];
              $key = array_search('fi', array_column($sentence, 'language'));
              echo "<p>" . $sentence[$key]['value'] . "</p>";
            }

            echo "</div>";
            echo "</div>";
          }
        }
        echo "</div>";
      }
    }
    
  }

?>
