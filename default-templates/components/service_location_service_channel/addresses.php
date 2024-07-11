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
      if ($address["subType"] == "Other") {
        if (!isset($address["otherAddress"])) {
          continue;
        }

        $otherAddress = $address["otherAddress"];
        if (!isset($otherAddress["additionalInformation"])) {
          continue;
        }
        
        $additionalInformation = $otherAddress["additionalInformation"];
       
        echo "<div>";
        echo "<strong>" . __("Visit address", "sptv") . "</strong>";
        echo "<br/>";
        echo getLocalizedValue($additionalInformation, $data->language);
        echo "</div>";
        echo "<br/>";
      } else {
        $streetAddress = $address["streetAddress"];
        $latitude = $streetAddress["latitude"];
        $longitude = $streetAddress["longitude"];
        $mapUrl = $serviceId ? "https://www.suomi.fi/kartta/palvelupaikat/$serviceId?sl=$serviceChannelId" : null;
        $routeUrl = $latitude && $longitude ? "https://www.suomi.fi/kartta/reitit?to.lat=$latitude&to.lon=$longitude" : null;

        echo "<div>";
        echo "<strong>" . __("Visit address", "sptv") . "</strong>";
        echo "<br/>";
        echo getLocalizedValue($streetAddress["street"], $data->language) . " " . $streetAddress["streetNumber"];
        echo "<br/>";
        echo $streetAddress["postalCode"] . " " . getLocalizedValue($streetAddress["postOffice"], $data->language);

        if ($mapUrl) {
          echo "<br/><a target=\"_blank\" href=\"$mapUrl\">Palvelupaikka kartalla</a>";
        }

        if ($routeUrl) {
          echo "<br/><a target=\"_blank\" href=\"$routeUrl\">Näytä reitti tänne</a>";
        }

        echo "</div>";
        echo "<br/>";
      }      
    } else if ($address["type"] == "Postal") {
      if (isset($address["postOfficeBoxAddress"])) {
        $postOfficeBoxAddress = $address["postOfficeBoxAddress"];
        $postOfficeBox = $postOfficeBoxAddress["postOfficeBox"];
        $postalCode = $postOfficeBoxAddress["postalCode"];
        $postOffice = $postOfficeBoxAddress["postOffice"];

        echo "<div>";
        echo "<strong>" . __("Mailing address", "sptv") . "</strong>";
        echo "<br/>";
        
        echo getLocalizedValue($postOfficeBox, $data->language) . ', ' . $postalCode . " " . getLocalizedValue($postOffice, $data->language);
      }
    }
  }

?>
