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
        return __("fi", "sptv");
      case "en":
        return __("en", "sptv");
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
      foreach ($serviceHours as $serviceHour) {
        if ($serviceHour["serviceHourType"] == "DaysOfTheWeek") {
          $additionalInformation = getLocalizedValue($serviceHour["additionalInformation"], $data->language);
          $openingHours = $serviceHour["openingHour"];
          $result .= "<p>$additionalInformation</p>";
          
          $result .= "<p>";
          if (!$serviceHour["isClosed"] && count($openingHours) == 0) {
            $result .= __("Open 24 hours.", "sptv");
          } else {
            $result .= formatOpeningHours($openingHours);
          }
          $result .= "</p>";

        }
      }
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
    $days = isset($openingHour['dayFrom']) ? getLocalizedDayName($openingHour['dayFrom']) : '';
    $from = "";
    $to = "";
    
    if (isset($openingHour['dayTo'])) {
      $days .= ' - ' . getLocalizedDayName($openingHour['dayTo']);
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
   * Formats list of opening hours
   * 
   * @param object[] $openingHours openingHours
   * @return string formatted string
   */
  function formatOpeningHours($openingHours) {
    return implode(", ", array_map(function ($openingHour) {
      return formatOpeningHour($openingHour);
    }, $openingHours));
  }

?>