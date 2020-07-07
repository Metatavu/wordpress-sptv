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

  foreach ($addresses as $address) {
    if ($address["type"] == "Location") {
      $streetAddress = $address["streetAddress"];
      $latitude = $streetAddress["latitude"];
      $longitude = $streetAddress["longitude"];
      $mapUrl = $serviceId ? "https://www.suomi.fi/kartta/palvelupaikat/$serviceId?sl=$serviceChannelId" : null;
      $routeUrl = $latitude && $longitude ? "https://www.suomi.fi/kartta/reitit?to.lat=$latitude&to.lon=$longitude" : null;

      /**
       * PTV accessibility information
       */
      if ($address['entrances']) {
        foreach ($address['entrances'] as $entrance) {
          foreach ($entrance['accessibilitySentences'] as $accessibilitySentence) {
            $sentenceGroups = $accessibilitySentence['sentenceGroup'];
            $key = array_search('fi', array_column($sentenceGroups, 'language'));
            echo "<h4>" . $sentenceGroups[$key]['value'] . "</h4>";
            
            foreach ($accessibilitySentence['sentences'] as $sentences) {
              $sentence = $sentences['sentence'];
              $key = array_search('fi', array_column($sentence, 'language'));
              echo "<p>" . $sentence[$key]['value'] . "</p>";
            }
          }
        }
        echo "<br/>";
      }

      echo getLocalizedValue($streetAddress["street"], $data->language) . " " . $streetAddress["streetNumber"];
      echo "<br/>";
      echo $streetAddress["postalCode"] . " " . getLocalizedValue($streetAddress["postOffice"], $data->language);

      if ($mapUrl) {
        echo "<br/><a target=\"_blank\" href=\"$mapUrl\">Palvelupaikka kartalla</a>";
      }

      if ($routeUrl) {
        echo "<br/><a target=\"_blank\" href=\"$routeUrl\">Näytä reitti tänne</a>";
      }
    }
  }

?>
