<?php
  require_once(__DIR__ . '/../common.php');

  $service = $data->service;
  $serviceChannels = $data->serviceChannels;

  if ($serviceChannels) {
    echo "<h3>Puhelinnumerot</h3>";

    foreach ($serviceChannels as $serviceChannel) {
      $name = getLocalizedValue($serviceChannel["serviceChannelNames"], $data->language);
      $description = getLocalizedValue($serviceChannel["serviceChannelDescriptions"], $data->language, "Description");

      if ($name) {
        echo "<h4>" . $name . "</h4>";
      }

      if ($description) {
        echo "<p>" . nl2br($description) . "</p>";
      }      

      foreach ($serviceChannel["phoneNumbers"] as $phoneNumber) {
        $additionalInformation = $phoneNumber["additionalInformation"];
        $prefixNumber = $phoneNumber["prefixNumber"];
        $number = $phoneNumber["number"];
        $chargeInfo = "";
    
        switch ($phoneNumber["serviceChargeType"]) {
          case "Chargeable":
            $chargeInfo = "paikallisverkkomaksu (pvm), matkapuhelinmaksu (mpm), ulkomaanpuhelumaksu";
        }
    
        echo "<p>";
        echo implode(" ", [$prefixNumber, $number]);

        if ($chargeInfo) {
          echo "<small>";
          echo "<br/>";
          echo $chargeInfo;

          if ($phoneNumber["chargeDescription"] && $phoneNumber["chargeDescription"] != $chargeInfo) {
            echo "<br/>";
            echo $phoneNumber["chargeDescription"];
          }

          echo "</small>";
        }
        
        echo "</p>";
      }

      echo formatServiceHours($serviceChannel["serviceHours"], $data->language);
    }
    
  }
?>