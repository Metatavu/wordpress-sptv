<?php
  
  /** 
   * Returns localized item from an array
   * 
   * @param object[] $values array of localized values
   * @param string $language preferred language
   * @param string $type filter results by type. optional
   * @return object localized object or null
   */
  function getLocalizedItem($values, $language, $type = null) {
    if (!$values) {
      return null;
    }
    
    $filtered = array_filter($values, function($value) use($type) {
      return (!empty($value["value"]) || !empty($value["url"])) && (!$type || ($value["type"] == $type));
    });

    usort($filtered, function ($a, $b) use ($language) {
      if (!isset($a["language"])) {
        return 0;
      }
      
      return $a["language"] == $language ? -1 : 1;
    });

    return $filtered[0] ? $filtered[0] : null;
  }


  /** 
   * Returns localized value from an array
   * 
   * @param object[] $values array of localized values
   * @param string $language preferred language
   * @param string $type filter results by type. optional
   */
  function getLocalizedValue($values, $language, $type = null) {
    $item = getLocalizedItem($values, $language, $type);
    return $item ? $item["value"] : "";
  }

  /** 
   * Returns localized URL from an array
   * 
   * @param object[] $values array of localized values
   * @param string $language preferred language
   * @param string $type filter results by type. optional
   */
  function getLocalizedUrl($values, $language) {
    if (!$values) {
      return null;
    }
    
    $filtered = array_filter($values, function($value) {
      return !empty($value["url"]);
    });

    usort($filtered, function ($a, $b) {
      return $a["language"] == $language ? -1 : 1;
    });

    return $filtered[0] ? $filtered[0] : null;
  }

  /**
   * Returns localized day name
   * 
   * @param $dayName day name
   * @return localized day name
   */
  function getLocalizedDayName($dayName) {
    switch ($dayName) {
      case "Monday":
        return __("Monday", "sptv");
      case "Tuesday":
        return __("Tuesday", "sptv");
      case "Wednesday":
        return __("Wednesday", "sptv");
      case "Thursday":
        return __("Thursday", "sptv");
      case "Friday":
        return __("Friday", "sptv");
      case "Saturday":
        return __("Saturday", "sptv");
      case "Sunday":
        return __("Sunday", "sptv");
    }

    return $dayName;
  }

  /**
   * Returns localized language name
   * 
   * @param languageShorthand language shorthand
   * @return localized language name
   */
  function getLocalizedLanguageName($languageShorthand) {
    switch ($languageShorthand) {
      case "fi":
        return __("Fi", "sptv");
      case "en":
        return __("En", "sptv");
    }
  }

  /**
   * Converts wraps text into html paragraphs by line breaks
   * 
   * @param string $text text
   * @return string text as html
   */
  function nl2p($text) {
    if (!$text) {
      return "";
    }

    $lines = explode("\n", $text);
    $array = array();

    for ($i = 0; $i < count($lines); $i++) {
      $line = $lines[$i];
      switch (true) {
        case !$line:
          break;
        case preg_match("/^•/", $line) > 0:
          $content = mb_substr($line, 1);
          $before = ($i - 1 <= 0) || (preg_match("/^•/", $lines[$i - 1]) == 0) ? "<ul>" : "";
          $after = ($i + 1 >= count($lines)) || (preg_match("/^•/", $lines[$i + 1]) == 0) ? "</ul>" : "";
          array_push($array ,"$before<li>$content</li>$after");
          break;
        default:
          array_push($array , "<p>$line</p>");
      }
    }

    return implode("", $array);
  }

  /**
   * Formats service hours
   * 
   * @param object $serviceHours service hours
   * @param string $language language
   * @return string formatted service hours
   */
  function formatServiceHours($serviceHours, $language) {
    $result = '';
    if (is_array($serviceHours)) {
      $normalServiceHours = array_filter($serviceHours, function ($serviceHour) {
        return $serviceHour["serviceHourType"] == "DaysOfTheWeek";
      });

      $exceptionalServiceHours = array_filter($serviceHours, function ($serviceHour) {
        return $serviceHour["serviceHourType"] == "Exceptional";
      });


      $result .= buildServiceHoursHtml($normalServiceHours, $language);
      $result .= buildServiceHoursHtml($exceptionalServiceHours, $language);
    }

    return $result;
  }

  function buildCombinedServiceHours($combination) {
    if (count($combination) == 1) {
      return formatOpeningHours($combination[0]);
    }

    $openingHours = [
      "days" => $combination[0]["days"] . "-" . end($combination)["days"],
      "from" => $combination[0]["from"],
      "to" => $combination[0]["to"]
    ];

    return formatOpeningHours($openingHours);
  }

  /**
   * Builds service hours html
   * @param object[] $serviceHours service hours
   * @param string $language language
   * @return string service hours html
   */
  function buildServiceHoursHtml($serviceHours, $language) {
    $result = '';

    foreach ($serviceHours as $serviceHour) {
      $additionalInformation = getLocalizedValue($serviceHour["additionalInformation"], $language);
      $openingHours = $serviceHour["openingHour"];
      $filtered = array_values(array_filter($serviceHour["additionalInformation"], function ($info) use($language) {
        return $info["language"] == $language; 
      }));
      if (count($filtered) == 0) {
        $values = array_values($serviceHour["additionalInformation"]);
        if (count($values) > 0) {
          $firstValue = $values[0]["value"];
          $result .= "<strong>$firstValue</strong>";
        }
      } else {
        if (count($filtered) > 1) {
          $additionalInfoValue = $filtered[0]["value"];
          $result .= "<strong>$additionalInfoValue</strong>";
        }
      }
        
      $result .= "<p>";

      if ($serviceHour["serviceHourType"] == "Exceptional") {
        $splitDate = explode("-",$serviceHour["validFrom"]);
        $year = $splitDate[0];
        $month = $splitDate[1];
        $day = explode("T", $splitDate[2])[0];
        $result .= $day . "." . $month . "." . $year;

        if ($serviceHour["validTo"] && trim($serviceHour["validTo"]) && $serviceHour["validTo"] != $serviceHour["validFrom"]) {
          $splitDate = explode("-", $serviceHour["validTo"]);
          $year = $splitDate[0];
          $month = $splitDate[1];
          $day = explode("T", $splitDate[2])[0];
          $result .= " - " . $day . "." . $month . "." . $year;
        }

        $result .= "</br>";
      }

      if (!$serviceHour["isClosed"] && count($openingHours) == 0) {
        $result .= __("Open 24 hours.", "sptv");
      } else if ($serviceHour["isClosed"]) {
        $result .= __("Closed", "sptv");
      } else {
        $formattedHours = [];
        $openingHourIndex = 0;
        $openingHourCount = count($openingHours);

        while ($openingHourIndex < $openingHourCount) {
          $openingHour = $openingHours[$openingHourIndex];
          $subsequentOpeningHours = [ $openingHour ];

          while ($openingHourIndex + 1 < $openingHourCount && isSubsequentAndEqualOpeningHour($openingHour, $openingHours[$openingHourIndex + 1])) {
            $openingHourIndex++;
            $openingHour = $openingHours[$openingHourIndex];
            array_push($subsequentOpeningHours, $openingHour);
          }

          $translatedOpeningHours = array_map("translateOpeningHours", $subsequentOpeningHours);
          array_push($formattedHours, buildCombinedServiceHours($translatedOpeningHours));

          $openingHourIndex++;
        }
      }

      if (isset($formattedHours)) {
        foreach ($formattedHours as $formattedHour) {
          $result .= $formattedHour;
          $result .= "</br>";
        }
      }

      $result .= "</p>";
    }

    return $result;
  }

  /**
   * Format opening hours
   * 
   * @param object $translatedOpeningHour translated opening hour
   * @return string formatted opening hour
   */
  function formatOpeningHours($translatedOpeningHour) {
    $from = $translatedOpeningHour["from"];
    $to = $translatedOpeningHour["to"];
    $days = $translatedOpeningHour["days"];

    if (!empty($from) || !empty($to)) {
      return "${days} ${from}-${to}";
    } else {
      return "${days} ${from}";
    }
  }

  /**
   * Translates opening hour object.
   * 
   * @param object $openingHour openingHour
   * @return string formatted object
   */
  function translateOpeningHours($openingHour) {
    $days = isset($openingHour['dayFrom']) ? formatDayName(getLocalizedDayName($openingHour['dayFrom'])) : '';
    $from = "";
    $to = "";
    
    if (!empty($openingHour['dayTo'])) {
      $days .= '-' . formatDayName(getLocalizedDayName($openingHour['dayTo']));
    }
    
    if (isset($openingHour['from'])) {
      $from = implode('.', array_slice(explode(':', $openingHour['from']), 0, 2));
    }
    
    if (isset($openingHour['to'])) {
      $to = implode('.', array_slice(explode(':', $openingHour['to']), 0, 2));
    }

    return [
      "days" => $days,
      "from" => $from,
      "to" => $to
    ];
  }

  /**
   * Formats a day name
   * Example: Maanantai -> ma
   * 
   * @param string $dayName day name to format
   * @return string formatted day name
   */
  function formatDayName($dayName) {
    $shortened = substr($dayName, 0, 2);
    $lowerCase = strtolower($shortened);
    return $lowerCase;
  }

  /**
   * Returns whether opening hours have same times and are from subsequent days.
   * 
   * @param object $openingHour1 openingHour1
   * @param object $openingHour2 openingHour2
   * @return boolean whether opening hours have same times and are from subsequent days.
   */
  function isSubsequentAndEqualOpeningHour($openingHour1, $openingHour2) {
    if (empty($openingHour1["dayFrom"]) || empty($openingHour2["dayFrom"])) {
      return false;
    }

    $dayIndices = [ 
      "Monday" => 0, 
      "Tuesday" => 1, 
      "Wednesday" => 2, 
      "Thursday" => 3, 
      "Friday" => 4, 
      "Saturday" => 5, 
      "Sunday"  => 6 
    ];

    $dayIndex1 = $dayIndices[$openingHour1["dayFrom"]];
    $dayIndex2 = $dayIndices[$openingHour2["dayFrom"]];
    $subsequentDays = $dayIndex1 + 1 == $dayIndex2;
    $sameTimes = $openingHour1["from"] == $openingHour2["from"] && $openingHour1["to"] == $openingHour2["to"];

    return $subsequentDays && $sameTimes;
  }

?>
