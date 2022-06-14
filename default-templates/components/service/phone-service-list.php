<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Yhteystiedot</h3>";

    foreach ($serviceChannels as $serviceChannel) {
      $email = count($serviceChannel["supportEmails"]) > 0 ? $serviceChannel["supportEmails"][0]["value"] : "";
      $phoneInfo = count($serviceChannel["phoneNumbers"]) > 0 ? $serviceChannel["phoneNumbers"][0]["additionalInformation"] : "";
      $phone = count($serviceChannel["phoneNumbers"]) > 0 ? $serviceChannel["phoneNumbers"][0]["number"] : "";
      $phoneCharge = count($serviceChannel["phoneNumbers"]) > 0 ? $serviceChannel["phoneNumbers"][0]["chargeDescription"] : "";

      if ($email) {
        echo "<a href='mailto:$email'><p>$email</p></a>";
      }
      
      if ($phone) {
        echo "<p>$phone<br />$phoneCharge<br/>$phoneInfo</p>";
      }
    }
    
  }
?>