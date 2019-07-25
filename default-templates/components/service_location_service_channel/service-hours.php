<?php
  require_once(__DIR__ . '/../common.php');

  $serviceChannel = $data->serviceChannel;

  foreach ($serviceChannel["serviceHours"] as $serviceHour) {
    $additionalInformation = getLocalizedValue($serviceHour["additionalInformation"], $data->language);
    $openingHours = $serviceHour["openingHour"];
    
    echo "<p>$additionalInformation</p>";
    echo formatOpeningHours($openingHours);
  }

?>
