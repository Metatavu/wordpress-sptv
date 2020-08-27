<?php
  require_once(__DIR__ . '/../common.php');

  $serviceChannel = $data->serviceChannel;
  $phoneNumbers = $serviceChannel["phoneNumbers"];


  if (!$phoneNumbers) {
    return;
  }
  
  foreach ($phoneNumbers as $phoneNumber) {
    $additionalInformation = $phoneNumber["additionalInformation"];
    $prefixNumber = $phoneNumber["prefixNumber"];
    $number = $phoneNumber["number"];
    $chargeInfo = "";

    switch ($phoneNumber["serviceChargeType"]) {
      case "Chargeable":
        $chargeInfo = __("Chargeable", "sptv");
    }

    echo "<p>";

    if ($additionalInformation) {
      echo "<b>$additionalInformation<br/></b>";
    }

    echo implode(" ", [$prefixNumber, $number, $chargeInfo]);

    echo "</p>";
  }

?>
