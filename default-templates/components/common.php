<?php
  
  /** 
   * Returns localized item from an array
   * 
   * @param object[] $values array of localized values
   * @param string $language preferred language
   * @param string $type filter results by type. optional
   * @return object localized object or null
   */
  function getLocalizedItem($values, $language, $type = null) {
    if (!$values) {
      return null;
    }
    
    $filtered = array_filter($values, function($value) use($type) {
      return !empty($value["value"]) && (!$type || ($value["type"] == $type));
    });

    usort($filtered, function ($a, $b) {
      return $a["language"] == $language ? -1 : 1;
    });

    return $filtered[0] ? $filtered[0] : null;
  }


  /** 
   * Returns localized value from an array
   * 
   * @param object[] $values array of localized values
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
   * @param object[] $values array of localized values
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
   * @return string formatted service hours
   */
  function formatServiceHours($serviceHours) {
    $result = '';
    if (is_array($serviceHours)) {
      $normalServiceHours = array_filter($serviceHours, function ($serviceHour) {
        return $serviceHour["serviceHourType"] == "DaysOfTheWeek";
      });

      $exceptionalServiceHours = array_filter($serviceHours, function ($serviceHour) {
        return $serviceHour["serviceHourType"] == "Exceptional";
      });


      $result .= buildServiceHoursHtml($normalServiceHours);
      $result .= buildServiceHoursHtml($exceptionalServiceHours);
    }

    return $result;
  }

  /**
   * Builds service hours html
   * @param object[] $serviceHours service hours
   * @return string service hours html
   */
  function buildServiceHoursHtml($serviceHours) {
    $result = '';

    foreach ($serviceHours as $serviceHour) {
      $additionalInformation = getLocalizedValue($serviceHour["additionalInformation"], $data->language);
      $openingHours = $serviceHour["openingHour"];
      $result .= "<strong>$additionalInformation</strong>";
        
      $result .= "<p>";

      if ($serviceHour["serviceHourType"] == "Exceptional") {
        $splitDate = explode("-",$serviceHour["validFrom"]);
        $year = $splitDate[0];
        $month = $splitDate[1];
        $day = explode("T", $splitDate[2])[0];
        $result .= $day . "." . $month . "." . $year;
        $result .= "</br>";
      }

      if (!$serviceHour["isClosed"] && count($openingHours) == 0) {
        $result .= __("Open 24 hours.", "sptv");
      } else if ($serviceHour["isClosed"]) {
        $result .= __("Closed", "sptv");
      } else {
        $formattedHours = formatOpeningHours($openingHours);
        foreach($formattedHours as $formattedHour) {
          $result .= $formattedHour;
          $result .= "</br>";
        }
      }
      $result .= "</p>";
    }

    return $result;
  }

  /**
   * Formats opening hour object.
   * 
   * @param object $openingHour openingHour
   * @return string formatted object
   */
  function formatOpeningHour($openingHour) {
    $days = isset($openingHour['dayFrom']) ? formatDayName(getLocalizedDayName($openingHour['dayFrom'])) : '';
    $from = "";
    $to = "";
    
    if (!empty($openingHour['dayTo'])) {
      $days .= ' - ' . formatDayName(getLocalizedDayName($openingHour['dayTo']));
    }
    
    if (isset($openingHour['from'])) {
      $from = implode('.', array_slice(explode(':', $openingHour['from']), 0, 2));
    }
    
    if (isset($openingHour['to'])) {
      $to = implode('.', array_slice(explode(':', $openingHour['to']), 0, 2));
    }
    
    if (!empty($from) || !empty($to)) {
      return "${days} ${from} - ${to}";
    } else {
      return "${days} ${from}";
    }
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
   * Formats list of opening hours
   * 
   * @param object[] $openingHours openingHours
   * @return string[] formatted hours
   */
  function formatOpeningHours($openingHours) {
    return array_map(function ($openingHour) {
      return formatOpeningHour($openingHour);
    }, $openingHours);
  }

?>